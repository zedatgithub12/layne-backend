<?php

namespace App\Http\Controllers;

use App\Models\FrameShape;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;

class FrameShapeController extends Controller
{
    public function index()
    {
        try {
            $frameShapes = FrameShape::all();
            return response()->json([
                'success' => true,
                'message' => 'Frame Shapes retrieved successfully',
                'data' => $frameShapes
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching frame shapes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:frame_shapes,slug',
            'description' => 'required|string',
            'rim_type' => 'required|string|max:255',
            'bridge_width' => 'required|integer|min:1',
            'temple_length' => 'required|integer|min:1',
            'lens_width' => 'required|integer|min:1',
            'frame_material' => ['required', Rule::in(['iron', 'plastic', 'wood'])],
            'face_shape_suitability' => 'required|string|max:255',
            'status' => ['required', Rule::in(['available', 'out-of-stock', 'unavailable'])],
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
            $frameShape = FrameShape::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame Shape created successfully',
                'data' => $frameShape
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating frame shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $frameShape = FrameShape::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Frame Shape retrieved successfully',
                'data' => $frameShape
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame Shape not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:frame_shapes,slug,' . $id,
            'description' => 'sometimes|string',
            'rim_type' => 'sometimes|string|max:255',
            'bridge_width' => 'sometimes|integer|min:1',
            'temple_length' => 'sometimes|integer|min:1',
            'lens_width' => 'sometimes|integer|min:1',
            'frame_material' => ['sometimes', Rule::in(['iron', 'plastic', 'wood'])],
            'face_shape_suitability' => 'sometimes|string|max:255',
            'status' => ['sometimes', Rule::in(['available', 'out-of-stock', 'unavailable'])],
        ]);

        try {
            $frameShape = FrameShape::findOrFail($id);
            $frameShape->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame Shape updated successfully',
                'data' => $frameShape
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating frame shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $frameShape = FrameShape::findOrFail($id);
            $frameShape->delete();

            return response()->json([
                'success' => true,
                'message' => 'Frame Shape deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting frame shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
