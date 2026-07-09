<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
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

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return $this->legacyLoginUrl();
        }
    }
}
