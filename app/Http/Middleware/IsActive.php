<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsActive
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
        if (!Auth::user()->status == 1) {
            auth()->logout();
            return redirect()->route('subscriber.login')
                    ->with('error', 'Looks like your status is deactivate by admin.');
          }
   
        return $next($request);
    }
}
