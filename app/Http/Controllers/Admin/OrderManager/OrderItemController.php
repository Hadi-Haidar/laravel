<?php

namespace App\Http\Controllers\Admin\OrderManager;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the order items.
     *
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($orderId)
    {
        $orderItems = OrderItem::with('product')
                        ->where('order_id', $orderId)
                        ->get();
        
        return response()->json(['orderItems' => $orderItems]);
    }

    /**
     * Display the specified order item.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $orderItem = OrderItem::with(['product', 'order'])->findOrFail($id);
        
        return response()->json(['orderItem' => $orderItem]);
    }

    /**
     * Store a newly created order item in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        
        $totalPrice = $validated['quantity'] * $validated['price'];
        
        $orderItem = new OrderItem([
            'order_id' => $orderId,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'total_price' => $totalPrice
        ]);
        
        $orderItem->save();
        
        // Update order total
        $this->updateOrderTotal($order);
        
        return response()->json([
            'message' => 'Order item created successfully',
            'orderItem' => $orderItem
        ], 201);
    }

    /**
     * Update the specified order item in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        
        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);
        
        if (isset($validated['quantity']) || isset($validated['price'])) {
            $price = $validated['price'] ?? $orderItem->price;
            $quantity = $validated['quantity'] ?? $orderItem->quantity;
            $validated['total_price'] = $price * $quantity;
        }
        
        $orderItem->update($validated);
        
        // Update order total
        $order = $orderItem->order;
        $this->updateOrderTotal($order);
        
        return response()->json([
            'message' => 'Order item updated successfully',
            'orderItem' => $orderItem->fresh()
        ]);
    }

    /**
     * Remove the specified order item from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $order = $orderItem->order;
        
        $orderItem->delete();
        
        // Update order total
        $this->updateOrderTotal($order);
        
        return response()->json([
            'message' => 'Order item deleted successfully'
        ]);
    }
    
    /**
     * Update the total amount of the order.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    private function updateOrderTotal($order)
    {
        $total = $order->items()->sum('total_price');
        $order->update(['total_amount' => $total]);
    }
} 