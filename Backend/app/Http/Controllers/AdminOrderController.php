<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CouponService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    private const STATUS_TRANSITIONS = [
        'processing' => ['awaiting_payment', 'paid'],
        'shipping' => ['processing'],
        'completed' => ['shipping'],
    ];

    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:pending,awaiting_payment,paid,processing,shipping,completed,cancelled'],
            'payment_status' => ['nullable', 'string', 'in:unpaid,pending,paid,failed,refunded'],
        ]);

        $query = Order::query()
            ->with(['items', 'shop', 'user:id,name,email', 'coupon']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%");
            });
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

    public function show(Order $order)
    {
        return response()->json([
            'status' => 'success',
            'data' => $order->load(['items', 'shop', 'user:id,name,email', 'coupon', 'payments', 'shipments']),
        ]);
    }

    public function updateStatus(Request $request, Order $order, CouponService $couponService, OrderStatusService $orderStatusService)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:processing,shipping,completed,cancelled'],
        ]);

        $target = $validated['status'];

        if ($target === 'cancelled') {
            DB::transaction(function () use ($order, $couponService, $orderStatusService) {
                $orderStatusService->cancel($order, $couponService);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Da huy don hang.',
                'data' => $order->refresh(),
            ]);
        }

        $allowedFrom = self::STATUS_TRANSITIONS[$target] ?? [];

        if (!in_array($order->status, $allowedFrom, true)) {
            throw ValidationException::withMessages([
                'status' => 'Khong the chuyen don hang tu trang thai ' . $order->status . ' sang ' . $target . '.',
            ]);
        }

        $order->update(['status' => $target]);

        return response()->json([
            'status' => 'success',
            'message' => 'Da cap nhat trang thai don hang.',
            'data' => $order->refresh(),
        ]);
    }
}
