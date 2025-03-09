<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use Illuminate\Http\Request;
use Exception;

class WishListController extends Controller
{
    public function index()
    {
        try {
            $wishLists = WishList::with(['customer', 'frame'])->get();
            return response()->json([
                'success' => true,
                'message' => 'Wish list items retrieved successfully',
                'data' => $wishLists
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve wish list items',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'frame_id' => 'required|uuid|exists:frames,id',
        ]);



        try {

            $existingWishlist = WishList::where('frame_id', $request->frame_id)
                ->where('customer_id', $request->customer_id)
                ->first();

            if ($existingWishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'The frame is already added to wish lists',
                ], 422);
            }

            $wishList = WishList::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Wish list item added successfully',
                'data' => $wishList
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add wish list item',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $wishList = WishList::with(['customer', 'frame'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Wish list item retrieved successfully',
                'data' => $wishList
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wish list item not found',
                'error' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $wishList = WishList::findOrFail($id);
            $wishList->delete();
            return response()->json([
                'success' => true,
                'message' => 'Wish list item deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete wish list item',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

