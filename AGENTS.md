# ARS — contexto para agentes de IA

Acompanhamento de resultados por setor: painéis de produção por cliente/carteira/região/área,
relatórios mensal e semanal, metas e cadastros (usuários, clientes, setores, regiões, semanas,
andamentos). Migrado de PHP puro para Laravel. Este arquivo (`AGENTS.md`) é lido pelos agentes de
código (Codex, Cursor, Gemini…); o `CLAUDE.md` só importa este arquivo para o Claude Code. Leia o
README para estrutura, rotas e traduções.

## Stack e ambiente
- **Branch `ars_php8` (desde 14/11/2026): Laravel 13, PHP 8.5, MySQL 8.4, banco em utf8mb4**, igual
  aos projetos irmãos (`~/projetos/peticaofacil`, `bvtarefas`, `bvacesso`). Composer `platform.php`
  8.5.0. A `ars_laravel` ficou em Laravel 6.20 / PHP 7.4 / MariaDB 10.6 / latin1.
- Estrutura antiga mantida (como o Petição Fácil): `app/Http/Kernel.php`, `Exceptions/Handler.php`,
  `RouteServiceProvider` com namespace de controllers (rotas em string `Controller@metodo`).
- Front: Blade + jQuery 1.8/jQuery UI (módulos em `public/js/modules`, globais, servidos como estão,
  versão pela data do arquivo) + Sass em `resources/sass` compilado pelo **Vite** (`@vite` no
  `index/shell.blade.php`; sem build, cai no `public/css/ars-modern.css`). Não há Tailwind (visual
  novo fica para outra fase). Estáticos em `public/css`, `public/js`, `public/img`.
- Dois bancos: MySQL (dados da ARS) e **NEO** (SQL Server, réplica `Neo_Juridico_replica`, só
  leitura) pelas funções `sqlsrv_*` com driver ODBC 18 (`SQLSRV_DB_TRUST_SERVER_CERTIFICATE=true`:
  certificado autoassinado, decisão do Fabio em 08/11 para o Petição Fácil). Acesso ao NEO só pela
  camada `NeoSqlsrvExecutor`/`Neo*Repository`/`SqlsrvLookupRepository`.
- Login sobre a tabela `usuarios` (provider `legacy-eloquent`, `App\Auth\LegacyUsuarioProvider`):
  aceita hash MD5 do legado. `Usuario::getAuthPasswordName()` = `senha_usu` é obrigatório: o
  Laravel 11+ regrava o hash no login (`rehash_on_login`) nessa coluna (MD5 vira bcrypt).
  Perfis: `ADM`, `GER`, `USU` (policies `SystemAdminPolicy`, `MetaAdminPolicy`, gate `access-admin`).
- Idioma pelo `.env` (`APP_LOCALE=pt_BR`, fallback `en_CA`); chaves em inglês em `lang/*.json`.
- **Local: DDEV** no WSL (`~/projetos/ars`), `http://ars.ddev.site:8080`, nginx-fpm, banco `db`.
  Driver do SQL Server no `.ddev/web-build/Dockerfile.sqlsrv`. Comandos via `ddev artisan …`,
  `ddev composer …`, `ddev npm …`. Banco local = cópia de 10/2026 (MariaDB → MySQL 8.4 em 14/11,
  dump em `.ddev/db_dumps/`, fora do Git). `ddev importar-producao` traz a produção do REIA-HD (o usuário só leitura do PC ainda precisa de `SELECT` no banco `ars`).
- **Produção (desde 15/11/2026):** EUROPA-HD `http://172.31.20.60/ars`, subpasta do site do Petição
  Fácil (`ASSET_URL`/`SESSION_PATH=/ars`), código em `/var/www/ars` (branch `ars_php8`); banco `ars`
  no REIA-HD `172.31.20.62` (MySQL 8.4). Roteiro em `deploy/README.md`; atualizar com
  `./deploy/atualizar.sh`; backup diário pelo `crontab` do `fabio.ferreira` (`/var/backups/ars`).
  Comandos nos servidores ficam com o Fabio (Bitdefender intercepta o SSH).
- **Servidor antigo `10.81.11.202`:** `/ars/...` redireciona (301) para o EUROPA-HD pela regra
  `deploy/apache-servidor-antigo.conf` (`/etc/apache2/conf-available/ars-redirect.conf`). Banco
  `ars_laravel` (latin1) intacto, só leitura; a cópia e a conversão estão na seção 2 do `deploy/README.md`.
- No Git deste WSL `core.fileMode=false`: script novo precisa de `git update-index --chmod=+x`.
- Conversão latin1 → utf8mb4: `php artisan ars:converter-utf8mb4` (relatório; `--apply` converte),
  o mesmo comando do Petição Fácil. Aplicado no banco local em 14/11. Virada do banco antigo:
  `ddev virada-banco` (cópia, conversão, conferências e dump para o REIA-HD; ensaiado em 14/11).
- Número do processo do NEO: `App\Support\ProcessNumber::format` (CNJ e formato antigo).
- **Login único (BVAcesso, desde 15/11/2026):** mesmo cliente do Petição Fácil (`config/bvacesso.php`,
  `app/Support/BVAcesso/BVAcessoClient.php` igual em todos os sistemas, `UserSync` próprio,
  `Auth\BVAcessoController` em `/auth/bvacesso` e `/callback`, middleware `bvacesso.check` no grupo
  `auth`, coluna `usuarios.bvacesso_id`, exportação `ars:exportar-bvacesso`). **Não cria conta**: só
  liga quem já existe na ARS (pelo `bvacesso_id`, `login_usu` ou `email_usu`), porque usuário sem
  setor e sem cliente vê tudo. O perfil (`nivel_usu`) vem do BVAcesso; setor, clientes e regiões
  ficam na ARS. O login entra pelo `AuthService::storeUserSession` (mesma sessão do login próprio).
  Roteiro na seção 5 do `deploy/README.md`; contexto do BVAcesso em `~/projetos/bvacesso/AGENTS.md`.

## Como rodar
- Testes: `ddev testes` (PHPUnit 12, SQLite em memória; aceita `--filter X`).
- Front: `ddev npm run build` (ou `ddev npm run dev`).
- Fluxo: implementar → `ddev testes` → commit.

## Regras
- Push e deploy só com OK do Fabio; não apagar dados sem OK; senhas e segredos só no `.env`.
- Nunca `--seed` em produção. Mensagens de commit simples (sem prefixo).
