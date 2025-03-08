<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Frame;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FrameController extends Controller
{
    // List all frames
    public function index()
    {
        try {
            $frames = Frame::all();

            return response()->json([
                'success' => true,
                'message' => 'Frames retrieved successfully',
                'data' => $frames
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching frames',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new frame
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'try_on_asset' => 'nullable|file|mimes:glb,gltf',
                'slug' => 'required|string|unique:frames,slug',
                'brand' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|uuid|exists:categories,id',
                'weight' => 'required|numeric',
                'gender' => 'required|in:men,women,both',
                'price' => 'required|numeric',
                'discount_price' => 'nullable|numeric',
                'stock_quantity' => 'required|integer',
                'featured' => 'boolean',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'tags' => 'nullable|array',
                'tags.*' => 'string',
                'ratings' => 'nullable|numeric|min:1|max:5',
                'status' => 'required|in:available,out-of-stock,unavailable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 400);
            }

            $data = $request->except(['images', 'try_on_asset']);

            // Store try-on asset if provided
            if ($request->hasFile('try_on_asset')) {
                $tryOnPath = $request->file('try_on_asset')->store('public/try_on_assets');
                $data['try_on_asset'] = Storage::url($tryOnPath);
            }

            // Store images and save URLs
            if ($request->hasFile('images')) {
                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('public/frames');
                    $imageUrls[] = Storage::url($path);
                }
                $data['images'] = json_encode($imageUrls);
            }

            $frame = Frame::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Frame created successfully',
                'data' => $frame
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // Show a single frame
    public function show($id)
    {
        try {
            $frame = Frame::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Frame retrieved successfully',
                'data' => $frame
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update frame
    public function update(Request $request, $id)
    {
        try {
            // Find the frame
            $frame = Frame::findOrFail($id);

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'try_on_asset' => 'nullable|file|mimes:glb,gltf',
                'slug' => 'nullable|string|unique:frames,slug,' . $frame->id,
                'brand' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'category_id' => 'sometimes|uuid|exists:categories,id',
                'weight' => 'sometimes|numeric',
                'gender' => 'sometimes|in:men,women,both',
                'price' => 'sometimes|numeric',
                'discount_price' => 'nullable|numeric',
                'stock_quantity' => 'sometimes|integer',
                'featured' => 'boolean',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'tags' => 'nullable|array',
                'tags.*' => 'string',
                'ratings' => 'nullable|numeric|min:1|max:5',
                'status' => 'nullable|in:available,out-of-stock,unavailable',
            ]);

            // If validation fails, throw an exception
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->except(['images', 'try_on_asset']);

            // Handle try-on asset upload
            if ($request->hasFile('try_on_asset')) {
                $tryOnPath = $request->file('try_on_asset')->store('public/try_on_assets');
                $data['try_on_asset'] = Storage::url($tryOnPath);
            }

            // Handle images upload
            if ($request->hasFile('images')) {
                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('public/frames');
                    $imageUrls[] = Storage::url($path);
                }
                $data['images'] = json_encode($imageUrls);
            }

            // Update frame
            $frame->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Frame updated successfully',
                'data' => $frame
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }




    // Delete frame
    public function destroy($id)
    {
        try {
            $frame = Frame::findOrFail($id);
            $frame->delete();

            return response()->json([
                'success' => true,
                'message' => 'Frame deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting frame',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
