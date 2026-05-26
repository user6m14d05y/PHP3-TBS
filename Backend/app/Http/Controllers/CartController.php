<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->cartForUser($request);

        return response()->json([
            'status' => 'success',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = DB::transaction(function () use ($request, $validated) {
            $quantity = (int) ($validated['quantity'] ?? 1);
            $variant = $this->activeVariant((int) $validated['product_variant_id']);
            $cart = $this->cartForUser($request);

            $item = $cart->items()
                ->where('product_variant_id', $variant->id)
                ->first();

            $newQuantity = $quantity + (int) ($item?->quantity ?? 0);

            if ($variant->stock < $newQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'So luong san pham trong kho khong du.',
                ]);
            }

            $cart->items()->updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'quantity' => $newQuantity,
                    'unit_price' => $this->currentPrice($variant),
                    'note' => $validated['note'] ?? $item?->note,
                ]
            );

            return $cart;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da them san pham vao gio hang.',
            'data' => $this->cartPayload($cart),
        ], 201);
    }

    public function update(Request $request, CartItem $item)
    {
        $this->assertOwnCartItem($request, $item);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = DB::transaction(function () use ($item, $validated) {
            $variant = $this->activeVariant($item->product_variant_id);

            if ($variant->stock < (int) $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'So luong san pham trong kho khong du.',
                ]);
            }

            $item->update([
                'quantity' => (int) $validated['quantity'],
                'unit_price' => $this->currentPrice($variant),
                'note' => $validated['note'] ?? $item->note,
            ]);

            return $item->cart;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da cap nhat gio hang.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function destroy(Request $request, CartItem $item)
    {
        $this->assertOwnCartItem($request, $item);

        $cart = $item->cart;
        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Da xoa san pham khoi gio hang.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function clear(Request $request)
    {
        $cart = $this->cartForUser($request);
        $cart->items()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Da xoa gio hang.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    private function cartForUser(Request $request): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);
    }

    private function cartPayload(Cart $cart): array
    {
        $cart->load(['items.productVariant.product', 'items.productVariant.size']);

        $items = $cart->items->map(function (CartItem $item) {
            $variant = $item->productVariant;
            $unitPrice = $variant ? $this->currentPrice($variant) : (float) $item->unit_price;
            $lineTotal = $unitPrice * (int) $item->quantity;

            return [
                'id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'product_id' => $variant?->product_id,
                'product_name' => $variant?->product?->name,
                'size_name' => $variant?->size?->name,
                'quantity' => (int) $item->quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($lineTotal, 2),
                'stock' => $variant?->stock,
                'note' => $item->note,
            ];
        })->values();

        return [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'items' => $items,
            'subtotal' => round($items->sum('line_total'), 2),
        ];
    }

    private function activeVariant(int $variantId): ProductVariant
    {
        $variant = ProductVariant::query()
            ->with(['product', 'size'])
            ->where('id', $variantId)
            ->where('is_active', true)
            ->whereHas('product', fn ($query) => $query->where('is_active', true))
            ->first();

        if (!$variant) {
            throw ValidationException::withMessages([
                'product_variant_id' => 'Bien the san pham khong hop le hoac dang tam an.',
            ]);
        }

        return $variant;
    }

    private function currentPrice(ProductVariant $variant): float
    {
        return (float) ($variant->sale_price ?: $variant->price);
    }

    private function assertOwnCartItem(Request $request, CartItem $item): void
    {
        $item->loadMissing('cart');

        if ((int) $item->cart->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }
}
