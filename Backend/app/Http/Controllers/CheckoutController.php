<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\UserAddress;
use App\Services\CouponService;
use App\Services\GeoDistanceService;
use App\Services\OrderCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(
        Request $request,
        CouponService $couponService,
        GeoDistanceService $geoDistance,
        OrderCodeService $orderCodeService
    ) {
        $validated = $request->validate([
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'user_address_id' => ['required', 'integer', 'exists:user_addresses,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.productVariant.product', 'items.productVariant.size'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Gio hang dang trong.',
            ]);
        }

        $address = UserAddress::query()
            ->where('user_id', $user->id)
            ->findOrFail($validated['user_address_id']);

        if ($address->latitude === null || $address->longitude === null) {
            throw ValidationException::withMessages([
                'user_address_id' => 'Dia chi giao hang can co toa do GPS de tinh khoang cach.',
            ]);
        }

        $shop = Shop::query()
            ->where('is_active', true)
            ->findOrFail($validated['shop_id']);

        $distanceKm = $geoDistance->distanceInKm(
            (float) $shop->latitude,
            (float) $shop->longitude,
            (float) $address->latitude,
            (float) $address->longitude
        );

        if ($distanceKm > (float) $shop->delivery_radius_km) {
            throw ValidationException::withMessages([
                'user_address_id' => 'Dia chi giao hang qua xa, hien tai chi ho tro trong ban kinh ' . (float) $shop->delivery_radius_km . 'km.',
            ]);
        }

        return DB::transaction(function () use (
            $validated,
            $user,
            $cart,
            $address,
            $shop,
            $distanceKm,
            $couponService,
            $orderCodeService
        ) {
            $items = $this->freshCartItems($cart);
            $subtotal = round($items->sum('line_total'), 2);
            $discountAmount = 0;
            $coupon = null;

            if (!empty($validated['coupon_code'])) {
                $couponResult = $couponService->validateForUser($validated['coupon_code'], $user, $subtotal, $items);
                $coupon = $couponResult['coupon'];

                $coupon = Coupon::query()
                    ->whereKey($coupon->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $couponResult = $couponService->validateForUser($coupon->code, $user, $subtotal, $items);
                $discountAmount = $couponResult['discount_amount'];
            }

            $shippingFee = $this->calculateShippingFee($distanceKm);
            $totalAmount = round(max($subtotal - $discountAmount, 0) + $shippingFee, 2);

            $order = Order::create([
                'order_code' => $orderCodeService->generate(),
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'user_address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->phone,
                'recipient_email' => $address->email,
                'shipping_address' => $this->shippingAddress($address),
                'shipping_latitude' => $address->latitude,
                'shipping_longitude' => $address->longitude,
                'delivery_distance_km' => $distanceKm,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'status' => 'awaiting_payment',
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_variant_id' => $item['variant']->id,
                    'product_name' => $item['variant']->product->name,
                    'variant_name' => $item['variant']->size?->name,
                    'sku' => $item['variant']->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            }

            if ($coupon) {
                $couponService->reserve($coupon, $user, $order->id, $discountAmount, $subtotal, $items);
            }

            $cart->items()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Da tao don hang, cho thanh toan.',
                'data' => $order->load(['items', 'shop', 'coupon']),
            ], 201);
        });
    }

    public function paymentSucceeded(Order $order, CouponService $couponService)
    {
        $this->assertAdmin(request());

        DB::transaction(function () use ($order, $couponService) {
            $order->refresh();

            if ($order->payment_status === 'paid') {
                return;
            }

            if ($order->payment_status === 'failed' || $order->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'order' => 'Don hang da bi huy, khong the xac nhan thanh toan.',
                ]);
            }

            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
            ]);

            $usage = $order->coupon
                ? $order->coupon->usages()->where('order_id', $order->id)->where('status', 'reserved')->first()
                : null;

            if ($usage) {
                $couponService->markUsed($usage);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da xac nhan thanh toan thanh cong.',
            'data' => $order->refresh(),
        ]);
    }

    public function paymentFailed(Order $order, CouponService $couponService)
    {
        $this->assertAdmin(request());

        DB::transaction(function () use ($order, $couponService) {
            $order->refresh();

            if ($order->payment_status === 'paid') {
                throw ValidationException::withMessages([
                    'order' => 'Don hang da thanh toan, khong the danh dau that bai.',
                ]);
            }

            if ($order->payment_status === 'failed' || $order->status === 'cancelled') {
                return;
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

            $order->items()->each(function ($item) {
                ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->increment('stock', $item->quantity);
            });

            $usage = $order->coupon
                ? $order->coupon->usages()->where('order_id', $order->id)->where('status', 'reserved')->first()
                : null;

            if ($usage) {
                $couponService->release($usage);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da huy don va hoan luot voucher neu co.',
            'data' => $order->refresh(),
        ]);
    }

    private function freshCartItems(Cart $cart)
    {
        return $cart->items->map(function ($item) {
            $variant = ProductVariant::query()
                ->with(['product', 'size'])
                ->whereKey($item->product_variant_id)
                ->where('is_active', true)
                ->whereHas('product', fn ($query) => $query->where('is_active', true))
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                throw ValidationException::withMessages([
                    'cart' => 'Gio hang co san pham khong hop le hoac da bi an.',
                ]);
            }

            if ($variant->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'San pham ' . $variant->product->name . ' khong du ton kho.',
                ]);
            }

            $unitPrice = (float) ($variant->sale_price ?: $variant->price);

            $variant->decrement('stock', $item->quantity);

            return [
                'variant' => $variant,
                'quantity' => (int) $item->quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * (int) $item->quantity, 2),
            ];
        });
    }

    private function shippingAddress(UserAddress $address): string
    {
        return collect([
            $address->formatted_address ?: $address->address_line,
            $address->ward,
            $address->district,
            $address->city,
        ])->filter()->implode(', ');
    }

    private function calculateShippingFee(float $distanceKm): float
    {
        $baseFee = 15000;
        $feePerKm = 5000;

        return round($baseFee + (ceil($distanceKm) * $feePerKm), 2);
    }

    private function assertAdmin(Request $request): void
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Chi admin hoac webhook thanh toan da xac thuc moi duoc cap nhat trang thai thanh toan.');
        }
    }
}
