<style>
    .usuario-temporary-page { max-width: 920px; }
    .usuario-temporary-page .admin-page__title { font-size: 24px; line-height: 1.2; }
    .usuario-temporary-page .admin-page__subtitle { font-size: 15px; }
    .usuario-temporary-card {
        display: flex;
        align-items: flex-start;
        gap: 22px;
        padding: 30px;
        background: #fff;
        border: 1px solid #d8e3ed;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(31, 55, 80, .06);
    }
    .usuario-temporary-card__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e8f5ee;
        color: #237348;
        font-size: 27px;
        font-weight: 700;
    }
    .usuario-temporary-card__body { flex: 1; min-width: 0; }
    .usuario-temporary-card__body p { font-size: 15px; line-height: 1.55; }
    .usuario-temporary-card__notice { margin: 0 0 22px; color: #536a80; }
    .usuario-temporary-card__credential {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 22px;
        background: #f1f6fb;
        border: 1px solid #c9d9e8;
        border-radius: 9px;
    }
    .usuario-temporary-card__credential span { color: #405b73; font-size: 15px; font-weight: 700; }
    .usuario-temporary-card__credential code { color: #173a61; font-size: 20px; font-weight: 700; letter-spacing: .04em; word-break: break-all; }
    .usuario-temporary-card__meta { display: flex; flex-wrap: wrap; gap: 30px; margin-top: 18px; color: #405b73; font-size: 15px; }
    .usuario-temporary-card__warning { margin: 22px 0 0; padding-top: 16px; border-top: 1px solid #e1e8ef; color: #80672d; font-size: 14px !important; }
    @media (max-width: 700px) {
        .usuario-temporary-card { padding: 20px; }
        .usuario-temporary-card__credential { align-items: flex-start; flex-direction: column; gap: 10px; }
    }
</style>
<div class="admin-page admin-page--flat usuario-temporary-page">
    <div class="admin-page__toolbar admin-page__toolbar--between">
        <div>
            <h1 class="admin-page__title">Senha temporária gerada</h1>
            <p class="admin-page__subtitle">Copie a senha e entregue-a ao usuário por um canal seguro.</p>
        </div>
        <a href="{{ url('usuarios') }}" class="admin-button admin-button--secondary">Voltar aos usuários</a>
    </div>
    <div class="usuario-temporary-card">
        <div class="usuario-temporary-card__icon" aria-hidden="true">✓</div>
        <div class="usuario-temporary-card__body">
            <p class="usuario-temporary-card__notice">A senha anterior foi invalidada. O usuário deverá criar uma nova senha no próximo acesso.</p>
            <div class="usuario-temporary-card__credential">
                <span>Senha temporária</span>
                <code>{{ $temporaryPassword }}</code>
            </div>
            <div class="usuario-temporary-card__meta">
                <span>Validade: <strong>24 horas</strong></span>
                <span>Expira em: <strong>{{ date('d/m/Y H:i', strtotime($expiresAt)) }}</strong></span>
            </div>
            <p class="usuario-temporary-card__warning">Por segurança, essa senha não poderá ser consultada novamente depois que você sair desta tela.</p>
        </div>
    </div>
</div>
