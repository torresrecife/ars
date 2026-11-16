{{-- Botao "Reportar bug" do BVTarefas (o mesmo widget do Peticao Facil): captura a tela, deixa
     escrever o que aconteceu e cria a tarefa no projeto da ARS no BVTarefas.
     So aparece para quem esta logado e quando BUG_REPORTER_SCRIPT e BUG_REPORTER_TOKEN estao no .env. --}}
@php($bugReporter = config('services.bug_reporter'))
@if (!empty($bugReporter['script']) && !empty($bugReporter['token']) && auth()->check())
@php($bugSrc = $bugReporter['script'])
{{-- BVTarefas no mesmo servidor: carrega com o esquema da pagina (http ou https), senao o navegador
     bloqueia o script http numa pagina https e o envio sai de outra origem. --}}
@php($bugSrc = parse_url($bugSrc, PHP_URL_HOST) === request()->getHost() ? preg_replace('#^https?:#i', '', $bugSrc) : $bugSrc)
<script src="{{ $bugSrc }}"
        data-token="{{ $bugReporter['token'] }}"
        data-user-name="{{ auth()->user()->nome_usu }}"
        data-user-login="{{ auth()->user()->login_usu }}"
        data-user-level="{{ auth()->user()->nivel_usu }}" defer></script>
@endif
