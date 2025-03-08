<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FrameColor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class FrameColorController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = FrameColor::with(['frame', 'color'])->get();
            return response()->json([
                'success' => true,
                'message' => 'Frame colors retrieved successfully',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'frame_id' => 'required|uuid|exists:frames,id',
                'color_id' => 'required|uuid|exists:colors,id',
                'status' => 'nullable|in:available,unavailable'
            ]);

            $existingFrameLens = FrameColor::where('frame_id', $request->frame_id)
                ->where('color_id', $request->color_id)
                ->first();

            if ($existingFrameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'The combination of this frame and color already exists.',
                ], 422);
            }

            $frameColor = FrameColor::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame color created successfully',
                'data' => $frameColor
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
                'message' => 'Error creating frame color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $frameColor = FrameColor::with(['frame', 'color'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Frame color retrieved successfully',
                'data' => $frameColor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame color not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'frame_id' => 'uuid|exists:frames,id',
                'color_id' => 'uuid|exists:colors,id',
                'status' => 'in:available,unavailable'
            ]);

            $frameColor = FrameColor::findOrFail($id);

            $existingFrameLens = FrameColor::where('frame_id', $request->frame_id)
                ->where('color_id', $request->color_id)->where('status', $request->status)
                ->first();

            if ($existingFrameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'The combination of this frame and color already exists.',
                ], 422);
            }

            $frameColor->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame color updated successfully',
                'data' => $frameColor
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
                'message' => 'Error updating frame color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $frameColor = FrameColor::findOrFail($id);
            $frameColor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Frame color deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting frame color',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
