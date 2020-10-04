<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AllAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            $apy = JWTAuth::getPayload($token);
            $role = $apy->get('role');
            if ($role && (strcmp($role, 'admin') == 0 || strcmp($role, 'superadmin') == 0) || strcmp($role, 'user') == 0) {
                return $next($request);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof JWTException) {
                return response()->json(['code' => '401', 'message' => 'You are unauthorized'], 401);
            } else {
                return response()->json(['code' => '500', 'message' => 'Internal server error'], 500);
            }
        }
    }
}
