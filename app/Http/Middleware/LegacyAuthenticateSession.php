<?php

namespace App\Http\Middleware;

use Closure;

class LegacyAuthenticateSession
{
    private function loginUrl()
    {
        return url('login');
    }

    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('usuarioID') || !$request->session()->has('usuarioNome')) {
            return redirect($this->loginUrl());
        }

        return $next($request);
    }
}
