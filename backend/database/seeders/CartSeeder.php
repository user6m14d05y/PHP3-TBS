<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'test@example.com')->first();

        if (!$user) {
            return;
        }

        $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

        $variants = ProductVariant::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->limit(3)
            ->get();

        foreach ($variants as $index => $variant) {
            CartItem::query()->updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variant->id,
                ],
                [
                    'quantity' => $index + 1,
                    'unit_price' => $variant->sale_price ?: $variant->price,
                    'note' => $index === 0 ? 'Seed demo cart item' : null,
                ]
            );
        }
    }
}
