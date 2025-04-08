<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Size::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('shorter_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            }

            if ($request->filled('status')) {
                $status = $request->query('status');
                $query->where('status', $status);
            }

            $limit = $request->input('limit', 10);
            $Sizes = $query->paginate($limit);



            return response()->json([
                'success' => true,
                'message' => 'Sizes are retrieved successfully',
                'data' => $Sizes
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Sizes',
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
                'shorter_name' => 'nullable|string|max:255',
                'width_range' => 'required|string|max:255',
                'description' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'nullable|in:available,unavailable',
            ]);

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
                'shorter_name' => 'sometimes|string|max:255',
                'width_range' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'sometimes|in:available,unavailable',
            ]);

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
