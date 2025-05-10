<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Storage;

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
                'user_id' => 'required|string|max:255',
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

            $customer = Customer::where('user_id', $id)->first();

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'gender' => 'nullable|in:male,female',
                'birthdate' => 'nullable|date',
                'email' => 'nullable|email|unique:customers,email,' . $id . '|max:255',
                'shipping_address' => 'sometimes|required|string',
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

    public function updateAvatar(Request $request)
    {
        $id = Auth::id();
        try {
            $validated = $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $customer = Customer::where('user_id', $id)->get();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            $file = $request->file('avatar');
            $newPath = $file->store('images', 'public');

            // Delete old avatar if it exists
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            // Update and save new avatar path
            $customer->avatar = $newPath;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Customer avatar updated successfully',
                'data' => $customer
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
