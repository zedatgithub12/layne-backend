<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class ReviewController extends Controller
{
    public function index()
    {
        try {
            $reviews = Review::with(['customer', 'frame'])->get();
            return response()->json([
                'success' => true,
                'message' => 'Reviews retrieved successfully',
                'data' => $reviews
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve reviews',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'frame_id' => 'required|uuid|exists:frames,id',
            'review_text' => 'nullable|string',
            'rating_value' => 'required|numeric|min:0|max:5',
            'rated_features' => 'required|string',
            'is_featured' => 'required|boolean',
            'status' => ['nullable', Rule::in(['drafted', 'approved', 'archived'])],
        ]);

        try {
            $review = Review::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Review created successfully',
                'data' => $review
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review',
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $review = Review::with(['customer', 'frame'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Review retrieved successfully',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
                'data' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'review_text' => 'nullable|string',
            'rating_value' => 'sometimes|numeric|min:0|max:5',
            'rated_features' => 'sometimes|string',
            'is_featured' => 'sometimes|boolean',
            'status' => ['sometimes', Rule::in(['drafted', 'approved', 'archived'])],
        ]);

        try {
            $review = Review::findOrFail($id);
            $review->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review',
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
                'data' => null
            ], 500);
        }
    }
}
