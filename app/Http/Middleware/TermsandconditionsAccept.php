<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TermsandconditionsAccept
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
        if (empty(Auth::user()->is_terms_and_conditions_accepted) && !empty(Auth::user()->provider_name)) {
            return redirect()->route('terms_and_conditions.form');
        }
        return $next($request);
    }
}
