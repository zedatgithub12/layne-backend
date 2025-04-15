<?php

namespace App\Http\Controllers;

use App\Models\FrameShape;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;
use Storage;

class FrameShapeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = FrameShape::query();

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
            $frameShapes = $query->paginate($limit);

            // Prefix API_IMAGE_HOST to each thumbnail
            $frameShapes->getCollection()->transform(function ($frameShape) {
                $frameShape->thumbnail = config('app.api_image_host') . $frameShape->thumbnail;
                return $frameShape;
            });

            return response()->json([
                'success' => true,
                'message' => 'Frame Shapes retrieved successfully',
                'data' => $frameShapes,

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
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => ['required', Rule::in(['available', 'out-of-stock', 'unavailable'])],
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

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
            $frameShape->thumbnail = config('app.api_image_host') . $frameShape->thumbnail;

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
            'slug' => 'nullable|string|unique:frame_shapes,slug,' . $id . ',id',
            'description' => 'sometimes|string',
            'thumbnail' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        try {
            $frameShape = FrameShape::findOrFail($id);

            if (!$frameShape) {
                return response()->json([
                    'success' => false,
                    'message' => 'the shape is not found in database',

                ], 404);

            }

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($frameShape->thumbnail) {
                    Storage::disk('public')->delete($frameShape->thumbnail);
                }

                // Store new file
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $frameShape->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame Shape updated successfully',
                'data' => $request->all()
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
