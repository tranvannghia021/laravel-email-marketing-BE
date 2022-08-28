<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTokenApi
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

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return response([
                'success' => true
            ]);
        }
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {

                return response()->json([
                    'success' => false,
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Token Expired',
                ]);
            } elseif ($e instanceof TokenInvalidException) {

                return response()->json([
                    'success' => false,
                    'message' => 'Token Invalid',
                ],  Response::HTTP_UNAUTHORIZED);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Token not found',
                ],  Response::HTTP_UNAUTHORIZED);
            }
        }
        return $next($request);
    }
}
