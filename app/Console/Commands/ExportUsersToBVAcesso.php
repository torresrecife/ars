<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;

/**
 * Exporta as contas ativas para o login único (BVAcesso), que importa com
 * "php artisan bvacesso:importar-usuarios <arquivo> ars".
 *
 * O arquivo tem as senhas no formato guardado aqui (hash, não o texto): fica em storage/app
 * só com leitura do dono. Apague depois da importação.
 */
class ExportUsersToBVAcesso extends Command
{
    protected $signature = 'ars:exportar-bvacesso
        {--desde= : só quem entrou a partir desta data (AAAA-MM-DD)}
        {--arquivo= : caminho do arquivo (padrão: storage/app/bvacesso-usuarios.json)}';

    protected $description = 'Exporta os usuários ativos para importar no BVAcesso';

    public function handle(): int
    {
        $since = $this->option('desde');
        if ($since !== null && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $since)) {
            $this->error('Use --desde=AAAA-MM-DD.');

            return self::FAILURE;
        }

        $users = Usuario::where('status_usu', 'ATI')
            ->when($since, fn ($q) => $q->where('acesso_usu', '>=', $since))
            ->orderBy('login_usu')
            ->get();

        $rows = $users->map(fn (Usuario $u) => [
            'login' => (string) $u->login_usu,
            'nome' => (string) $u->nome_usu,
            'email' => trim((string) $u->email_usu),
            'perfil' => (string) $u->nivel_usu,
            'hash' => $this->hash((string) $u->senha_usu),
        ])->values();

        $path = $this->option('arquivo') ?: storage_path('app/bvacesso-usuarios.json');
        $old = umask(0077);
        file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        umask($old);
        @chmod($path, 0600);

        $this->info($rows->count().' conta(s) ativa(s)'.($since ? " com entrada desde {$since}" : '').' exportada(s) para '.$path);
        $this->line('No BVAcesso: php artisan bvacesso:importar-usuarios '.$path.' ars   (depois, apague o arquivo)');

        return self::SUCCESS;
    }

    /** bcrypt/argon se houver; senão o MD5 do legado (o BVAcesso aceita uma vez e pede outra). */
    private function hash(string $hash): ?string
    {
        if (preg_match('/^\$(2[ayb]|argon2i|argon2id)\$/', $hash)) {
            return $hash;
        }
        if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            return strtolower($hash);
        }

        return null;
    }
}
