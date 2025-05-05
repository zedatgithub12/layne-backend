<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $query = Order::where('user_id', $user_id)->with('user')->with('frame')->with('variant');

        if ($request->filled('status')) {
            $status = $request->query('status');

            if ($status === 'Active') {
                $query->where('status', 'processing');
            } elseif ($status === 'History') {
                $query->whereIn('status', ['completed', 'cancelled']);
            }
        }


        $limit = $request->input('limit', 10);
        $orders = $query->paginate($limit);

        $imageHost = config('app.api_image_host');

        foreach ($orders->items() as $order) {
            $variant = $order->variant;

            if ($variant) {
                $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                $images = is_array($decodedImages) ? $decodedImages : [];
                $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                    return is_string($imagePath) && !str_starts_with($imagePath, $imageHost) ? $imageHost . $imagePath : $imagePath;
                }, $images)));
                $order->variant = $variant;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ], 200);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'frame_id' => 'required|uuid',
            'frame_name' => 'required|string',
            'variant_id' => 'required|uuid',
            'lens' => 'required|string',
            'lens_type' => 'nullable|string',
            'lens_variant_name' => 'nullable|string',
            'lens_variant_value' => 'nullable|string',
            'need_prescription' => 'nullable|string',
            'prescription' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('prescription')) {
            $prescriptionPath = $request->file('prescription')->store('images', 'public');
            $validated['prescription'] = $prescriptionPath;
        }

        try {
            $order = Order::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user_id = Auth::id();
            $order = Order::where('user_id', $user_id)->with('user')->with('frame')->with('variant')->findOrFail($id);

            $imageHost = config('app.api_image_host');

            $variant = $order->variant;
            if ($variant) {
                $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                $images = is_array($decodedImages) ? $decodedImages : [];
                $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                    return is_string($imagePath) && !str_starts_with($imagePath, $imageHost) ? $imageHost . $imagePath : $imagePath;
                }, $images)));
            }

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => $order
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'frame_id' => 'sometimes|uuid',
                'frame_name' => 'sometimes|string',
                'variant_id' => 'sometimes|uuid',
                'lens' => 'sometimes|string',
                'lens_type' => 'nullable|string',
                'lens_variant_name' => 'nullable|string',
                'lens_variant_value' => 'nullable|string',
                'need_prescription' => 'nullable|boolean',
                'prescription' => 'nullable|string',
                'total_price' => 'numeric|min:0',
                'shipping_address' => 'string|max:255',
                'shipping_method' => 'nullable|string|max:255',
                'payment_status' => 'nullable|in:pending,completed',
                'delivery_status' => 'nullable|in:pending,picked,on-delivery,blocked,delivered,cancelled',
                'status' => 'nullable|in:processing,cancelled,completed',
            ]);

            if ($request->hasFile('prescription')) {
                // Delete old prescription if exists
                if ($order->prescription) {
                    Storage::disk('public')->delete($order->prescription);
                }

                // Store new file
                $prescriptionPath = $request->file('prescription')->store('images', 'public');
                $validated['prescription'] = $prescriptionPath;
            }

            $order->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

