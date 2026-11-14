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
cp deploy/env.producao.example .env && nano .env      # preencher os "<...>"
php artisan key:generate
composer install --no-dev --optimize-autoloader
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
# 1. Dump do banco antigo pelo cliente do container do banco (MySQL 8.4)
read -rsp "Senha do admin no 10.81.11.202: " MYSQL_PWD; echo
ddev exec -s db "MYSQL_PWD='$MYSQL_PWD' mysqldump -h 10.81.11.202 -P 3306 -u admin \
  --single-transaction --quick --no-tablespaces --skip-triggers --column-statistics=0 \
  --set-gtid-purged=OFF --default-character-set=latin1 ars_laravel" | gzip > .ddev/db_dumps/ars-antigo.sql.gz
unset MYSQL_PWD
gzip -dc .ddev/db_dumps/ars-antigo.sql.gz | tail -n 1      # tem que terminar em "Dump completed"

# 2. Importar no DDEV (snapshot antes), converter e conferir
ddev snapshot --name antes-ars-antigo
ddev import-db --file=.ddev/db_dumps/ars-antigo.sql.gz
ddev artisan ars:converter-utf8mb4            # relatório: conferir as colunas listadas
ddev artisan ars:converter-utf8mb4 --apply
ddev artisan migrate --force
ddev testes && echo "abrir http://ars.ddev.site:8080 e conferir acentos, painel e relatório"

# 3. Dump do banco convertido, para o REIA-HD
ddev exec -s db "mysqldump --single-transaction --quick --no-tablespaces --set-gtid-purged=OFF \
  --default-character-set=utf8mb4 db" | gzip > .ddev/db_dumps/ars-utf8mb4.sql.gz
scp .ddev/db_dumps/ars-utf8mb4.sql.gz fabio.ferreira@172.31.20.62:~
```

Se o `mysqldump` não alcançar o `10.81.11.202` de dentro do DDEV, fazer o dump pelo HeidiSQL
(conexão latin1) e seguir do passo 2.

**No REIA-HD** (banco `ars` já criado na seção 1):

```bash
gunzip -c ~/ars-utf8mb4.sql.gz | sudo mysql ars && rm ~/ars-utf8mb4.sql.gz
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
estrito só na sessão dele.

**Senhas:** 16 das 33 contas da produção antiga ainda têm senha MD5 do legado. O login continua aceitando e,
na primeira entrada, regrava a senha em bcrypt (`Usuario::getAuthPasswordName()` = `senha_usu`).

**Servidor antigo:** depois da virada, redirecionar `/ars/...` do Apache antigo para
`http://172.31.20.60/ars/...` (como foi feito com o `/peticaofacil2`), ou desligar o sistema antigo.

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

## 5. Onde olhar quando algo falha

- Log da aplicação: `/var/www/ars/storage/logs/laravel-AAAA-MM-DD.log`.
- NEO lento ou fora: `APP_NEO_QUERY_LOG=true` no `.env` + `php artisan optimize` registra tempo e
  erros das consultas ao SQL Server (`general.monthly`, `dashboard.panel` no log).
- nginx: `/var/log/nginx/error.log`; PHP-FPM: `journalctl -u php8.5-fpm`.
