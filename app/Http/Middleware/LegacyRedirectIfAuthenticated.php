<?php

namespace App\Http\Middleware;

use Closure;

class LegacyRedirectIfAuthenticated
{
    private function indexUrl()
    {
        return url('index');
    }

    public function handle($request, Closure $next)
    {
        if ($request->session()->has('usuarioID') && $request->session()->has('usuarioNome')) {
            return redirect($this->indexUrl());
        }

        return $next($request);
    }
}
