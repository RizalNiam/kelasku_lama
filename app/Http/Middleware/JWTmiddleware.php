<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JWTmiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::parseToken(); // Get the token from the request
            $user = $token->authenticate(); // Try to authenticate the token
        } catch (\Throwable $th) {
            // If the token is invalid or authentication fails, return a 401 Unauthorized response
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // If the token is valid and authentication succeeds, set the authenticated user on the request
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
