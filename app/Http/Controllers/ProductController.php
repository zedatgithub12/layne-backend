<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Str;
use Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'categories' => 'required|array',
                'shape' => 'required|string',
                'lens_types' => 'required|array',
                'description' => 'required|string',
                'product_weight' => 'required|string',
                'sku' => 'required|string',
                'material' => 'required|string',
                'pd_range' => 'required|string',
                'rx_range' => 'required|string',
                'spring_hinge' => 'required|string',
                'bridge_fit' => 'required|string',
                'adjustable_nose_pad' => 'nullable|string',
                'is_flexible' => 'nullable|string',
                'need_prescription' => 'nullable|string',
                'tags' => 'required|array',

                'variants' => 'required|array|min:1',
                'variants.*.name' => 'required|string',
                'variants.*.color' => 'required|string',
                'variants.*.size' => 'required|string',
                'variants.*.price' => 'required|numeric',
                'variants.*.stock_quantity' => 'required|integer',
                'variants.*.images' => 'nullable|array',
                'variants.*.ar_file' => 'nullable|string',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Create product
            $product = Product::create([
                'id' => Str::uuid(),
                'name' => $data['name'],
                'categories' => $data['categories'],
                'shape' => $data['shape'],
                'lens_types' => $data['lens_types'],
                'description' => $data['description'],
                'product_weight' => $data['product_weight'],
                'sku' => $data['sku'],
                'material' => $data['material'],
                'pd_range' => $data['pd_range'],
                'rx_range' => $data['rx_range'],
                'spring_hinge' => $data['spring_hinge'],
                'bridge_fit' => $data['bridge_fit'],
                'adjustable_nose_pad' => $data['adjustable_nose_pad'],
                'is_flexible' => $data['is_flexible'],
                'need_prescription' => $data['need_prescription'],
                'tags' => $data['tags'],
                'status' => 'available',
            ]);

            // Create associated variants
            $variants = [];

            foreach ($request->variants as $index => $variantInput) {
                $imagePaths = [];

                if (isset($variantInput['images'])) {
                    foreach ($variantInput['images'] as $image) {
                        $path = $image->store('variants/images', 'public');
                        $imagePaths[] = $path;
                    }
                }

                $arFilePath = null;
                if (isset($variantInput['ar_file'])) {
                    $arFilePath = $variantInput['ar_file']->store('variants/ar', 'public');
                }

                $variant = Variant::create([
                    'id' => Str::uuid(),
                    'product_id' => $product->id,
                    'name' => $variantInput['name'],
                    'color' => $variantInput['color'],
                    'size' => $variantInput['size'],
                    'price' => $variantInput['price'],
                    'stock_quantity' => $variantInput['stock_quantity'],
                    'images' => $imagePaths,
                    'ar_file' => $arFilePath,
                    'availability' => $variantInput['availability'],
                    'status' => 'available',
                ]);

                $variants[] = $variant;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => [
                    'product' => $product,
                    'variants' => $variants,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            $query = Product::with('variants');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            }

            if ($request->filled('status')) {
                $status = $request->query('status');
                $query->where('status', $status);
            }

            $limit = $request->input('limit', 10);
            $products = $query->paginate($limit);

            $imageHost = env('API_IMAGE_HOST');

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                    $images = is_array($decodedImages) ? $decodedImages : [];
                    $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                        return is_string($imagePath) ? $imageHost . $imagePath : null;
                    }, $images)));
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPopularProducts(Request $request)
    {
        try {
            $query = Product::with('variants');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            }

            if ($request->filled('status')) {
                $status = $request->query('status');
                $query->where('status', $status);
            }

            $limit = $request->input('limit', 10);
            $products = $query->paginate($limit);

            $imageHost = env('API_IMAGE_HOST');

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                    $images = is_array($decodedImages) ? $decodedImages : [];
                    $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                        return is_string($imagePath) ? $imageHost . $imagePath : null;
                    }, $images)));
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getGeeksProducts(Request $request)
    {
        try {
            $query = Product::with('variants');
            $limit = $request->input('limit', 10);
            $products = $query->paginate($limit);

            $imageHost = env('API_IMAGE_HOST');

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                    $images = is_array($decodedImages) ? $decodedImages : [];
                    $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                        return is_string($imagePath) ? $imageHost . $imagePath : null;
                    }, $images)));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('variants')->findOrFail($id);
            $lensTypeIds = $product->lens_types;
            $lensTypes = DB::table('lens_types')->whereIn('id', $lensTypeIds)->get();

            $imageHost = env('API_IMAGE_HOST');

            foreach ($lensTypes as $lens) {
                $lens->thumbnail = $imageHost . $lens->thumbnail;
            }

            $product->lens_types = $lensTypes;

            foreach ($product->variants as $variant) {
                $decodedImages = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                $images = is_array($decodedImages) ? $decodedImages : [];
                $variant->images = array_values(array_filter(array_map(function ($imagePath) use ($imageHost) {
                    return is_string($imagePath) ? $imageHost . $imagePath : null;
                }, $images)));
            }



            return response()->json([
                'success' => true,
                'message' => 'successfully done',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string',
                'categories' => 'sometimes|required|array',
                'shape' => 'sometimes|required|string',
                'lens_types' => 'sometimes|required|array',
                'description' => 'sometimes|required|string',
                'product_weight' => 'sometimes|required|string',
                'sku' => 'sometimes|required|string',
                'material' => 'sometimes|required|string',
                'pd_range' => 'sometimes|required|string',
                'rx_range' => 'sometimes|required|string',
                'spring_hinge' => 'sometimes|required|string',
                'adjustable_nose_pad' => 'nullable|string',
                'is_flexible' => 'nullable|string',
                'need_prescription' => 'nullable|string',
                'tags' => 'sometimes|required|array',
                'status' => 'nullable|in:available,unavailable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully Updated Product',
                'data' => $product,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
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

            $product = Product::findOrFail($id);
            $product->update(['status' => $request->input('status')]);

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully',
                'data' => $product,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Product status change failed: ' . $e->getMessage());

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
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'successfully deleted',
                'data' => null,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Product deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }


}
