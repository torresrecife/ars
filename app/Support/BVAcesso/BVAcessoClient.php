<?php

namespace App\Support\BVAcesso;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Conversa com o BVAcesso (OAuth2, código de autorização com PKCE).
 * Mesmo arquivo em todos os sistemas; o que muda por sistema fica no UserSync.
 */
class BVAcessoClient
{
    public static function enabled(): bool
    {
        return filled(config('bvacesso.url')) && filled(config('bvacesso.client_id')) && filled(config('bvacesso.client_secret'));
    }

    public function redirectUri(): string
    {
        // Quem abriu o sistema por um endereço com /index.php (favoritos do sistema antigo) geraria o
        // retorno com ele, e o BVAcesso só aceita o endereço cadastrado ("Client authentication failed").
        return preg_replace('#/index\.php(?=/)#', '', route('bvacesso.callback'), 1);
    }

    /** Endereço de login no BVAcesso; guarda state e verificador PKCE na sessão. */
    public function authorizeUrl(): string
    {
        $state = Str::random(40);
        $verifier = Str::random(64);
        session()->put('bvacesso.oauth', ['state' => $state, 'verifier' => $verifier]);

        return config('bvacesso.url').'/oauth/authorize?'.http_build_query([
            'client_id' => config('bvacesso.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
            'code_challenge_method' => 'S256',
        ]);
    }

    /** Sair do BVAcesso também (todos os sistemas) e voltar para $returnTo. */
    public function logoutUrl(string $returnTo): string
    {
        return config('bvacesso.url').'/logout?'.http_build_query(['return_to' => $returnTo]);
    }

    /** @return array{access_token: string, refresh_token: string, expires_in: int} */
    public function exchangeCode(string $code, string $verifier): array
    {
        return $this->token([
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri(),
            'code' => $code,
            'code_verifier' => $verifier,
        ])->throw()->json();
    }

    /** Novo token a partir do refresh token; null se o BVAcesso recusou (sessão encerrada lá). */
    public function refresh(string $refreshToken): ?array
    {
        $response = $this->token(['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken]);

        return $response->successful() ? $response->json() : null;
    }

    /** Dados da pessoa para este sistema (sub, name, email, username, role). */
    public function user(string $accessToken): Response
    {
        return $this->http()->withToken($accessToken)->get($this->internalUrl().'/api/user');
    }

    /**
     * Confere a sessão guardada (renovando o token se preciso).
     * Devolve os dados da pessoa, false se o acesso acabou, ou null se o BVAcesso não respondeu.
     */
    public function check(array &$session): array|false|null
    {
        try {
            if (($session['expires_at'] ?? 0) <= now()->getTimestamp() + 30) {
                $tokens = $this->refresh($session['refresh_token'] ?? '');
                if (! $tokens) {
                    return false;
                }
                $session = self::sessionFrom($tokens);
            }
            $response = $this->user($session['access_token']);
        } catch (ConnectionException) {
            return null;
        }
        if (in_array($response->status(), [401, 403], true)) {
            return false;
        }

        return $response->successful() ? $response->json() : null;
    }

    public static function sessionFrom(array $tokens): array
    {
        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? null,
            'expires_at' => now()->getTimestamp() + (int) ($tokens['expires_in'] ?? 3600),
            'checked_at' => now()->getTimestamp(),
        ];
    }

    private function token(array $params): Response
    {
        return $this->http()->asForm()->post($this->internalUrl().'/oauth/token', $params + [
            'client_id' => config('bvacesso.client_id'),
            'client_secret' => config('bvacesso.client_secret'),
        ]);
    }

    private function http()
    {
        return Http::acceptJson()->timeout(10)->connectTimeout(5);
    }

    private function internalUrl(): string
    {
        return config('bvacesso.internal_url') ?: config('bvacesso.url');
    }
}
