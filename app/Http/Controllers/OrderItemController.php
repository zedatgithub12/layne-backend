<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Exception;

class OrderItemController extends Controller
{

    public function index()
    {
        try {
            $orderItems = OrderItem::all();

            return response()->json([
                'success' => true,
                'message' => 'Order items retrieved successfully',
                'data' => $orderItems
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|uuid|exists:orders,id',
            'frame_id' => 'required|uuid|exists:frames,id',
            'frame_name' => 'required|string|max:255',
            'lens_id' => 'required|uuid|exists:lenses,id',
            'color_id' => 'required|uuid|exists:colors,id',
            'size_id' => 'required|uuid|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $validated['subtotal'] = $validated['quantity'] * $validated['unit_price'];

            // $existingOrderItem = OrderItem::where('order_id', $request->order_id)
            //     ->where('frame_id', $request->frame_id)
            //     ->first();

            // if ($existingOrderItem) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'The order item record is already created',
            //     ], 422);
            // }

            $orderItem = OrderItem::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order item created successfully',
                'data' => $orderItem,
            ], 201);

        } catch (Exception $e) {
            // Log the exception for debugging
            \Log::error('Error creating order item: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'frame_name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $orderItem->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order item updated successfully',
                'data' => $orderItem
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(OrderItem $orderItem)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order item retrieved successfully',
            'data' => $orderItem
        ], 200);
    }

    public function destroy(OrderItem $orderItem)
    {
        try {
            $orderItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order item deleted successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
