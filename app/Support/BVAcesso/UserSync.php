<?php

namespace App\Support\BVAcesso;

use App\Models\Usuario;
use Illuminate\Support\Str;

/**
 * ARS: liga a pessoa do BVAcesso a um usuário que já existe aqui (pelo ID do BVAcesso, pelo login
 * ou pelo e-mail) e aplica o perfil (ADM, GER ou USU no BVAcesso). Setor, clientes e regiões
 * continuam sendo definidos na ARS, em Administração → Usuários.
 *
 * Não cria conta: na ARS um usuário sem setor e sem cliente vê os dados de todos (decisão do
 * Fabio em 15/11). Quem não está cadastrado aqui recebe aviso para pedir ao administrador.
 */
class UserSync
{
    public const LEVELS = ['ADM', 'GER', 'USU'];

    public function find(array $info): ?Usuario
    {
        $sub = (string) $info['sub'];
        $user = Usuario::where('bvacesso_id', $sub)->first();
        if ($user) {
            return $user;
        }

        $candidates = [];
        if (filled($info['username'] ?? null)) {
            $candidates[] = Usuario::where('login_usu', $info['username'])->first();
        }
        if (filled($info['email'] ?? null)) {
            $candidates[] = Usuario::whereRaw('lower(email_usu) = ?', [Str::lower($info['email'])])
                ->orderByRaw("case when status_usu = 'ATI' then 0 else 1 end")
                ->orderByDesc('acesso_usu')
                ->first();
        }

        // Conta já ligada a outra pessoa do BVAcesso não é tomada.
        foreach ($candidates as $candidate) {
            if ($candidate && blank($candidate->bvacesso_id)) {
                return $candidate;
            }
        }

        return null;
    }

    /** Liga e atualiza o usuário; null se ele não existe na ARS. */
    public function sync(array $info): ?Usuario
    {
        $user = $this->find($info);
        if (! $user) {
            return null;
        }

        $user->forceFill([
            'bvacesso_id' => (string) $info['sub'],
            'nome_usu' => mb_substr((string) $info['name'], 0, 50),
            'email_usu' => mb_substr((string) $info['email'], 0, 50),
            // A senha agora é a do BVAcesso: nada de troca obrigatória aqui.
            'must_change_password' => false,
            'temporary_password_expires_at' => null,
        ]);
        if (in_array($info['role'] ?? null, self::LEVELS, true)) {
            $user->nivel_usu = $info['role'];
        }
        $user->save();

        return $user;
    }
}
