<?php

namespace Tests\Unit;

use App\Http\Controllers\UserAdminController;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserAdminService;
use App\Support\View;
use App\Support\WriteResult;
use Mockery;
use Tests\TestCase;

class UserAdminControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(UserAdminService::class);
        $service->shouldReceive('editPayload')
            ->once()
            ->with(7)
            ->andReturn(array(
                'id_usu' => 7,
                'nome_usu' => 'Maria',
                'login_usu' => 'maria',
            ));

        $controller = new UserAdminController($service, app(View::class));
        $response = $controller->show(7);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            array(
                'ok' => true,
                'code' => 'loaded',
                'message' => __('User loaded.'),
                'data' => array(
                    'id_usu' => 7,
                    'nome_usu' => 'Maria',
                    'login_usu' => 'maria',
                ),
            ),
            $response->getData(true)
        );
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(UserAdminService::class);
        $service->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['nome_usu'], $input['login_usu'], $input['email_usu'])
                    && $input['nome_usu'] === 'Maria'
                    && $input['login_usu'] === 'maria';
            }))
            ->andReturn(WriteResult::success());

        $controller = new UserAdminController($service, app(View::class));
        $request = UserStoreRequest::create('/***REMOVED***/usuarios', 'POST', array(
            'nome_usu' => 'Maria',
            'login_usu' => 'maria',
            'email_usu' => 'maria@example.com',
            'nivel_usu' => 'ADM',
            'setor_usu' => 1,
            'status_usu' => 'ATI',
            'regiao_modo' => 'N',
            'banco_neo' => '1,2',
            'regiao_neo' => '1,2',
            'senha_usu1' => 'abcd',
            'senha_usu2' => 'abcd',
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(UserAdminService::class);
        $service->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['id_usu']) && (int) $input['id_usu'] === 9;
            }))
            ->andReturn(WriteResult::success());

        $controller = new UserAdminController($service, app(View::class));
        $request = UserUpdateRequest::create('/***REMOVED***/usuarios/9', 'PUT', array(
            'nome_usu' => 'Maria',
            'login_usu' => 'maria',
            'email_usu' => 'maria@example.com',
            'nivel_usu' => 'ADM',
            'setor_usu' => 1,
            'status_usu' => 'ATI',
            'regiao_modo' => 'N',
        ));

        $response = $controller->update($request, 9);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(UserAdminService::class);
        $service->shouldReceive('delete')
            ->once()
            ->with(9)
            ->andReturn(WriteResult::success());

        $controller = new UserAdminController($service, app(View::class));
        $response = $controller->destroy(9);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
