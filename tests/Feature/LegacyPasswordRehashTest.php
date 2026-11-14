<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * O Laravel 11+ regrava o hash da senha no login (rehash_on_login) na coluna
 * de getAuthPasswordName(). Sem ela, o login de quem ainda tem senha MD5 do
 * legado tentava gravar numa coluna "password" que nao existe.
 */
class LegacyPasswordRehashTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', array(
            '--path' => 'database/migrations/2026_07_15_000200_create_usuarios_table.php',
        ));
        $this->artisan('migrate', array(
            '--path' => 'database/migrations/2026_10_26_000100_add_temporary_password_fields_to_usuarios_table.php',
        ));
    }

    public function test_password_column_is_senha_usu()
    {
        $this->assertSame('senha_usu', (new Usuario())->getAuthPasswordName());
    }

    public function test_md5_password_logs_in_and_becomes_bcrypt()
    {
        $id = DB::table('usuarios')->insertGetId(array(
            'nome_usu' => 'Legado',
            'login_usu' => 'legado',
            'senha_usu' => md5('Teste#123'),
            'nivel_usu' => 'USU',
            'status_usu' => 'ATI',
        ), 'id_usu');

        $this->assertTrue(Auth::guard('web')->attempt(array('login_usu' => 'legado', 'password' => 'Teste#123')));

        $hash = DB::table('usuarios')->where('id_usu', $id)->value('senha_usu');
        $this->assertStringStartsWith('$2y$', $hash);
        $this->assertTrue(Hash::check('Teste#123', $hash));
    }
}
