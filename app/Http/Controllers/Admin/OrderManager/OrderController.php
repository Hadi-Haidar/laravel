<?php

namespace App\Http\Controllers\Admin\OrderManager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::with(['user', 'items.product'])->latest()->get();
        
        return response()->json(['orders' => $orders]);
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        
        return response()->json(['order' => $order]);
    }

    /**
     * Update the specified order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            ],
        ]);
        
        $order->update(['status' => $validated['status']]);
        
        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->fresh()
        ]);
    }

    /**
     * Get orders by status.
     *
     * @param  string  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByStatus($status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status',
            ], 422);
        }
        
        $orders = Order::with(['user', 'items.product'])
            ->where('status', $status)
            ->latest()
            ->get();
        
        return response()->json(['orders' => $orders]);
    }

    /**
     * Get order statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $total = Order::count();
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $delivered = Order::where('status', 'delivered')->count();
        $cancelled = Order::where('status', 'cancelled')->count();
        
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        
        return response()->json([
            'statistics' => [
                'total_orders' => $total,
                'pending_orders' => $pending,
                'processing_orders' => $processing,
                'shipped_orders' => $shipped,
                'delivered_orders' => $delivered,
                'cancelled_orders' => $cancelled,
                'total_revenue' => $totalRevenue,
            ]
        ]);
    }
}
