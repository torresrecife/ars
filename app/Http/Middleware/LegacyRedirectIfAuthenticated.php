<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LegacyRedirectIfAuthenticated
{
    private function indexUrl()
    {
        return url('index');
    }

    public function handle($request, Closure $next)
    {
        if (Auth::guard('web')->check() || ($request->session()->has('usuarioID') && $request->session()->has('usuarioNome'))) {
            return redirect($this->indexUrl());
        }

        return $next($request);
    }
}
