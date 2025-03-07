<?php

namespace App\Http\Controllers;

use App\Models\LensType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class LensTypeController extends Controller
{
    public function index()
    {
        $lensTypes = LensType::all();
        return response()->json([
            'success' => true,
            'message' => 'Lens types retrieved successfully',
            'data' => $lensTypes
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lens_types,name',
                'description' => 'required|string',
                'use_case' => 'required|string|max:255',
                'thickness' => 'required|string|max:255',
                'status' => 'required|in:available,unavailable',
            ]);

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
                'use_case' => 'sometimes|required|string|max:255',
                'thickness' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|required|in:available,unavailable',
            ]);

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
