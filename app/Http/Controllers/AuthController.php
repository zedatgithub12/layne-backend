<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


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

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            "success" => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
}
