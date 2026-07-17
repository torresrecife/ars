<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LegacyAuthenticateSession
{
    private function loginUrl()
    {
        return url('login');
    }

    public function handle($request, Closure $next)
    {
        if (!Auth::guard('web')->check() && (!$request->session()->has('usuarioID') || !$request->session()->has('usuarioNome'))) {
            return redirect($this->loginUrl());
        }

        return $next($request);
    }
}
