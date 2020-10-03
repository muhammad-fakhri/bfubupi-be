<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSpecificMiddleware
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
            if (strcmp($role, 'user') == 0) {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user->id != $request->route('user_id')) {
                    return response()->json(['code' => '403', 'message' => "You don't have access to this resource"], 403);
                }
            }
            return $next($request);
        } catch (\Exception $exception) {
            return response()->json(['code' => '401', 'message' => 'You are unauthorized'], 401);
        }
    }
}
