<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class PaymentController extends Controller
{
    public function index()
    {
        try {
            $payments = Payment::all();
            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully',
                'data' => $payments
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|uuid|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'transaction_id' => 'required|string|max:255|unique:payments,transaction_id',
            'currency' => 'required|string|max:10',
            'gateway_fee' => 'nullable|numeric|min:0',
            'refund_status' => 'nullable|boolean',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'nullable|in:pending,completed,refunded',
        ]);

        try {
            $payment = Payment::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => $payment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment',
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Payment retrieved successfully',
                'data' => $payment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|string|max:255',
            'transaction_id' => 'sometimes|string|max:255|unique:payments,transaction_id,' . $id,
            'currency' => 'sometimes|string|max:10',
            'gateway_fee' => 'nullable|numeric|min:0',
            'refund_status' => 'nullable|boolean',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'nullable|in:pending,completed,refunded',
        ]);

        try {
            $payment = Payment::findOrFail($id);
            $payment->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'data' => null
            ], 500);
        }
    }
}

