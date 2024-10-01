<?php

namespace App\Http\Controllers\api\auth;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{   
    // Register new user
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'role_id' => 'nullable|integer|exists:roles,id',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            // Check if there are no users or roles, then create default roles
            $role_id = null;

            if (Role::count() == 0) {
                // Create default roles
                $adminRole = Role::create(['name' => 'Admin']);
                $userRole = Role::create(['name' => 'User']);
                $role_id = $adminRole->id; // First user becomes Admin
            } else {
                // If roles exist, assign "User" role
                $role_id = Role::where('name', 'User')->first()->id;
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $request->city,
                'country' => $request->country,
                'role_id' => $role_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Login a user
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // Get authenticated user
    public function me()
    {
        return response()->json(auth()->user());
    }

    // Logout a user
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully'
        ]);
    }

    // Get refresh token
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // JWT response
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'user' => auth()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

}