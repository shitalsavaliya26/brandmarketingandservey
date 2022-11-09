<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsInformationFilled
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
        if (empty(Auth::user()->organization_name) || empty(Auth::user()->contact_number) || empty(Auth::user()->country_id ) || empty(Auth::user()->calling_code)) {

            if(empty(Auth::user()->provider_name)){
                return redirect()->route('subscriber.welcome');
            }else{
                return redirect()->route('subscriber.profile')->with('success','Complete your profile to start.');
            }
            
        }
        return $next($request);
    }
}
