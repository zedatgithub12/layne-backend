<?php

namespace App\Http\Controllers;

use App\Models\FrameSize;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class FrameSizeController extends Controller
{
    public function index()
    {
        $frameSizes = FrameSize::with(['frame', 'size'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Frame sizes retrieved successfully',
            'data' => $frameSizes
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'frame_id' => 'required|uuid|exists:frames,id',
            'size_id' => 'required|uuid|exists:sizes,id',
            'status' => 'nullable|in:available,unavailable',
        ]);

        $existingFrameSize = FrameSize::where('frame_id', $request->frame_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingFrameSize) {
            return response()->json([
                'success' => false,
                'message' => 'The combination of this frame and size already exists.',
            ], 422);
        }

        try {
            $frameSize = FrameSize::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame size created successfully',
                'data' => $frameSize
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create frame size',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $frameSize = FrameSize::with(['frame', 'size'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Frame size retrieved successfully',
                'data' => $frameSize
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame size not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $frameSize = FrameSize::findOrFail($id);

            $validated = $request->validate([
                'frame_id' => 'uuid|exists:frames,id',
                'size_id' => 'uuid|exists:sizes,id',
                'status' => 'in:available,unavailable',
            ]);

            $existingFrameSize = FrameSize::where('frame_id', $request->frame_id)
                ->where('size_id', $request->size_id)->where('status', $request->status)->first();

            if ($existingFrameSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'The combination of this frame and size already exists.',
                ], 422);
            }


            $frameSize->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Frame size updated successfully',
                'data' => $frameSize
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame size not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update frame size',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $frameSize = FrameSize::findOrFail($id);
            $frameSize->delete();

            return response()->json([
                'success' => true,
                'message' => 'Frame size deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Frame size not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete frame size',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
