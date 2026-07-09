<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    private function legacyIndexUrl()
    {
        $scriptName = str_replace('\\', '/', isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '');
        if ($scriptName === '') {
            return '/index.php';
        }

        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return ($directory === '' || $directory === '.')
            ? '/index.php'
            : $directory . '/index.php';
    }

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
        if (Auth::guard($guard)->check() || ($request->session()->has('usuarioID') && $request->session()->has('usuarioNome'))) {
            return response('', 302)->header('Location', $this->legacyIndexUrl());
        }

        return $next($request);
    }
}
