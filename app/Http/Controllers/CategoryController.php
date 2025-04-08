<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Str;
use Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Category::query();

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
            $categories = $query->paginate($limit);

            // Prefix API_IMAGE_HOST to each thumbnail
            $categories->getCollection()->transform(function ($frameShape) {
                $frameShape->thumbnail = env('API_IMAGE_HOST') . $frameShape->thumbnail;
                return $frameShape;
            });

            return response()->json([
                'success' => true,
                'message' => 'Categories are retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'slug' => 'nullable|string|unique:categories,slug',
                'description' => 'required|string',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'required|in:active,inactive',
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->thumbnail = env('API_IMAGE_HOST') . $category->thumbnail;
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'description' => 'sometimes|string',
                'thumbnail' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'sometimes|required|in:active,inactive',
            ]);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'the category is not found in database',

                ], 404);

            }

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($category->thumbnail) {
                    Storage::disk('public')->delete($category->thumbnail);
                }

                // Store new file
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
