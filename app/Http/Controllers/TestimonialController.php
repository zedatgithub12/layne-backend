<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class TestimonialController extends Controller
{
    public function index()
    {
        try {
            $testimonials = Testimonial::with(['customer', 'frame', 'order'])->get();
            return response()->json([
                'success' => true,
                'message' => 'Testimonials retrieved successfully',
                'data' => $testimonials
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve testimonials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'frame_id' => 'required|uuid|exists:frames,id',
            'order_id' => 'required|uuid|exists:orders,id',
            'image' => 'nullable|string',
            'testimonial_text' => 'required|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_verified' => 'required|boolean',
            'is_featured' => 'required|boolean',
            'reply' => 'nullable|string',
            'status' => ['nullable', Rule::in(['drafted', 'approved', 'archived', 'rejected'])],
        ]);

        try {
            $testimonial = Testimonial::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Testimonial created successfully',
                'data' => $testimonial
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create testimonial',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $testimonial = Testimonial::with(['customer', 'frame', 'order'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Testimonial retrieved successfully',
                'data' => $testimonial
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found',
                'data' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'image' => 'nullable|string',
            'testimonial_text' => 'sometimes|string',
            'rating' => 'sometimes|numeric|min:0|max:5',
            'is_verified' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'reply' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['drafted', 'approved', 'archived', 'rejected'])],
        ]);

        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Testimonial updated successfully',
                'data' => $testimonial
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update testimonial',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->delete();
            return response()->json([
                'success' => true,
                'message' => 'Testimonial deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete testimonial',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}

