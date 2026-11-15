# Implantação da ARS (Ubuntu 26.04, sem Docker)

Fica no **EUROPA-HD** `172.31.20.60`, numa subpasta do site do Petição Fácil (porta 80), como o
BVTarefas e o BVAcesso: **`http://172.31.20.60/ars`**. Banco `ars` no **REIA-HD** `172.31.20.62`
(MySQL 8.4), ao lado do `peticaofacil`, `bvtarefas` e `bvacesso`. Os dados de produção e
faturamento vêm do NEO (SQL Server, réplica `Neo_Juridico_replica`), só leitura.

Código em `/var/www/ars` (branch `ars_php8`), backups em `/var/backups/ars`. Usa o mesmo nginx,
PHP-FPM 8.5, driver do SQL Server (ODBC 18 + `sqlsrv`), Composer e Node já instalados para o
Petição Fácil (ver `deploy/README.md` do Petição Fácil).

| Arquivo | Vai para |
|---|---|
| `nginx-ars-subpasta.conf` | `/etc/nginx/snippets/peticaofacil-extra/ars.conf` (incluído pelo site do Petição Fácil) |
| `env.producao.example` | modelo do `/var/www/ars/.env` (senhas só no servidor) |
| `atualizar.sh` | atualização a partir do GitHub (backup, código, dependências, build, migrations, caches) |
| `backup.sh` | banco, 14 dias |
| `https/` | HTTPS com a CA interna para o site inteiro do EUROPA-HD (roteiro em `https/README.md`) |
| `apache-servidor-antigo.conf` | `/etc/apache2/conf-available/ars-redirect.conf` no servidor antigo 10.81.11.202 (seção 2) |

Desde 09/11 o Bitdefender intercepta o SSH dos servidores (só entra com senha): os comandos
abaixo são rodados pelo Fabio, numa sessão SSH própria. O GitHub é buscado por
`ssh.github.com:443`.

## 1. Instalação

**REIA-HD** (`ssh fabio.ferreira@172.31.20.62`): banco e usuário.

```bash
SENHA=$(openssl rand -base64 24 | tr -d '/+='); echo "Senha do banco ars: $SENHA"   # vai para DB_PASSWORD
sudo mysql -e "CREATE DATABASE ars CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'ars'@'172.31.20.60' IDENTIFIED BY '$SENHA';
  GRANT ALL PRIVILEGES ON ars.* TO 'ars'@'172.31.20.60';"
```

A porta 3306 do REIA-HD já aceita o EUROPA-HD (regra do Petição Fácil).

**EUROPA-HD** (`ssh fabio.ferreira@172.31.20.60`)

```bash
# Chave só de leitura do repositório (GitHub → torresrecife/ars → Settings → Deploy keys)
ssh-keygen -q -t ed25519 -N "" -C "EUROPA-HD deploy ars" -f ~/.ssh/deploy_ars
printf 'Host github-ars\n  HostName ssh.github.com\n  Port 443\n  User git\n  IdentityFile ~/.ssh/deploy_ars\n  IdentitiesOnly yes\n' >> ~/.ssh/config
cat ~/.ssh/deploy_ars.pub          # colar na Deploy key do GitHub

sudo install -d -o "$USER" -g www-data -m 2775 /var/www/ars /var/backups/ars
git clone -b ars_php8 github-ars:torresrecife/ars.git /var/www/ars
cd /var/www/ars
cp deploy/env.producao.example .env && chmod 640 .env
nano .env                                             # preencher os 4 "<...>" (NEO: os mesmos do Petição Fácil)
grep -n '<' .env                                      # não deve mostrar nada
composer install --no-dev --optimize-autoloader       # antes do artisan: cria o vendor/
php artisan key:generate
npm ci && npm run build                               # public/build (lê ASSET_URL=/ars do .env)
sudo setfacl -R -m g:www-data:rwX -m d:g:www-data:rwX storage bootstrap/cache
```

Depois, os dados (seção 2) e só então:

```bash
php artisan migrate --force                           # NUNCA --seed em produção
php artisan optimize

sudo cp deploy/nginx-ars-subpasta.conf /etc/nginx/snippets/peticaofacil-extra/ars.conf
sudo nginx -t && sudo systemctl reload nginx
```

Conferir: `http://172.31.20.60/ars` (login), um painel com dados do NEO e o relatório mensal.

## 2. Dados da produção antiga (10.81.11.202)

Banco antigo: `ars_laravel` em `10.81.11.202:3306`, usuário `admin` (senha só com o Fabio), em
latin1. O REIA-HD é MySQL 8.4 em utf8mb4. A conversão passa pelo comando próprio, nunca por
`ALTER TABLE ... CONVERT TO`, porque ele trata, valor a valor, tanto o texto em latin1 de verdade
quanto os bytes UTF-8 gravados em coluna latin1. O caminho é o do Petição Fácil: converter e
conferir no DDEV do PC e só então mandar para o REIA-HD.

**No PC (WSL, em `~/projetos/ars`), com o sistema antigo parado para ninguém gravar no meio:**

```bash
ddev virada-banco
```

O comando (`.ddev/commands/host/virada-banco`) usa `~/.config/bvaa/mysql-ars-antigo.cnf`
(permissão 600; na primeira vez ele cria o arquivo para preencher a senha) e faz: snapshot do banco
local → leitura do servidor antigo (versão, tabelas, contagens; confere que as colunas utf8mb4 não
têm texto fora do latin1) → dump em latin1 → import no DDEV e contagens iguais às do servidor antigo
→ `ars:converter-utf8mb4 --apply` → checksum do texto de todas as tabelas igual antes e depois, os
acentos iguais e tudo em utf8mb4 → migrations e `ddev testes` → dump final em utf8mb4 com
`sha256`. Para no primeiro problema, sem gerar o dump final. Tudo fica em
`.ddev/db_dumps/virada-AAAAMMDD-HHMMSS/` (fora do Git); no fim ele mostra os comandos seguintes.
Depois, abrir `http://ars.ddev.site:8080` e conferir painel e relatório.

Se o DDEV não alcançar o `10.81.11.202`, fazer o dump pelo HeidiSQL (conexão latin1), importar com
`ddev import-db`, rodar `ddev artisan ars:converter-utf8mb4 --apply` e gerar o dump em utf8mb4 com
`ddev export-db`.

**No REIA-HD** (banco `ars` já criado na seção 1):

```bash
scp .ddev/db_dumps/virada-AAAAMMDD-HHMMSS/ars-utf8mb4.sql.gz* fabio.ferreira@172.31.20.62:~   # do PC
sha256sum -c ars-utf8mb4.sql.gz.sha256 && gunzip -c ars-utf8mb4.sql.gz | sudo mysql ars        # no REIA-HD
rm ~/ars-utf8mb4.sql.gz ~/ars-utf8mb4.sql.gz.sha256
```

**No EUROPA-HD**, em `/var/www/ars`: `php artisan ars:converter-utf8mb4` (deve dizer que não há
nada a converter), `php artisan migrate --force` e `php artisan optimize`.

**Ensaio de 14/11/2026 (DDEV, dados reais do 10.81.11.202, MySQL 5.6.51):** dump em latin1 sem
perda (as colunas utf8mb4 do banco antigo não têm caractere fora do latin1); 14 tabelas e 4.806
linhas, contagens iguais às do servidor antigo; 10 tabelas convertidas, todo o texto em latin1 de
verdade, as 49 linhas com acento idênticas antes e depois; as 16 migrations já estavam aplicadas
(nada a migrar); 84 testes, 21 telas e consultas ao NEO sem erro. Uma data `0000-00-00`
(`usuarios.acesso_usu`, usuário que nunca entrou): a tela mostra vazio, e editar o usuário e
registrar o acesso funcionam no modo estrito do MySQL 8.4. O comando de conversão tira o modo
estrito só na sessão dele. O `ddev virada-banco` foi rodado com sucesso no mesmo dia (mesmos números),
sua conferência por checksum foi testada alterando um acento (detectou), e o dump final importado num
banco vazio do MySQL 8.4 ficou igual ao convertido.

**Senhas:** 16 das 33 contas da produção antiga ainda têm senha MD5 do legado. O login continua aceitando e,
na primeira entrada, regrava a senha em bcrypt (`Usuario::getAuthPasswordName()` = `senha_usu`).

**Virada feita em 14–15/11/2026:** dump do `ddev virada-banco` de 14/11 15:42 importado no REIA-HD,
login (inclusive senha MD5), painéis do NEO e relatório mensal conferidos em `http://172.31.20.60/ars`.

**Servidor antigo:** `http://10.81.11.202/ars/...` redireciona (301) para o mesmo caminho em
`http://172.31.20.60/ars/...`, como o `/peticaofacil2`; os nomes antigos em `.php` (`painel.php`,
`usu.php`, `dados_anda.php`…) vão para as rotas novas. Regra em `deploy/apache-servidor-antigo.conf`,
instalada no 10.81.11.202 (Apache 2.4) como `/etc/apache2/conf-available/ars-redirect.conf`:

```bash
sudo nano /etc/apache2/conf-available/ars-redirect.conf     # colar o conteúdo do arquivo
sudo a2enconf ars-redirect && sudo apache2ctl configtest && sudo systemctl reload apache2
curl -sI http://10.81.11.202/ars/painel.php | grep -i location   # http://172.31.20.60/ars/painel
```

Código e banco `ars_laravel` antigos ficam intactos. Voltar atrás: `sudo a2disconf ars-redirect &&
sudo systemctl reload apache2`.

## 3. Atualizar

No PC: alteração → `ddev testes` → commit → `git push`. No servidor:

```bash
ssh fabio.ferreira@172.31.20.60
cd /var/www/ars && ./deploy/atualizar.sh
```

**Voltar atrás:** `git revert` no PC, `git push` e `./deploy/atualizar.sh`; dados estragados →
restaurar o banco do backup feito no início da atualização:

```bash
read -rsp "Senha do banco: " MYSQL_PWD; export MYSQL_PWD
gunzip -c /var/backups/ars/banco-AAAAMMDD-HHMMSS.sql.gz | mysql -h 172.31.20.62 -u ars ars
```

## 4. Backup diário

No `crontab` do `fabio.ferreira` no EUROPA-HD:

```bash
50 2 * * * /var/www/ars/deploy/backup.sh >> /var/backups/ars/backup.log 2>&1
```

## 5. Login único (BVAcesso)

Mesmo cliente do Petição Fácil e do BVTarefas (`app/Support/BVAcesso`). Diferença da ARS: **só entra
quem já tem conta aqui** (ligada pelo login curto ou pelo e-mail). Um usuário sem setor e sem
cliente veria os dados de todos, então a ARS não cria conta no primeiro login (decisão de 15/11):
quem não existe vê "A sua conta não está cadastrada na ARS" e o administrador a cria, com setor e
clientes. O perfil (`ADM`, `GER`, `USU`) vem do BVAcesso; setor, clientes e regiões continuam na ARS.

1. **No BVAcesso** (`http://172.31.20.60/bvacesso`): Administração → Sistemas → Novo sistema.
   Nome `ARS`, identificador `ars`, endereço `http://172.31.20.60/ars`, retorno
   `http://172.31.20.60/ars/auth/bvacesso/callback`, perfis `ADM, GER, USU`. Copiar o bloco
   "Ligação com o sistema" (o segredo só aparece nessa hora).
2. **No EUROPA-HD**, em `/var/www/ars`: colar `BVACESSO_CLIENT_ID` e `BVACESSO_CLIENT_SECRET` no
   `.env` (com `BVACESSO_URL=http://172.31.20.60/bvacesso` e `BVACESSO_LOCAL_LOGIN=true`) e rodar
   `php artisan optimize`. A tela de login ganha o botão "Entrar com o BVAcesso" ao lado do formulário.
3. **Contas** (só as ativas que entraram em 2026, decisão de 15/11). Cada pessoa entra no BVAcesso
   com o mesmo login e a mesma senha de hoje; quem tem senha MD5 cria uma nova na hora. Quem já está
   no BVAcesso (pelo Petição Fácil) só ganha o acesso à ARS. Contas com e-mail repetido ou inválido
   ficam de fora e aparecem no relatório: corrigir o e-mail na ARS e rodar de novo (repetir é seguro).

```bash
cd /var/www/ars && php artisan ars:exportar-bvacesso --desde=2026-01-01
cd /var/www/bvacesso && php artisan bvacesso:importar-usuarios /var/www/ars/storage/app/bvacesso-usuarios.json ars          # só relatório
cd /var/www/bvacesso && php artisan bvacesso:importar-usuarios /var/www/ars/storage/app/bvacesso-usuarios.json ars --apply  # grava
rm /var/www/ars/storage/app/bvacesso-usuarios.json /var/www/bvacesso/storage/app/importacao-*.csv
```

O arquivo exportado tem os hashes das senhas e o relatório tem nomes e e-mails: apague os dois depois.

4. Depois que todos estiverem entrando pelo BVAcesso: `BVACESSO_LOCAL_LOGIN=false` e
   `php artisan optimize` (a tela de login vai direto para o BVAcesso; `/login?local=1` mostra só as
   mensagens). Voltar ao login próprio: apagar `BVACESSO_CLIENT_ID` e `php artisan optimize`.

## 6. Onde olhar quando algo falha

- Log da aplicação: `/var/www/ars/storage/logs/laravel-AAAA-MM-DD.log`.
- NEO lento ou fora: `APP_NEO_QUERY_LOG=true` no `.env` + `php artisan optimize` registra tempo e
  erros das consultas ao SQL Server (`general.monthly`, `dashboard.panel` no log).
- nginx: `/var/log/nginx/error.log`; PHP-FPM: `journalctl -u php8.5-fpm`.
