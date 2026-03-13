<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Log the request details
        Log::info('RoleMiddleware accessed', [
            'path' => $request->path(),
            'method' => $request->method(),
            'has_token' => $request->bearerToken() ? 'Yes' : 'No',
            'token_preview' => substr($request->bearerToken() ?? '', 0, 10) . '...'
        ]);

        if (!auth()->check()) {
            Log::warning('RoleMiddleware: Auth check failed', [
                'token' => $request->bearerToken(),
                'headers' => $request->headers->all()
            ]);
            
            return response()->json([
                'message' => 'Unauthorized - Not logged in',
                'debug' => [
                    'auth_check' => auth()->check(),
                    'has_token' => !is_null($request->bearerToken()),
                    'token_preview' => substr($request->bearerToken() ?? '', 0, 10) . '...'
                ]
            ], 401);
        }

        $user = auth()->user();
        
        Log::info('RoleMiddleware: User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Forbidden - insufficient permissions',
            'debug' => [
                'your_role' => $user->role,
                'required_roles' => $roles
            ]
        ], 403);
    }
}

