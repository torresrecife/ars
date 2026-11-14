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
    /**
     * Desde 26/10/2026 o gerente lista e edita os cadastros do sistema;
     * criar e excluir continuam so com o administrador.
     */
    public function test_manager_can_view_and_update_but_not_create_or_delete_system_admin_resources()
    {
        $user = new Usuario(array(
            'id_usu' => 2,
            'nome_usu' => 'Gerente',
            'login_usu' => 'gerente',
            'nivel_usu' => 'GER',
        ));

        foreach (array(Usuario::class, Area::class, Banco::class, Andamento::class, Semana::class, Regiao::class) as $resource) {
            $this->assertTrue(Gate::forUser($user)->allows('viewAny', $resource), $resource . ' viewAny');
            $this->assertTrue(Gate::forUser($user)->allows('update', $resource), $resource . ' update');
            $this->assertFalse(Gate::forUser($user)->allows('create', $resource), $resource . ' create');
            $this->assertFalse(Gate::forUser($user)->allows('delete', $resource), $resource . ' delete');
        }
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

    public function test_common_user_cannot_access_admin_gate()
    {
        $user = new Usuario(array(
            'id_usu' => 4,
            'nome_usu' => 'Usuario',
            'login_usu' => 'usuario',
            'nivel_usu' => 'USU',
        ));

        $this->assertFalse(Gate::forUser($user)->allows('access-admin'));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', MetaAndamento::class));
    }

    public function test_admin_can_manage_all_admin_resources()
    {
        $user = new Usuario(array(
            'id_usu' => 1,
            'nome_usu' => 'Admin',
            'login_usu' => 'admin',
            'nivel_usu' => 'ADM',
        ));

        $this->assertTrue(Gate::forUser($user)->allows('access-admin'));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Usuario::class));
        $this->assertTrue(Gate::forUser($user)->allows('create', Banco::class));
        $this->assertTrue(Gate::forUser($user)->allows('update', Regiao::class));
        $this->assertTrue(Gate::forUser($user)->allows('delete', Semana::class));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', MetaAndamento::class));
    }
}
