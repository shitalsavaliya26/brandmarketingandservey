<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsVerifyEmail
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
        if (!Auth::user()->email_verified_at) {
            auth()->logout();
            return redirect()->route('subscriber.login')
                    ->with('error', 'You need to confirm your account. We have sent you an activation link, please check your email.');
          }
   
        return $next($request);
    }
}
