<?php

namespace App\Http\Controllers\api\roles;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller
{
    // Create a new role
    public function create(Request $request)
    {
        try {
            $validateRole = Validator::make($request->all(), [
                'name' => 'required|string|max:255'
            ]);

            if ($validateRole->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateRole->errors()
                ], 401);
            }

            $role = Role::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Role created successfully',
                'role' => $role,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all roles
    public function index()
    {
        try {
            $roles = Role::all();
            return response()->json([
                'status' => true,
                'message' => 'Roles fetched successfully',
                'roles' => $roles
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a role
    public function update(Request $request, $id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            $validateRole = Validator::make($request->all(), [
                'name' => 'required|string|max:255'
            ]);

            if ($validateRole->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateRole->errors()
                ], 401);
            }

            $role->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Role updated successfully',
                'role' => $role
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a role
    public function delete($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            $role->delete();
            return response()->json([
                'status' => true,
                'message' => 'Role deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
