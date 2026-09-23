<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function validateForUser(string $code, User $user, float $subtotal, ?iterable $items = null): array
    {
        $coupon = Coupon::query()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (!$coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher khong ton tai.',
            ]);
        }

        $this->validateCouponState($coupon, $user, $subtotal, null, $items);

        return [
            'coupon' => $coupon,
            'discount_amount' => $this->calculateDiscount($coupon, $subtotal),
        ];
    }

    public function reserve(Coupon $coupon, User $user, int $orderId, float $discountAmount, float $subtotal, ?iterable $items = null): CouponUsage
    {
        $coupon->refresh();
        $this->validateCouponState($coupon, $user, $subtotal, $orderId, $items);

        $coupon->increment('used_count');

        return CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'status' => 'reserved',
            'reserved_at' => now(),
        ]);
    }

    public function markUsed(CouponUsage $usage): void
    {
        $usage->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function release(CouponUsage $usage): void
    {
        if ($usage->status !== 'reserved') {
            return;
        }

        Coupon::query()
            ->whereKey($usage->coupon_id)
            ->where('used_count', '>', 0)
            ->decrement('used_count');

        $usage->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
    }

    private function validateCouponState(Coupon $coupon, User $user, float $subtotal, ?int $ignoreOrderId = null, ?iterable $items = null): void
    {
        $now = Carbon::now();

        if (!$coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher dang bi tat.',
            ]);
        }

        if ($coupon->starts_at && $coupon->starts_at->gt($now)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher chua den ngay su dung.',
            ]);
        }

        if ($coupon->expires_at && $coupon->expires_at->lt($now)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher da het han.',
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher da het luot su dung.',
            ]);
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Don hang chua dat gia tri toi thieu de dung voucher.',
            ]);
        }

        $usedByUser = CouponUsage::query()
            ->where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['reserved', 'used'])
            ->when($ignoreOrderId, fn ($query) => $query->where('order_id', '!=', $ignoreOrderId))
            ->count();

        if ($usedByUser >= $coupon->per_user_limit) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Tai khoan nay da su dung voucher qua so lan cho phep.',
            ]);
        }

        $this->validateConditions($coupon, $items);
    }

    private function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->discount_type === 'percentage') {
            $discount = $subtotal * ((float) $coupon->discount_value / 100);
        } else {
            $discount = (float) $coupon->discount_value;
        }

        if ($coupon->max_discount_amount !== null) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        return round(min($discount, $subtotal), 2);
    }

    private function validateConditions(Coupon $coupon, ?iterable $items): void
    {
        $conditions = $coupon->conditions ?: [];

        if ($conditions === []) {
            return;
        }

        $items = collect($items ?? []);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher can thong tin san pham trong gio hang de kiem tra dieu kien.',
            ]);
        }

        $totalQuantity = $items->sum(fn ($item) => (int) $this->itemValue($item, 'quantity'));

        if (($conditions['min_quantity'] ?? null) && $totalQuantity < (int) $conditions['min_quantity']) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Gio hang chua dat so luong toi thieu de dung voucher.',
            ]);
        }

        $this->assertAnyMatch($items, $conditions, 'product_variant_ids', fn ($item) => $this->variantValue($item, 'id'));
        $this->assertAnyMatch($items, $conditions, 'product_ids', fn ($item) => $this->variantValue($item, 'product_id'));
        $this->assertAnyMatch($items, $conditions, 'category_ids', fn ($item) => $this->productValue($item, 'category_id'));
        $this->assertAnyMatch($items, $conditions, 'category_item_ids', fn ($item) => $this->productValue($item, 'category_item_id'));
    }

    private function assertAnyMatch(Collection $items, array $conditions, string $key, callable $valueResolver): void
    {
        $allowedValues = collect($conditions[$key] ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->values();

        if ($allowedValues->isEmpty()) {
            return;
        }

        $hasMatch = $items->contains(function ($item) use ($allowedValues, $valueResolver) {
            $value = $valueResolver($item);

            return $value !== null && $allowedValues->contains((int) $value);
        });

        if (!$hasMatch) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Voucher khong ap dung cho san pham trong gio hang.',
            ]);
        }
    }

    private function itemValue($item, string $key)
    {
        return is_array($item) ? ($item[$key] ?? null) : data_get($item, $key);
    }

    private function variantValue($item, string $key)
    {
        $variant = $this->itemValue($item, 'variant');

        return $variant ? data_get($variant, $key) : null;
    }

    private function productValue($item, string $key)
    {
        $variant = $this->itemValue($item, 'variant');
        $product = $variant ? data_get($variant, 'product') : null;

        return $product ? data_get($product, $key) : null;
    }
}
