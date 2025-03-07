<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;

class ColorController extends Controller
{
    public function index()
    {
        try {
            $colors = Color::all();
            return response()->json([
                'success' => true,
                'message' => 'Colors retrieved successfully',
                'data' => $colors
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching colors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'hex_code' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/|unique:colors,hex_code',
            'slug' => 'nullable|string|unique:colors,slug',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['available', 'unavailable'])],
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
            $color = Color::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color created successfully',
                'data' => $color
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $color = Color::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Color retrieved successfully',
                'data' => $color
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:colors,name,' . $id,
            'hex_code' => 'sometimes|string|size:7|regex:/^#[a-fA-F0-9]{6}$/|unique:colors,hex_code,' . $id,
            'slug' => 'nullable|string|unique:colors,slug,' . $id,
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['available', 'unavailable'])],
        ]);

        try {
            $color = Color::findOrFail($id);
            $color->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color updated successfully',
                'data' => $color
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $color = Color::findOrFail($id);
            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'Color deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting color',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
