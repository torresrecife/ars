<?php

namespace App\Http\Middleware;

use Closure;

class LegacyAuthenticateSession
{
    private function legacyLoginUrl()
    {
        $scriptName = str_replace('\\', '/', isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '');
        if ($scriptName === '') {
            return '/login.php';
        }

        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return ($directory === '' || $directory === '.')
            ? '/login.php'
            : $directory . '/login.php';
    }

    public function handle($request, Closure $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['usuarioID']) || !isset($_SESSION['usuarioNome'])) {
            return response('', 302)->header('Location', $this->legacyLoginUrl());
        }

        return $next($request);
    }
}
