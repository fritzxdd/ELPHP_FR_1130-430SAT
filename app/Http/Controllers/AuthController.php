<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate token
        $token = Str::random(60);
        
        // Store token
        $authToken = AuthToken::create([
            'user_id' => $user->users_id,
            'token' => $token,
            'expires_at' => now()->addDays(7) // Token expires in 7 days
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->users_id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token
        ]);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admins,renters,owners'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Generate token
        $token = Str::random(60);
        
        // Store token
        $authToken = AuthToken::create([
            'user_id' => $user->users_id,
            'token' => $token,
            'expires_at' => now()->addDays(7) // Token expires in 7 days
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->users_id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token) {
            // Remove "Bearer " prefix if present
            $token = str_replace('Bearer ', '', $token);
            
            // Delete token from database
            AuthToken::where('token', $token)->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}