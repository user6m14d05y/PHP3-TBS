<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    /**
     * Huy don hang: hoan ton kho, nha voucher, danh dau cancelled.
     * Phai duoc goi ben trong DB::transaction cua caller.
     */
    public function cancel(Order $order, CouponService $couponService): void
    {
        $order = Order::query()
            ->whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($order->status === 'cancelled') {
            return;
        }

        if ($order->payment_status === 'paid' || $order->status === 'completed') {
            throw ValidationException::withMessages([
                'order' => 'Don hang da thanh toan hoac hoan tat, khong the huy.',
            ]);
        }

        $order->items()->get()->each(function ($item) {
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

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);
    }
}
