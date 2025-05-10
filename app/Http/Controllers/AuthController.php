<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    public function getAllUsers()
    {
        $users = User::all();
        if (!$users) {
            return response()->json(["success" => false, "message" => ""], 404);
        }
        return response()->json(['users' => $users], 200);
    }


    public function createAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'gender' => 'nullable|string|in:male,female',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole($validated['role']);
            if ($validated['role'] === 'user') {
                Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'gender' => $user->gender,
                    'phone_number' => $user->phone,
                    'email' => $user->email,
                    'is_verified' => false,
                    'status' => 'active',
                    'otp_code' => null,
                    'otp_expires_at' => null,
                    'birthdate' => null,
                    'shipping_address' => null,
                ]);
            }

            // All good, commit transaction
            DB::commit();

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                "success" => true,
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Account creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user account. Please try again.'
            ], 500);
        }
    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $username = $request->input('username');
        $password = $request->input('password');


        $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [$fieldType => $username, 'password' => $password];

        // Attempt authentication
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        return response()->json([
            'token' => $token,
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function destroy()
    {
        try {
            $id = Auth::id();
            $user = User::findOrFail($id);
            if ($user->hasRole('user')) {
                $customer = Customer::where('user_id', $user->id)->first();
                if ($customer) {
                    $customer->delete();
                }
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = auth()->user();


        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update the password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }
}
