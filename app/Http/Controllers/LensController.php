<?php

namespace App\Http\Controllers;

use App\Models\Lens;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class LensController extends Controller
{
    public function index()
    {
        $lenses = Lens::with('lensType')->get();
        return response()->json([
            'success' => true,
            'message' => 'Lenses retrieved successfully',
            'data' => $lenses
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'lens_type_id' => 'required|uuid|exists:lens_types,id',
                'lens_material' => 'required|in:still,iron,glass,plastic',
                'lens_color' => 'required|string|max:255',
                'lens_coating' => 'required|string|max:255',
                'lens_power' => 'required|string|max:255',
                'polarized' => 'required|boolean',
                'photochromatic' => 'required|boolean',
                'lens_thickness' => 'required|integer|min:1',
                'description' => 'required|string',
                'use_cases' => 'required|string|max:255',
                'status' => 'required|in:available,unavailable',
            ]);

            $lens = Lens::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lens created successfully',
                'data' => $lens
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
                'message' => 'Error creating lens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $lens = Lens::with('lensType')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Lens retrieved successfully',
                'data' => $lens
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lens not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $lens = Lens::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'lens_type_id' => 'sometimes|required|uuid|exists:lens_types,id',
                'lens_material' => 'sometimes|required|in:still,iron,glass,plastic',
                'lens_color' => 'sometimes|required|string|max:255',
                'lens_coating' => 'sometimes|required|string|max:255',
                'lens_power' => 'sometimes|required|string|max:255',
                'polarized' => 'sometimes|required|boolean',
                'photochromatic' => 'sometimes|required|boolean',
                'lens_thickness' => 'sometimes|required|integer|min:1',
                'description' => 'sometimes|required|string',
                'use_cases' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|required|in:available,unavailable',
            ]);

            $lens->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lens updated successfully',
                'data' => $lens
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
                'message' => 'Error updating lens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lens = Lens::findOrFail($id);
            $lens->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lens deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lens',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
