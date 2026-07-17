<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function routeTestPath($name, array $parameters = array())
    {
        $url = route($name, $parameters);
        $path = (string) parse_url($url, PHP_URL_PATH);
        $basePath = (string) parse_url((string) config('app.url'), PHP_URL_PATH);

        if ($basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        return $path === '' ? '/' : $path;
    }
}
