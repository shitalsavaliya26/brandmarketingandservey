<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
            	return response()->json([
                    "success" => false,
                    "message" => "Authorization Token not found.",
                    "code" => 401,
                ], 401);
            }

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    "success" => false,
                    "message" => "Token is Invalid.",
                    "code" => 401,
                ], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    "success" => false,
                    "message" => "Token is Expired.",
                    "code" => 401,
                ], 401);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Authorization Token not found.",
                    "code" => 401,
                ], 401);
            }
        }
        return $next($request);
    }
}

