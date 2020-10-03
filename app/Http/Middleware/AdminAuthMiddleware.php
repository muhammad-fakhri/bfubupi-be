<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthMiddleware
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
            if ($role && (strcmp($role, 'admin') == 0 || strcmp($role, 'superadmin') == 0)) {
                return $next($request);
            }
            return response()->json(['code' => '403', 'message' => "You don't have access to this resource"], 403);
        } catch (\Exception $exception) {
            return response()->json(['code' => '401', 'message' => 'You are unauthorized'], 401);
        }
    }
}
