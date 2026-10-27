<div class="admin-page admin-page--flat ars-profile-page">
    <div class="admin-page__toolbar admin-page__toolbar--between">
        <div><h1 class="admin-page__title">Meu perfil</h1><p class="admin-page__subtitle">Consulte seus dados e mantenha sua senha atualizada.</p></div>
    </div>
    <div class="admin-surface ars-profile-card">
        <div class="ars-profile-card__heading"><span class="ars-profile-card__avatar">{{ strtoupper(substr((string) $user['nome_usu'], 0, 1)) }}</span><div><h2>{{ $user['nome_usu'] }}</h2><p>{{ $user['login_usu'] }}</p></div></div>
        <div class="ars-profile-grid">
            <div><span>Nome</span><strong>{{ $user['nome_usu'] }}</strong></div><div><span>Usuário</span><strong>{{ $user['login_usu'] }}</strong></div>
            <div><span>E-mail</span><strong>{{ $user['email_usu'] ?: 'Não informado' }}</strong></div><div><span>Nível</span><strong>{{ $user['nivel_usu'] }}</strong></div>
            <div><span>Setor</span><strong>{{ $user['id_setor'] ?: 'Todos' }}</strong></div><div><span>Status</span><strong>{{ $user['status_usu'] }}</strong></div>
        </div>
        <p class="ars-profile-readonly">Dados administrativos como setor, clientes, nível e status são gerenciados pelos responsáveis do sistema.</p>
    </div>
    <div class="admin-surface ars-profile-password-card">
        <div><h2>Alterar senha</h2><p>Escolha uma nova senha pessoal para acessar a ARS.</p></div>
        <form id="profile-password-form" class="admin-form" novalidate>
            @csrf <input type="hidden" name="id_usu" value="{{ (int) $user['id_usu'] }}">
            <div class="admin-form-subgrid"><div class="admin-form-group"><label for="profile-password">Nova senha</label><input type="password" class="admin-form-input" id="profile-password" name="senha_usu1" minlength="4" required></div><div class="admin-form-group"><label for="profile-password-confirm">Repita a nova senha</label><input type="password" class="admin-form-input" id="profile-password-confirm" name="senha_usu2" minlength="4" required></div></div>
            <div id="profile-password-message" class="force-password-message" role="alert"></div><div class="admin-form-actions"><button type="submit" class="admin-button admin-button--primary">Salvar nova senha</button></div>
        </form>
    </div>
</div>
<style>
.ars-profile-page{max-width:980px}.ars-profile-card,.ars-profile-password-card{max-width:900px;padding:28px}.ars-profile-card__heading{display:flex;align-items:center;gap:14px;margin-bottom:24px}.ars-profile-card__avatar{display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#e8f1fa;color:#285983;font-size:22px;font-weight:700}.ars-profile-card__heading h2,.ars-profile-password-card h2{margin:0 0 4px;color:#203a5b;font-size:21px}.ars-profile-card__heading p,.ars-profile-password-card p{margin:0;color:#6b7e91;font-size:14px}.ars-profile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.ars-profile-grid div{display:flex;flex-direction:column;gap:5px;padding:13px 15px;border:1px solid #e0e8f0;border-radius:8px;background:#f8fafc}.ars-profile-grid span{color:#6b7e91;font-size:12px}.ars-profile-grid strong{color:#263f5d;font-size:15px;font-weight:600}.ars-profile-readonly{margin:20px 0 0;padding-top:16px;border-top:1px solid #e1e8ef;color:#718294;font-size:13px}.ars-profile-password-card{display:flex;flex-direction:column;gap:22px}.ars-profile-password-card .admin-form{width:100%}@media(max-width:700px){.ars-profile-grid{grid-template-columns:1fr}.ars-profile-card,.ars-profile-password-card{padding:20px}}
</style>
<script>
document.getElementById('profile-password-form').addEventListener('submit',function(event){event.preventDefault();var form=event.currentTarget,message=document.getElementById('profile-password-message'),first=document.getElementById('profile-password').value,second=document.getElementById('profile-password-confirm').value;message.className='force-password-message';message.textContent='';if(!first||first.length<4){message.textContent='Informe uma senha com pelo menos 4 caracteres.';return}if(first!==second){message.textContent='As senhas não coincidem.';return}var button=form.querySelector('button[type="submit"]');button.disabled=true;fetch('{{ url('ajax/newpass') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:new URLSearchParams(new FormData(form))}).then(function(r){return r.json().then(function(d){return{ok:r.ok,data:d}})}).then(function(result){if(!result.ok||!result.data.ok)throw new Error(result.data.message||'Não foi possível alterar a senha.');message.className='force-password-message is-success';message.textContent='Senha alterada com sucesso.';form.reset()}).catch(function(error){message.textContent=error.message;button.disabled=false})});
</script>
