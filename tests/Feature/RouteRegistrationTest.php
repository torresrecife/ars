<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_auth_routes_are_registered_with_expected_middleware()
    {
        $loginRoute = app('router')->getRoutes()->getByName('login');
        $homeRoute = app('router')->getRoutes()->getByName('legacy.home');

        $this->assertNotNull($loginRoute);
        $this->assertNotNull($homeRoute);
        $this->assertSame('login', $loginRoute->uri());
        $this->assertSame('index', $homeRoute->uri());
        $this->assertContains('guest', $loginRoute->gatherMiddleware());
        $this->assertContains('auth', $homeRoute->gatherMiddleware());
    }

    public function test_user_admin_rest_routes_are_registered()
    {
        $showRoute = app('router')->getRoutes()->getByName('admin.usuarios.show');
        $storeRoute = app('router')->getRoutes()->getByName('admin.usuarios.store');
        $updateRoute = app('router')->getRoutes()->getByName('admin.usuarios.update');
        $destroyRoute = app('router')->getRoutes()->getByName('admin.usuarios.destroy');

        $this->assertNotNull($showRoute);
        $this->assertNotNull($storeRoute);
        $this->assertNotNull($updateRoute);
        $this->assertNotNull($destroyRoute);
        $this->assertSame(array('GET', 'HEAD'), $showRoute->methods());
        $this->assertSame(array('POST'), $storeRoute->methods());
        $this->assertSame(array('PUT', 'PATCH'), $updateRoute->methods());
        $this->assertSame(array('DELETE'), $destroyRoute->methods());
        $this->assertSame('admin/usuarios/{id}', $showRoute->uri());
        $this->assertContains('auth', $showRoute->gatherMiddleware());
        $this->assertContains('can:viewAny,App\\Models\\Usuario', $showRoute->gatherMiddleware());
    }

    public function test_meta_admin_routes_are_registered_with_meta_gate()
    {
        $showRoute = app('router')->getRoutes()->getByName('admin.metas.show');
        $pageRoute = app('router')->getRoutes()->getByName('metas');

        $this->assertNotNull($showRoute);
        $this->assertNotNull($pageRoute);
        $this->assertContains('auth', $showRoute->gatherMiddleware());
        $this->assertContains('can:viewAny,App\\Models\\MetaAndamento', $showRoute->gatherMiddleware());
        $this->assertContains('can:access-admin', $pageRoute->gatherMiddleware());
        $this->assertContains('can:viewAny,App\\Models\\MetaAndamento', $pageRoute->gatherMiddleware());
    }
}
