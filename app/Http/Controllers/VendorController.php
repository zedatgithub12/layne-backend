<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully',
            'data' => $vendors
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:vendors,email|max:255',
            ]);

            $vendor = Vendor::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully',
                'data' => $vendor
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
                'message' => 'Error creating vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Vendor retrieved successfully',
                'data' => $vendor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:vendors,email,' . $id . '|max:255',
            ]);

            $vendor->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully',
                'data' => $vendor
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
                'message' => 'Error updating vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
