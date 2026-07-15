<?php

namespace App\Http\Middleware;

use Closure;

class LegacyAuthenticateSession
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
        $this->startLegacySession();

        if ((!$request->session()->has('usuarioID') || !$request->session()->has('usuarioNome'))
            && isset($_SESSION['usuarioID']) && isset($_SESSION['usuarioNome'])) {
            $request->session()->put('usuarioID', $_SESSION['usuarioID']);
            $request->session()->put('usuarioNome', $_SESSION['usuarioNome']);
            if (isset($_SESSION['usuarioNivel'])) {
                $request->session()->put('usuarioNivel', $_SESSION['usuarioNivel']);
            }
            if (isset($_SESSION['usuarioSetor'])) {
                $request->session()->put('usuarioSetor', $_SESSION['usuarioSetor']);
            }
            if (isset($_SESSION['usuarioCliente'])) {
                $request->session()->put('usuarioCliente', $_SESSION['usuarioCliente']);
            }
            if (isset($_SESSION['usuarioRegiaoModo'])) {
                $request->session()->put('usuarioRegiaoModo', $_SESSION['usuarioRegiaoModo']);
            }
            if (isset($_SESSION['usuarioRegiaoIds'])) {
                $request->session()->put('usuarioRegiaoIds', $_SESSION['usuarioRegiaoIds']);
            }
            if (isset($_SESSION['usuarioRegiaoUfs'])) {
                $request->session()->put('usuarioRegiaoUfs', $_SESSION['usuarioRegiaoUfs']);
            }
        }

        if ((!$request->session()->has('usuarioID') || !$request->session()->has('usuarioNome'))
            && (!isset($_SESSION['usuarioID']) || !isset($_SESSION['usuarioNome']))) {
            return response('', 302)->header('Location', $this->legacyLoginUrl());
        }

        return $next($request);
    }
}
