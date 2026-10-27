@php($isForced = isset($isForced) ? (bool) $isForced : true)
<div class="admin-page admin-page--flat force-password-page">
    <div class="admin-page__toolbar admin-page__toolbar--between">
        <div>
            <h1 class="admin-page__title">{{ $isForced ? 'Crie sua nova senha' : 'Alterar minha senha' }}</h1>
            <p class="admin-page__subtitle">{{ $isForced ? 'Por segurança, a senha temporária precisa ser substituída antes de continuar.' : 'Atualize sua senha de acesso à ARS.' }}</p>
        </div>
        <a href="{{ url('logout') }}" class="admin-button admin-button--secondary">Sair</a>
    </div>

    <div class="admin-surface force-password-card">
        <div class="force-password-card__intro">
            <span class="force-password-card__icon" aria-hidden="true">✓</span>
            <div>
                <h2>{{ $isForced ? 'Alteração de senha obrigatória' : 'Senha de acesso' }}</h2>
                <p>{{ $isForced ? 'Escolha uma senha pessoal para acessar a ARS. A senha temporária não poderá ser usada novamente.' : 'Escolha uma senha pessoal e não compartilhe sua credencial.' }}</p>
            </div>
        </div>

        <form id="force-password-form" class="admin-form" novalidate>
            @csrf
            <input type="hidden" name="id_usu" value="{{ (int) $userId }}">
            <div class="admin-form-subgrid">
                <div class="admin-form-group">
                    <label for="senha_usu1">Nova senha</label>
                    <input type="password" class="admin-form-input" name="senha_usu1" id="senha_usu1" autocomplete="new-password" minlength="4" required>
                </div>
                <div class="admin-form-group">
                    <label for="senha_usu2">Repita a nova senha</label>
                    <input type="password" class="admin-form-input" name="senha_usu2" id="senha_usu2" autocomplete="new-password" minlength="4" required>
                </div>
            </div>
            <div id="force-password-message" class="force-password-message" role="alert" aria-live="polite"></div>
            <div class="admin-form-actions">
                <button type="submit" class="admin-button admin-button--primary">Salvar nova senha</button>
                <a href="{{ url('logout') }}" class="admin-button admin-button--secondary">Sair</a>
            </div>
        </form>
    </div>
</div>
<style>
.force-password-page { max-width: 980px; }
.force-password-card { max-width: 820px; padding: 30px; }
.force-password-card__intro { display:flex; align-items:flex-start; gap:16px; margin-bottom:28px; }
.force-password-card__icon { display:flex; align-items:center; justify-content:center; flex:0 0 42px; width:42px; height:42px; border-radius:50%; background:#e8f5ee; color:#237348; font-size:22px; font-weight:700; }
.force-password-card h2 { margin:0 0 7px; color:#203a5b; font-size:21px; }
.force-password-card__intro p { margin:0; color:#607286; font-size:14px; line-height:1.5; }
.force-password-message { min-height:22px; margin-top:16px; color:#a03c32; font-size:13px; }
.force-password-message.is-success { color:#237348; }
@media (max-width: 700px) { .force-password-card { padding:20px; } }
</style>
<script>
document.getElementById('force-password-form').addEventListener('submit', function (event) {
    event.preventDefault();
    var form = event.currentTarget;
    var message = document.getElementById('force-password-message');
    var first = document.getElementById('senha_usu1').value;
    var second = document.getElementById('senha_usu2').value;
    message.className = 'force-password-message';
    message.textContent = '';
    if (!first || first.length < 4) { message.textContent = 'Informe uma senha com pelo menos 4 caracteres.'; return; }
    if (first !== second) { message.textContent = 'As senhas não coincidem.'; return; }
    var button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    fetch('{{ url('ajax/newpass') }}', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}, body: new URLSearchParams(new FormData(form)) })
        .then(function (response) { return response.json().then(function (data) { return {ok: response.ok, data: data}; }); })
        .then(function (result) {
            if (!result.ok || !result.data.ok) throw new Error(result.data.message || 'Não foi possível alterar a senha.');
            message.className = 'force-password-message is-success';
            message.textContent = 'Senha alterada com sucesso. Redirecionando...';
            window.setTimeout(function () { window.location.href = '{{ url('index') }}'; }, 900);
        })
        .catch(function (error) { message.textContent = error.message; button.disabled = false; });
});
</script>
