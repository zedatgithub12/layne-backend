<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;
use Log;
use Str;
use Validator;

class VariantController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'name' => 'required|string',
                'color' => 'required|string',
                'size' => 'required|string',
                'price' => 'required|numeric',
                'stock_quantity' => 'required|integer',
                'images' => 'nullable|array',
                'ar_file' => 'nullable|string',
                'availability' => 'required|in:in_stock,out_of_stock',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $variant = Variant::create(array_merge(
                $validator->validated(),
                ['id' => Str::uuid()]
            ));

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Variant creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function index()
    {
        try {
            $variants = Variant::with('product')->get();

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $variants,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fetching variants failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $variant = Variant::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $variant = Variant::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string',
                'color' => 'sometimes|required|string',
                'size' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric',
                'stock_quantity' => 'sometimes|required|integer',
                'images' => 'nullable|array',
                'ar_file' => 'nullable|string',
                'availability' => 'sometimes|required|in:in_stock,out_of_stock',
                'status' => 'sometimes|required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $variant->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Variant update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $variant = Variant::findOrFail($id);
            $variant->delete();

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => null,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Variant deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
