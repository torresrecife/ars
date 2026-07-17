<?php

namespace Tests\Feature;

use App\Models\Andamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\MetaAndamento;
use App\Models\Regiao;
use App\Models\Semana;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    public function test_manager_cannot_manage_system_***REMOVED***_resources()
    {
        $user = new Usuario(array(
            'id_usu' => 2,
            'nome_usu' => 'Gerente',
            'login_usu' => 'gerente',
            'nivel_usu' => 'GER',
        ));

        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Usuario::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Area::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Banco::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Andamento::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Semana::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Regiao::class));
    }

    public function test_manager_can_manage_meta_resource()
    {
        $user = new Usuario(array(
            'id_usu' => 3,
            'nome_usu' => 'Gerente',
            'login_usu' => 'gerente2',
            'nivel_usu' => 'GER',
        ));

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', MetaAndamento::class));
        $this->assertTrue(Gate::forUser($user)->allows('create', MetaAndamento::class));
        $this->assertTrue(Gate::forUser($user)->allows('update', MetaAndamento::class));
        $this->assertTrue(Gate::forUser($user)->allows('delete', MetaAndamento::class));
    }

    public function test_common_user_cannot_access_***REMOVED***_gate()
    {
        $user = new Usuario(array(
            'id_usu' => 4,
            'nome_usu' => 'Usuario',
            'login_usu' => 'usuario',
            'nivel_usu' => 'USU',
        ));

        $this->assertFalse(Gate::forUser($user)->allows('access-***REMOVED***'));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', MetaAndamento::class));
    }

    public function test_***REMOVED***_can_manage_all_***REMOVED***_resources()
    {
        $user = new Usuario(array(
            'id_usu' => 1,
            'nome_usu' => 'Admin',
            'login_usu' => '***REMOVED***',
            'nivel_usu' => 'ADM',
        ));

        $this->assertTrue(Gate::forUser($user)->allows('access-***REMOVED***'));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Usuario::class));
        $this->assertTrue(Gate::forUser($user)->allows('create', Banco::class));
        $this->assertTrue(Gate::forUser($user)->allows('update', Regiao::class));
        $this->assertTrue(Gate::forUser($user)->allows('delete', Semana::class));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', MetaAndamento::class));
    }
}
