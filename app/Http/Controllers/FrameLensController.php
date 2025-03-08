<?php

namespace App\Http\Controllers;

use App\Models\FrameLens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrameLensController extends Controller
{

    // Fetch all FrameLens records
    public function index()
    {
        try {
            $frameLenses = FrameLens::with(['frame', 'lens'])->get();
            return response()->json([
                'success' => true,
                'message' => 'FrameLens records fetched successfully',
                'data' => $frameLenses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching FrameLens records',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new FrameLens record
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frame_id' => 'required|uuid|exists:frames,id',
            'lens_id' => 'required|uuid|exists:lenses,id',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|in:available,unavailable',
        ]);

        $existingFrameLens = FrameLens::where('frame_id', $request->frame_id)
            ->where('lens_id', $request->lens_id)
            ->first();

        if ($existingFrameLens) {
            return response()->json([
                'success' => false,
                'message' => 'The combination of frame and lens already exists.',
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $frameLens = new FrameLens();
            $frameLens->frame_id = $request->frame_id;
            $frameLens->lens_id = $request->lens_id;
            $frameLens->is_default = $request->is_default ?? $request->is_default;
            $frameLens->status = $request->status ?? $request->status;
            $frameLens->save();

            return response()->json([
                'success' => true,
                'message' => 'FrameLens successfully created',
                'data' => $frameLens
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating FrameLens',
                'data' => $e->getMessage()
            ], 500);
        }
    }


    // Show a specific FrameLens record
    public function show($id)
    {
        try {
            $frameLens = FrameLens::with(['frame', 'lens'])->find($id);

            if (!$frameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'FrameLens not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'FrameLens details fetched successfully',
                'data' => $frameLens
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the FrameLens details',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    // Update a specific FrameLens record
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'frame_id' => 'sometimes|uuid|exists:frames,id',
            'lens_id' => 'sometimes|uuid|exists:lenses,id',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|in:available,unavailable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }


        try {
            $frameLens = FrameLens::find($id);

            if (!$frameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'FrameLens not found',
                ], 404);
            }

            $frameLens->update($request->only(['frame_id', 'lens_id', 'is_default', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'FrameLens successfully updated',
                'data' => $frameLens
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating FrameLens',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a specific FrameLens record
    public function destroy($id)
    {
        try {
            $frameLens = FrameLens::find($id);

            if (!$frameLens) {
                return response()->json([
                    'success' => false,
                    'message' => 'FrameLens not found',
                ], 404);
            }

            $frameLens->delete();

            return response()->json([
                'success' => true,
                'message' => 'FrameLens successfully deleted',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting FrameLens',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}

