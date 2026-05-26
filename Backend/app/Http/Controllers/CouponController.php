<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ProductVariant;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    public function index()
    {
        $this->assertAdmin(request());

        $query = Coupon::query();

        // Search by code or name
        if (request()->filled('search')) {
            $search = trim(request()->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by discount type
        if (request()->filled('discount_type')) {
            $query->where('discount_type', request()->input('discount_type'));
        }

        // Filter by status (active, inactive, expired)
        if (request()->filled('status')) {
            $status = request()->input('status');
            $now = now();
            if ($status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) use ($now) {
                          $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                      })
                      ->where(function ($q) use ($now) {
                          $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                      });
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'expired') {
                $query->where(function ($q) use ($now) {
                    $q->where('is_active', false)
                      ->orWhere(function ($sub) use ($now) {
                          $sub->whereNotNull('expires_at')->where('expires_at', '<', $now);
                      });
                });
            }
        }

        if (request()->boolean('all')) {
            $coupons = $query->latest('id')->get();
            return response()->json([
                'status' => 'success',
                'data' => $coupons,
            ]);
        }

        $coupons = $query->latest('id')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $coupons->items(),
            'total' => $coupons->total(),
            'per_page' => $coupons->perPage(),
            'current_page' => $coupons->currentPage(),
            'last_page' => $coupons->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $validated = $this->validateCoupon($request);
        $this->assertDiscountValue($validated);
        $validated['code'] = Str::upper(trim($validated['code']));

        $coupon = Coupon::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Da tao voucher.',
            'data' => $coupon,
        ], 201);
    }

    public function show(Coupon $coupon)
    {
        $this->assertAdmin(request());

        return response()->json([
            'status' => 'success',
            'data' => $coupon,
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->assertAdmin($request);

        $validated = $this->validateCoupon($request, $coupon->id, true);
        $this->assertDiscountValue($validated, $coupon);

        if (isset($validated['code'])) {
            $validated['code'] = Str::upper(trim($validated['code']));
        }

        $coupon->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Da cap nhat voucher.',
            'data' => $coupon->refresh(),
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $this->assertAdmin(request());

        $coupon->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Da xoa voucher.',
        ]);
    }

    public function apply(Request $request, CouponService $couponService)
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with('items.productVariant.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Gio hang dang trong.',
            ]);
        }

        $items = $cart ? $this->currentCartItems($cart) : collect();
        $subtotal = round($items->sum('line_total'), 2);

        $result = $couponService->validateForUser($validated['coupon_code'], $request->user(), $subtotal, $items);

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher hop le.',
            'data' => [
                'coupon' => $result['coupon'],
                'subtotal' => round($subtotal, 2),
                'discount_amount' => $result['discount_amount'],
                'total_after_discount' => round(max($subtotal - $result['discount_amount'], 0), 2),
            ],
        ]);
    }

    private function currentCartItems(Cart $cart)
    {
        return $cart->items->map(function ($item) {
            $variant = ProductVariant::query()
                ->with('product')
                ->whereKey($item->product_variant_id)
                ->where('is_active', true)
                ->whereHas('product', fn ($query) => $query->where('is_active', true))
                ->first();

            if (!$variant) {
                throw ValidationException::withMessages([
                    'cart' => 'Gio hang co san pham khong hop le hoac da bi an.',
                ]);
            }

            if ($variant->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'San pham trong gio khong du ton kho.',
                ]);
            }

            $unitPrice = (float) ($variant->sale_price ?: $variant->price);

            return [
                'variant' => $variant,
                'quantity' => (int) $item->quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * (int) $item->quantity, 2),
            ];
        });
    }

    private function validateCoupon(Request $request, ?int $ignoreId = null, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';
        $uniqueCode = 'unique:coupons,code';

        if ($ignoreId) {
            $uniqueCode .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => [$required, 'string', 'max:50', $uniqueCode],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => [$required, 'in:percentage,fixed_amount'],
            'discount_value' => [$required, 'numeric', 'min:0.01'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'conditions' => ['nullable', 'array'],
            'conditions.min_quantity' => ['nullable', 'integer', 'min:1'],
            'conditions.product_variant_ids' => ['nullable', 'array'],
            'conditions.product_variant_ids.*' => ['integer', 'exists:product_variants,id'],
            'conditions.product_ids' => ['nullable', 'array'],
            'conditions.product_ids.*' => ['integer', 'exists:products,id'],
            'conditions.category_ids' => ['nullable', 'array'],
            'conditions.category_ids.*' => ['integer', 'exists:category,id'],
            'conditions.category_item_ids' => ['nullable', 'array'],
            'conditions.category_item_ids.*' => ['integer', 'exists:category_item,id'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function assertAdmin(Request $request): void
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Chi admin moi duoc thuc hien thao tac nay.');
        }
    }

    private function assertDiscountValue(array $validated, ?Coupon $coupon = null): void
    {
        $discountType = $validated['discount_type'] ?? $coupon?->discount_type;
        $discountValue = $validated['discount_value'] ?? $coupon?->discount_value;

        if ($discountType === 'percentage' && (float) $discountValue > 100) {
            throw ValidationException::withMessages([
                'discount_value' => 'Voucher phan tram khong duoc lon hon 100.',
            ]);
        }
    }
}
