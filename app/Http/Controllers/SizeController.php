<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{
    public function index()
    {
        try {
            $sizes = Size::all();

            return response()->json([
                'success' => true,
                'message' => 'Sizes retrieved successfully',
                'data' => $sizes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sizes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new size
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'lens_width' => 'required|integer|min:0',
                'bridge_width' => 'required|integer|min:0',
                'temple_length' => 'required|integer|min:0',
                'status' => 'required|in:available,unavailable',
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            $size = Size::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Size created successfully',
                'data' => $size
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating size',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single size
    public function show($id)
    {
        try {
            $size = Size::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Size retrieved successfully',
                'data' => $size
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Size not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update size
    public function update(Request $request, $id)
    {
        try {
            $size = Size::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'lens_width' => 'sometimes|integer|min:0',
                'bridge_width' => 'sometimes|integer|min:0',
                'temple_length' => 'sometimes|integer|min:0',
                'status' => 'sometimes|in:available,unavailable',
            ]);

            if (isset($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $size->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Size updated successfully',
                'data' => $size
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating size',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete size
    public function destroy($id)
    {
        try {
            $size = Size::findOrFail($id);
            $size->delete();

            return response()->json([
                'success' => true,
                'message' => 'Size deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting size',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
