<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use App\Services\AuthService;
use App\Support\WriteResult;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_update_own_password_returns_unauthenticated_when_session_is_missing()
    {
        $service = Mockery::mock(AuthService::class);
        $service->shouldReceive('currentUser')->once()->andReturn(false);

        $controller = new AuthController($service);
        $request = Request::create('/password/update', 'POST', array(
            'id_usu' => 8,
            'senha_usu1' => 'nova-senha',
        ));

        $response = $controller->updateOwnPassword($request);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('unauthenticated', $response->getData(true)['code']);
    }

    public function test_update_own_password_returns_invalid_payload_for_empty_password()
    {
        $service = Mockery::mock(AuthService::class);
        $service->shouldReceive('currentUser')->once()->andReturn(array('id_usu' => 8));

        $controller = new AuthController($service);
        $request = Request::create('/password/update', 'POST', array(
            'id_usu' => 8,
            'senha_usu1' => '',
        ));

        $response = $controller->updateOwnPassword($request);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('invalid_payload', $response->getData(true)['code']);
    }

    public function test_update_own_password_returns_forbidden_for_different_user()
    {
        $service = Mockery::mock(AuthService::class);
        $service->shouldReceive('currentUser')->once()->andReturn(array('id_usu' => 8));

        $controller = new AuthController($service);
        $request = Request::create('/password/update', 'POST', array(
            'id_usu' => 9,
            'senha_usu1' => 'nova-senha',
        ));

        $response = $controller->updateOwnPassword($request);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('forbidden', $response->getData(true)['code']);
    }

    public function test_update_own_password_returns_conflict_when_service_fails()
    {
        $service = Mockery::mock(AuthService::class);
        $service->shouldReceive('currentUser')->once()->andReturn(array('id_usu' => 8));
        $service->shouldReceive('updatePasswordAndAccess')->once()->with(8, 'nova-senha')->andReturn(WriteResult::error());

        $controller = new AuthController($service);
        $request = Request::create('/password/update', 'POST', array(
            'id_usu' => 8,
            'senha_usu1' => 'nova-senha',
        ));

        $response = $controller->updateOwnPassword($request);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('update_failed', $response->getData(true)['code']);
    }

    public function test_update_own_password_returns_success_when_service_succeeds()
    {
        $service = Mockery::mock(AuthService::class);
        $service->shouldReceive('currentUser')->once()->andReturn(array('id_usu' => 8));
        $service->shouldReceive('updatePasswordAndAccess')->once()->with(8, 'nova-senha')->andReturn(WriteResult::success());

        $controller = new AuthController($service);
        $request = Request::create('/password/update', 'POST', array(
            'id_usu' => 8,
            'senha_usu1' => 'nova-senha',
        ));

        $response = $controller->updateOwnPassword($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('updated', $response->getData(true)['code']);
    }
}
