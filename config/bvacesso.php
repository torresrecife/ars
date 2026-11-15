<?php

/*
 * Login único pelo BVAcesso. Com BVACESSO_CLIENT_ID vazio, o sistema usa só o login próprio.
 * O ID e o segredo saem do BVAcesso: Administração → Sistemas → (este sistema).
 */
return [
    'url' => rtrim((string) env('BVACESSO_URL', ''), '/'),
    // Endereço que o servidor usa para falar com o BVAcesso (troca do código, conferências).
    // Vazio = o mesmo do BVACESSO_URL.
    'internal_url' => rtrim((string) env('BVACESSO_INTERNAL_URL', ''), '/'),
    'client_id' => env('BVACESSO_CLIENT_ID'),
    'client_secret' => env('BVACESSO_CLIENT_SECRET'),

    // De quantos em quantos minutos o sistema confere se a pessoa ainda tem acesso
    // (conta desativada, acesso retirado ou saída no BVAcesso desconectam aqui).
    'check_minutes' => (int) env('BVACESSO_CHECK_MINUTES', 5),

    // Manter o formulário de login próprio ao lado do botão do BVAcesso (período de transição).
    // false = a tela de login manda direto para o BVAcesso.
    'local_login' => (bool) env('BVACESSO_LOCAL_LOGIN', true),
];
