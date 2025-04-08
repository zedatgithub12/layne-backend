<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;
use Storage;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Color::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")->orWhereJsonContains('tags', $search);
                });
            }

            if ($request->filled('status')) {
                $status = $request->query('status');
                $query->where('status', $status);
            }

            $limit = $request->input('limit', 10);
            $colors = $query->paginate($limit);

            // Prefix API_IMAGE_HOST to each texture image
            $colors->getCollection()->transform(function ($color) {

                if ($color->texture_image) {
                    $color->texture_image = env('API_IMAGE_HOST') . $color->texture_image;
                }
                return $color;
            });

            return response()->json([
                'success' => true,
                'message' => 'Colors retrieved successfully',
                'data' => $colors
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching  colors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'color_code' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/|unique:colors,color_code',
            'description' => 'nullable|string',
            'is_textured' => 'nullable|boolean',
            'texture_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_mixed' => 'nullable|boolean',
            'mixed_colors' => 'nullable|array',
            'mixed_colors.*' => 'string|max:50',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => ['nullable', Rule::in(['published', "draft", 'unpublished'])],
        ]);


        try {
            if ($request->hasFile('texture_image')) {
                $ImagePath = $request->file('texture_image')->store('images', 'public');
                $validated['texture_image'] = $ImagePath;
            }
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
            'color_code' => 'sometimes|string|size:7|regex:/^#[a-fA-F0-9]{6}$/|unique:colors,color_code,' . $id,
            'description' => 'nullable|string',
            'is_textured' => 'nullable|boolean',
            'texture_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_mixed' => 'nullable|boolean',
            'mixed_colors' => 'nullable|array',
            'mixed_colors.*' => 'string|max:50',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => ['sometimes', Rule::in(['published', "draft", 'unpublished'])],
        ]);

        try {
            $color = Color::findOrFail($id);

            if ($request->hasFile('texture_image')) {
                if ($color->texture_image) {
                    Storage::disk('public')->delete($color->texture_image);
                }

                // Store new file
                $imagePath = $request->file('texture_image')->store('images', 'public');
                $validated['texture_image'] = $imagePath;
            }
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
