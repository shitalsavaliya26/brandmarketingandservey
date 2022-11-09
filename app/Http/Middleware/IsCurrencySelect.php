<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;

class IsCurrencySelect
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
        if (empty(Auth::user()->currency_id)) {
            $id = CustomHelper::getEncrypted(Auth::user()->id);
            return redirect()->route('currency.form', ['id' => $id]);
        }
        return $next($request);
    }
}
