<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Tests\TestCase;

/** Botão "Reportar bug" do BVTarefas (mesmo widget do Petição Fácil). */
class BugReporterWidgetTest extends TestCase
{
    private function usuario(): Usuario
    {
        return (new Usuario())->forceFill([
            'id_usu' => 7,
            'nome_usu' => 'Maria "Teste"',
            'login_usu' => 'maria.teste',
            'nivel_usu' => 'USU',
        ]);
    }

    private function render(string $url = 'http://localhost/ars/index'): string
    {
        $this->app->instance('request', Request::create($url));

        return view('partials.bug-reporter')->render();
    }

    public function test_logged_user_gets_the_script_with_identity_when_configured(): void
    {
        config(['services.bug_reporter' => ['script' => 'http://bvtarefas.test/widget/bug-reporter.js', 'token' => 'token-de-teste']]);
        $this->actingAs($this->usuario());

        $html = $this->render();

        $this->assertStringContainsString('src="http://bvtarefas.test/widget/bug-reporter.js"', $html);
        $this->assertStringContainsString('data-token="token-de-teste"', $html);
        $this->assertStringContainsString('data-user-name="Maria &quot;Teste&quot;"', $html);
        $this->assertStringContainsString('data-user-login="maria.teste"', $html);
        $this->assertStringContainsString('data-user-level="USU"', $html);
    }

    public function test_script_on_the_same_server_follows_the_page_scheme(): void
    {
        config(['services.bug_reporter' => ['script' => 'http://localhost/bvtarefas/widget/bug-reporter.js', 'token' => 'x']]);
        $this->actingAs($this->usuario());

        $this->assertStringContainsString('src="//localhost/bvtarefas/widget/bug-reporter.js"', $this->render());
        $this->assertStringContainsString('src="//localhost/bvtarefas/widget/bug-reporter.js"', $this->render('https://localhost/ars/index'));
    }

    public function test_no_script_when_not_configured_or_logged_out(): void
    {
        config(['services.bug_reporter' => ['script' => 'http://bvtarefas.test/widget/bug-reporter.js', 'token' => null]]);
        $this->actingAs($this->usuario());
        $this->assertStringNotContainsString('bug-reporter.js', $this->render());

        config(['services.bug_reporter' => ['script' => 'http://bvtarefas.test/widget/bug-reporter.js', 'token' => 'x']]);
        auth()->logout();
        $this->assertStringNotContainsString('bug-reporter.js', $this->render());
    }

    public function test_logged_in_pages_include_the_button_and_login_does_not(): void
    {
        foreach (['index/shell', 'dados_anda/index', 'dados_fatur/index'] as $view) {
            $this->assertStringContainsString("@include('partials.bug-reporter')", file_get_contents(resource_path("views/{$view}.blade.php")), $view);
        }
        $this->assertStringNotContainsString('bug-reporter', file_get_contents(resource_path('views/auth/login.blade.php')));
    }
}
