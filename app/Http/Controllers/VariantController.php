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
                'images' => 'required|array',
                'ar_file' => 'nullable|string',
                'availability' => 'required|in:local,pre-order,comingsoon',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $imagePaths = [];

            if (isset($request['images'])) {
                foreach ($request['images'] as $image) {
                    $path = $image->store('variants/images', 'public');
                    $imagePaths[] = $path;
                }
            }

            $arFilePath = null;
            if ($request->hasFile('ar_file')) {
                $arFilePath = $request['ar_file']->store('variants/ar', 'public');
            }

            $data = $validator->validated();
            $variant = Variant::create(array_merge(

                [
                    'id' => Str::uuid(),
                    'product_id' => $data['product_id'],
                    'name' => $data['name'],
                    'color' => $data['color'],
                    'size' => $data['size'],
                    'price' => $data['price'],
                    'stock_quantity' => $data['stock_quantity'],
                    'images' => $imagePaths,
                    'ar_file' => $arFilePath,
                    'availability' => $data['availability'],
                ]

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
            $imageHost = env('API_IMAGE_HOST');

            if (is_array($variant->images)) {
                $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                    return is_string($imagePath) ? $imageHost . $imagePath : null;
                }, $variant->images)));
            }

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found',
                'error' => $e->getMessage()
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
                'ar_file' => 'nullable|file|string',
                'availability' => 'sometimes|required|in:local,pre-order,comingsoon',
                'status' => 'sometimes|required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Merge new images with existing ones
            if ($request->has('images') && is_array($request->images)) {
                $newImagePaths = [];

                foreach ($request->images as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $path = $image->store('variants/images', 'public');
                        $newImagePaths[] = $path;
                    }
                }
                $existingImages = is_array($variant->images) ? $variant->images : [];
                $data['images'] = array_values(array_merge($existingImages, $newImagePaths));
            }

            // Handle AR file upload
            if ($request->hasFile('ar_file')) {
                $arFilePath = $request->file('ar_file')->store('variants/ar', 'public');
                $data['ar_file'] = $arFilePath;
            }

            $variant->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Successfully Saved Changes',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:available,unavailable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $variant = Variant::findOrFail($id);
            $variant->update(['status' => $request->input('status')]);

            return response()->json([
                'success' => true,
                'message' => 'Variant status updated successfully',
                'data' => $variant,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Variant status change failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
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
