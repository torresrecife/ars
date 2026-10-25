<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $route = app('router')->getRoutes()->getByName('login');

        $this->assertNotNull($route);
        $this->assertSame('login', $route->uri());
    }
}
