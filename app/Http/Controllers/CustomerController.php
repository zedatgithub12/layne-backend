<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => $customers
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'gender' => 'nullable|in:male,female,other',
                'birthdate' => 'nullable|date',
                'phone_number' => 'required|string|max:20|unique:customers,phone_number',
                'email' => 'nullable|email|unique:customers,email|max:255',
                'otp_code' => 'required|integer',
                'otp_expires_at' => 'required|date|after:now',
                'is_verified' => 'boolean',
                'shipping_address' => 'required|string',
                'status' => 'required|in:active,inactive,suspended',
            ]);

            $customer = Customer::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
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
                'message' => 'Error creating customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Customer retrieved successfully',
                'data' => $customer
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'gender' => 'nullable|in:male,female',
                'birthdate' => 'nullable|date',
                'phone_number' => 'sometimes|required|string|max:20|unique:customers,phone_number,' . $id,
                'email' => 'nullable|email|unique:customers,email,' . $id . '|max:255',
                'otp_code' => 'sometimes|required|integer',
                'otp_expires_at' => 'sometimes|required|date|after:now',
                'is_verified' => 'sometimes|required|boolean',
                'shipping_address' => 'sometimes|required|string',
                'status' => 'sometimes|required|in:active,inactive,suspended',
            ]);

            $customer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
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
                'message' => 'Error updating customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
