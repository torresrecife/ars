<div style="max-width:720px;margin:40px auto;padding:32px;background:#fff;border:1px solid #dbe4ef;border-radius:12px;font-family:Arial,sans-serif;color:#203a5b;">
    <h1>Senha temporária gerada</h1>
    <p>Entregue esta senha ao usuário com segurança. Ela ficará válida por 24 horas e exigirá a troca no próximo acesso.</p>
    <p style="font-size:24px;padding:18px;background:#f0f5fa;border-radius:8px;"><code>{{ $temporaryPassword }}</code></p>
    <p>Validade até: {{ date('d/m/Y H:i', strtotime($expiresAt)) }}</p>
    <a href="{{ url('usuarios') }}">Voltar para usuários</a>
</div>
