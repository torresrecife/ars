<?php

namespace Tests\Unit;

use App\Http\Controllers\AndamentoAdminController;
use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AndamentoAdminService;
use App\Support\View;
use Mockery;
use Tests\TestCase;

class AndamentoAdminControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(AndamentoAdminService::class);
        $service->shouldReceive('editPayload')
            ->once()
            ->with(6)
            ->andReturn(array(
                'anda_id' => 6,
                'nome' => 'Ajuizamento',
                'chave' => 'AJUI',
                'tipos' => array('X', 'Y'),
            ));

        $controller = new AndamentoAdminController($service, app(View::class));
        $response = $controller->show(6);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(6, $response->getData(true)['data']['anda_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(AndamentoAdminService::class);
        $service->shouldReceive('create')->once()->andReturn('1');

        $controller = new AndamentoAdminController($service, app(View::class));
        $request = AndamentoStoreRequest::create('/admin/andamentos', 'POST', array(
            'nome' => 'Ajuizamento',
            'chave' => 'AJUI',
            'painel' => 'Y',
            'titulo' => 'Ajuizamento',
            'especie' => 1,
            'anda_neo' => 'X,Y',
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(AndamentoAdminService::class);
        $service->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['anda_id']) && (int) $input['anda_id'] === 6;
            }))
            ->andReturn('1');

        $controller = new AndamentoAdminController($service, app(View::class));
        $request = AndamentoUpdateRequest::create('/admin/andamentos/6', 'PUT', array(
            'nome' => 'Ajuizamento',
            'chave' => 'AJUI',
            'painel' => 'Y',
            'titulo' => 'Ajuizamento',
            'especie' => 1,
            'anda_neo' => 'X,Y',
        ));

        $response = $controller->update($request, 6);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(AndamentoAdminService::class);
        $service->shouldReceive('delete')->once()->with(6)->andReturn('1');

        $controller = new AndamentoAdminController($service, app(View::class));
        $response = $controller->destroy(6);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
