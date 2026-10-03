<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CouponService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:pending,awaiting_payment,paid,processing,shipping,completed,cancelled'],
        ]);

        $query = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['items', 'shop', 'coupon']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest('id')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $orders->items(),
            'total' => $orders->total(),
            'per_page' => $orders->perPage(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $this->assertOwnOrder($request, $order);

        return response()->json([
            'status' => 'success',
            'data' => $order->load(['items', 'shop', 'coupon', 'address']),
        ]);
    }

    public function cancel(Request $request, Order $order, CouponService $couponService, OrderStatusService $orderStatusService)
    {
        $this->assertOwnOrder($request, $order);

        DB::transaction(function () use ($order, $couponService, $orderStatusService) {
            $orderStatusService->cancel($order, $couponService);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Da huy don hang.',
            'data' => $order->refresh()->load(['items', 'shop', 'coupon']),
        ]);
    }

    private function assertOwnOrder(Request $request, Order $order): void
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }
}
