<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    // Create a new role
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        return response()->json(['role' => $role], 201);
    }
    // Get all roles
    public function getRoles()
    {
        $roles = Role::all();
        if (!$roles) {
            return response()->json(["success" => false, "message" => ""], 404);
        }
        return response()->json(['roles' => $roles], 200);


    }

    // Get all permissions
    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json(['permissions' => $permissions], 200);
    }
}
