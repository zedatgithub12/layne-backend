<?php

namespace App\Http\Controllers;

use App\Models\FramesShape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FramesShapeController extends Controller
{
    /**
     * Store a newly created FramesShape.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'frame_id' => 'required|uuid|exists:frames,id',
                'shape_id' => 'required|uuid|exists:frame_shapes,id',
                'status' => 'nullable|in:available,unavailable'
            ]);


            $existingFrameLens = FramesShape::where('frame_id', $request->frame_id)
                ->where('shape_id', $request->shape_id)
                ->first();

            if ($existingFrameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'The combination of this frame and shape already exists.',
                ], 422);
            }

            // Create the FramesShape record
            $framesShape = FramesShape::create([
                'id' => \Str::uuid(),
                'frame_id' => $validatedData['frame_id'],
                'shape_id' => $validatedData['shape_id'],
                'status' => $validatedData['status'],
            ]);

            // Return the success response
            return response()->json([
                'success' => true,
                'message' => 'FramesShape created successfully',
                'data' => $framesShape
            ], 200);

        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified FramesShape.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Find FramesShape by ID with related frame and shape data
            $framesShape = FramesShape::with(['frame', 'shape'])->findOrFail($id);

            // Return the success response
            return response()->json([
                'success' => true,
                'message' => 'FramesShape retrieved successfully',
                'data' => $framesShape
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Handle record not found
            return response()->json([
                'success' => false,
                'message' => 'FramesShape not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Update the specified FramesShape.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'frame_id' => 'sometimes|uuid|exists:frames,id',
                'shape_id' => 'sometimes|uuid|exists:frame_shapes,id',
                'status' => 'nullable|in:available,unavailable'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Find the FramesShape by ID
            $framesShape = FramesShape::findOrFail($id);

            if (!$framesShape) {
                return response()->json([
                    'success' => false,
                    'message' => "To be updated record is not found",
                ], 400);
            }

            $existingFrameLens = FramesShape::where('frame_id', $request->frame_id)
                ->where('shape_id', $request->shape_id)
                ->first();

            if ($existingFrameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'The combination of this frame and shape already exists.',
                ], 422);
            }

            $framesShape->update($request->only(['frame_id', 'shape_id', 'status']));

            // Return the success response
            return response()->json([
                'success' => true,
                'message' => 'FramesShape updated successfully',
                'data' => $framesShape
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Handle record not found
            return response()->json([
                'success' => false,
                'message' => 'FramesShape not found',
                'data' => []
            ], 404);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Remove the specified FramesShape.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find the FramesShape by ID
            $framesShape = FramesShape::findOrFail($id);

            // Delete the FramesShape record
            $framesShape->delete();

            // Return the success response
            return response()->json([
                'success' => true,
                'message' => 'FramesShape deleted successfully',
                'data' => []
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Handle record not found
            return response()->json([
                'success' => false,
                'message' => 'FramesShape not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * List all FramesShapes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get all FramesShapes with their related frame and shape data
            $framesShapes = FramesShape::with(['frame', 'shape'])->get();

            // Return the success response
            return response()->json([
                'success' => true,
                'message' => 'FramesShapes retrieved successfully',
                'data' => $framesShapes
            ], 200);

        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}

