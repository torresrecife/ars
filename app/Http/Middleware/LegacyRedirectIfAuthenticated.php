<?php

namespace App\Http\Middleware;

use Closure;

class LegacyRedirectIfAuthenticated
{
    private function startLegacySession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (session_name() === 'PHPSESSID') {
                return;
            }

            session_write_close();
        }

        if (session_name() !== 'PHPSESSID') {
            session_name('PHPSESSID');
        }

        session_start();
    }

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

    public function handle($request, Closure $next)
    {
        $this->startLegacySession();

        if (isset($_SESSION['usuarioID']) && isset($_SESSION['usuarioNome'])) {
            return response('', 302)->header('Location', $this->legacyIndexUrl());
        }

        return $next($request);
    }
}
