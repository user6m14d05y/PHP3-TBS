<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Cart;
class CheckCart extends Command {
    protected $signature = 'check:cart';
    public function handle() {
        $carts = Cart::with(['items.productVariant.product', 'items.productVariant.size'])->get();
        foreach($carts as $cart) {
            $this->info("Cart ID: {$cart->id}, User ID: {$cart->user_id}");
            foreach($cart->items as $item) {
                $variant = $item->productVariant;
                $thumb = $variant?->product?->thumbnail;
                $this->line("  - Item: {$variant?->product?->name}, Thumb: " . var_export($thumb, true));
            }
        }
    }
}
