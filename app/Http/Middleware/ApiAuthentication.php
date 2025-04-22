<?php
// app/Http/Middleware/ApiAuthentication.php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\AuthToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the Authorization header
        $token = $request->header('Authorization');
        
        // Check if token exists
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Authentication token is missing.'
            ], 401);
        }
        
        // Remove "Bearer " prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        // Find the token in the database
        $authToken = AuthToken::where('token', $token)
                            ->where('expires_at', '>', now())
                            ->first();
        
        if (!$authToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or expired token.'
            ], 401);
        }
        
        // Get the user associated with the token
        $user = User::find($authToken->user_id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. User not found.'
            ], 401);
        }
        
        // Add user to the request
        $request->user = $user;
        
        return $next($request);
    }
}