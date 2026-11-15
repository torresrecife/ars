<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Support\BVAcesso\BVAcessoClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BVAcessoLoginTest extends TestCase
{
    /** Resposta do /api/user do BVAcesso falso: [status, dados]; null = fora do ar. */
    private ?array $userResponse = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        // O phpunit.xml usa a subpasta de produção (/ars); aqui as requisições vão para a raiz.
        URL::forceRootUrl('http://localhost');
        config([
            'bvacesso.url' => 'http://acesso.test',
            'bvacesso.client_id' => 'cliente-1',
            'bvacesso.client_secret' => 'segredo',
            'bvacesso.local_login' => true,
        ]);
        Http::fake(function ($request) {
            if ($this->userResponse === null) {
                throw new ConnectionException('fora do ar');
            }
            if (str_ends_with($request->url(), '/oauth/token')) {
                return Http::response(['access_token' => 'tok', 'refresh_token' => 'ref', 'expires_in' => 3600]);
            }

            return Http::response($this->userResponse[1], $this->userResponse[0]);
        });
        // Página simples atrás do mesmo grupo das telas da ARS (as de verdade consultam o NEO).
        Route::middleware(['web', 'auth', 'bvacesso.check'])->get('/_teste-sso', fn () => 'ok:'.session('usuarioNivel'));
    }

    private function acesso(array $user = [], int $status = 200): void
    {
        $this->userResponse = [$status, $user + ['sub' => '7', 'name' => 'Ana Souza', 'email' => 'ana@bvaa.adv.br', 'username' => 'ana.souza', 'role' => 'USU']];
    }

    private function usuario(array $attributes = []): Usuario
    {
        $id = DB::table('usuarios')->insertGetId($attributes + [
            'nome_usu' => 'Ana',
            'login_usu' => 'ana.souza',
            'senha_usu' => Hash::make('Teste#123'),
            'email_usu' => 'antigo@bvaa.adv.br',
            'nivel_usu' => 'USU',
            'status_usu' => 'ATI',
            'id_setor' => 2,
            'id_cliente' => '5,8',
            'acesso_usu' => '2026-10-01 10:00:00',
        ], 'id_usu');

        return Usuario::findOrFail($id);
    }

    private function loginThroughAcesso()
    {
        $location = $this->get('/auth/bvacesso')->headers->get('Location');
        $this->assertStringStartsWith('http://acesso.test/oauth/authorize?', $location);
        parse_str(parse_url($location, PHP_URL_QUERY), $query);

        return $this->get('/auth/bvacesso/callback?code=abc&state='.$query['state']);
    }

    public function test_login_page_button_and_local_form(): void
    {
        $this->get('/login')->assertOk()->assertSee('Entrar com o BVAcesso')->assertSee('name="username"', false);
        config(['bvacesso.local_login' => false]);
        $this->get('/login')->assertRedirect('/auth/bvacesso');
        $this->get('/login?local=1')->assertOk()->assertDontSee('name="username"', false);
        $this->post('/login', ['username' => 'x', 'passwd' => 'x'])->assertNotFound();

        config(['bvacesso.client_id' => null]);
        $this->get('/login')->assertOk()->assertDontSee('Entrar com o BVAcesso')->assertSee('name="username"', false);
    }

    public function test_existing_user_is_linked_by_login_and_keeps_sector_and_clients(): void
    {
        $ana = $this->usuario(['must_change_password' => true, 'temporary_password_expires_at' => '2026-01-01 00:00:00']);
        $this->acesso(['role' => 'GER']);

        $this->loginThroughAcesso()->assertRedirect('/index');
        $this->assertAuthenticatedAs($ana);
        $ana->refresh();
        $this->assertSame('7', $ana->bvacesso_id);
        $this->assertSame('GER', $ana->nivel_usu);
        $this->assertSame('ana@bvaa.adv.br', $ana->email_usu);
        $this->assertSame('Ana Souza', $ana->nome_usu);
        $this->assertFalse($ana->must_change_password, 'Sem troca de senha local para quem entra pelo BVAcesso.');
        $this->assertNull($ana->temporary_password_expires_at);
        $this->assertSame(2, $ana->id_setor);
        $this->assertSame('5,8', $ana->id_cliente);
        $this->assertSame(2, session('usuarioSetor'));
        $this->assertSame('5,8', session('usuarioCliente'));
        $this->get('/_teste-sso')->assertOk()->assertSee('ok:GER');
    }

    public function test_user_is_linked_by_email_when_the_login_differs(): void
    {
        $ana = $this->usuario(['login_usu' => 'ana', 'email_usu' => 'Ana@BVAA.adv.br']);
        $this->acesso();

        $this->loginThroughAcesso()->assertRedirect('/index');
        $this->assertAuthenticatedAs($ana);
        $this->assertSame('7', $ana->refresh()->bvacesso_id);
    }

    public function test_person_not_registered_in_ars_gets_no_account(): void
    {
        $this->usuario(['login_usu' => 'outra', 'email_usu' => 'outra@bvaa.adv.br']);
        $this->acesso();

        $this->loginThroughAcesso()->assertRedirect('/login?local=1')
            ->assertSessionHasErrors(['username' => 'A sua conta não está cadastrada na ARS. Peça ao administrador.']);
        $this->assertGuest();
        $this->assertSame(1, Usuario::count());
    }

    public function test_account_linked_to_another_person_is_not_taken(): void
    {
        $this->usuario(['bvacesso_id' => '99']);
        $this->acesso();

        $this->loginThroughAcesso()->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_refusals(): void
    {
        $this->acesso([], 403);
        $this->loginThroughAcesso()->assertRedirect('/login?local=1')->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->usuario(['status_usu' => 'INA']);
        $this->acesso();
        $this->loginThroughAcesso()->assertSessionHasErrors(['username' => 'Sua conta está inativa na ARS. Fale com o administrador.']);
        $this->assertGuest();

        $this->get('/auth/bvacesso');
        $this->get('/auth/bvacesso/callback?code=abc&state=outro')->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_session_is_checked_and_logout_leaves_bvacesso(): void
    {
        $this->usuario();
        $this->acesso();
        $this->loginThroughAcesso();

        $this->travel(6)->minutes();
        $this->acesso(['role' => 'ADM']); // perfil trocado no BVAcesso: vale na próxima conferência
        $this->get('/_teste-sso')->assertOk()->assertSee('ok:ADM');

        $this->travel(6)->minutes();
        $this->userResponse = null; // fora do ar: continua
        $this->get('/_teste-sso')->assertOk();

        $this->travel(6)->minutes();
        $this->userResponse = [401, []];
        $this->get('/_teste-sso')->assertRedirect('/login?local=1');
        $this->assertGuest();

        $this->acesso();
        $this->loginThroughAcesso();
        $this->travel(6)->minutes();
        $this->acesso();
        DB::table('usuarios')->update(['status_usu' => 'INA']); // desativada na ARS
        $this->getJson('/_teste-sso')->assertStatus(401);
        $this->assertGuest();

        DB::table('usuarios')->update(['status_usu' => 'ATI']);
        $this->acesso();
        $this->loginThroughAcesso();
        $this->get('/logout')->assertRedirect('http://acesso.test/logout?return_to='.urlencode(url('login')));
        $this->assertGuest();
    }

    public function test_local_login_still_works_and_logout_stays_local(): void
    {
        $this->usuario();
        $this->post('/login', ['username' => 'ana.souza', 'passwd' => 'Teste#123'])->assertRedirect('/index');
        $this->get('/logout')->assertRedirect('/login');
    }

    public function test_export_command_writes_active_users_with_hashes(): void
    {
        $path = storage_path('app/bvacesso-teste.json');
        $this->usuario(['login_usu' => 'velho', 'email_usu' => 'velho@bvaa.adv.br', 'senha_usu' => md5('1234'), 'acesso_usu' => '2025-06-01']);
        $this->usuario(['login_usu' => 'md5', 'email_usu' => ' md5@bvaa.adv.br ', 'senha_usu' => strtoupper(md5('1234')), 'acesso_usu' => '2026-03-01', 'nivel_usu' => 'ADM']);
        $bcrypt = Hash::make('x');
        $this->usuario(['login_usu' => 'forte', 'email_usu' => 'forte@bvaa.adv.br', 'senha_usu' => $bcrypt, 'acesso_usu' => '2026-10-01']);
        $this->usuario(['login_usu' => 'inativo', 'email_usu' => 'inativo@bvaa.adv.br', 'status_usu' => 'INA']);

        $this->artisan('ars:exportar-bvacesso', ['--desde' => '2026-01-01', '--arquivo' => $path])->assertExitCode(0);
        $rows = collect(json_decode(file_get_contents($path), true))->keyBy('login');
        @unlink($path);

        $this->assertSame(['forte', 'md5'], $rows->keys()->all());
        $this->assertSame(md5('1234'), $rows['md5']['hash']);
        $this->assertSame('md5@bvaa.adv.br', $rows['md5']['email']);
        $this->assertSame('ADM', $rows['md5']['perfil']);
        $this->assertSame($bcrypt, $rows['forte']['hash']);
    }

    public function test_callback_address_never_carries_index_php(): void
    {
        URL::forceRootUrl('http://localhost/index.php');
        $client = app(BVAcessoClient::class);
        $this->assertSame('http://localhost/auth/bvacesso/callback', $client->redirectUri());
        parse_str(parse_url($client->authorizeUrl(), PHP_URL_QUERY), $query);
        $this->assertSame('http://localhost/auth/bvacesso/callback', $query['redirect_uri']);
    }

    public function test_logged_in_user_opening_login_again_goes_to_the_panel(): void
    {
        $user = $this->usuario();
        $this->actingAs($user)->get('/login')->assertRedirect('/index');
        $this->actingAs($user)->get('/auth/bvacesso')->assertRedirect('/index');
    }
}
