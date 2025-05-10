<?php
namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AdController extends Controller
{
    public function index()
    {
        try {
            $ads = Ad::all();
            foreach ($ads as $ad) {
                $ad->image = config('app.api_image_host') . $ad->image;
            }
            return response()->json([
                'success' => true,
                'message' => 'Ads retrieved successfully',
                'data' => $ads
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to retrieve ads');
        }
    }

    public function show($id)
    {
        try {
            $ad = Ad::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Ad retrieved successfully',
                'data' => $ad
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Ad not found');
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to retrieve ad');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'link' => 'required|url',
                'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            $imagePath = $request->file('image')->store('images', 'public');

            $ad = Ad::create([
                'id' => Str::uuid(),
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'link' => $request->link,
                'image' => $imagePath,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ad created successfully',
                'data' => $ad
            ], 201);
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to create ad');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ad = Ad::findOrFail($id);

            $request->validate([
                'title' => 'sometimes|string|max:255',
                'subtitle' => 'sometimes|string|max:255',
                'link' => 'sometimes|url',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                if ($ad->image && Storage::disk('public')->exists($ad->image)) {
                    Storage::disk('public')->delete($ad->image);
                }
                $ad->image = $request->file('image')->store('images', 'public');
            }

            $ad->fill($request->only(['title', 'subtitle', 'link']))->save();

            return response()->json([
                'success' => true,
                'message' => 'Ad updated successfully',
                'data' => $ad
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Ad not found');
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to update ad');
        }
    }

    public function destroy($id)
    {
        try {
            $ad = Ad::findOrFail($id);

            if ($ad->image && Storage::disk('public')->exists($ad->image)) {
                Storage::disk('public')->delete($ad->image);
            }

            $ad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ad deleted successfully',
                'data' => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Ad not found');
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to delete ad');
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,active,inactive',
            ]);

            $ad = Ad::findOrFail($id);
            $ad->status = $request->status;
            $ad->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $ad
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Ad not found');
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to update status');
        }
    }

    // Helper: Standard error handler
    private function handleException($e, $message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => $e->getMessage()
        ], 500);
    }

    // Helper: 404 Not Found response
    private function notFound($message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 404);
    }
}
