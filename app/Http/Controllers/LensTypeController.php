<?php

namespace App\Http\Controllers;

use App\Models\LensType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Storage;

class LensTypeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = LensType::query();

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
            $lensTypes = $query->paginate($limit);

            // Prefix API_IMAGE_HOST to each thumbnail
            $lensTypes->getCollection()->transform(function ($lensType) {
                $lensType->thumbnail = env('API_IMAGE_HOST') . $lensType->thumbnail;
                return $lensType;
            });

            return response()->json([
                'success' => true,
                'message' => 'Lens Types retrieved successfully',
                'data' => $lensTypes
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lens types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lens_types,name',
                'description' => 'required|string',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'required|in:available,unavailable',
            ]);

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $lensType = LensType::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lens type created successfully',
                'data' => $lensType
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
                'message' => 'Error creating lens type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $lensType = LensType::findOrFail($id);
            $lensType->thumbnail = env('API_IMAGE_HOST') . $lensType->thumbnail;
            return response()->json([
                'success' => true,
                'message' => 'Lens type retrieved successfully',
                'data' => $lensType
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lens type not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $lensType = LensType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:lens_types,name,' . $id,
                'description' => 'sometimes|required|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'sometimes|required|in:available,unavailable',
            ]);

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($lensType->thumbnail) {
                    Storage::disk('public')->delete($lensType->thumbnail);
                }

                // Store new file
                $thumbnailPath = $request->file('thumbnail')->store('images', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $lensType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lens type updated successfully',
                'data' => $lensType
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
                'message' => 'Error updating lens type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lensType = LensType::findOrFail($id);
            $lensType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lens type deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lens type',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
