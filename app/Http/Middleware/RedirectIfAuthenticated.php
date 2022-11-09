<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == "subscribers" && Auth::guard($guard)->check()) {
            return redirect()->route('subscriber.dashboard');
        }

        if ($guard == "superadmins" && Auth::guard($guard)->check()) {
            return redirect()->route('superadmin.dashboard');
        }
        return $next($request);
    }
}
