# ARS - Acompanhamento de Resultados Setoriais

Sistema ARS Online em processo de migração e modernização para Laravel.

Este repositório contém a aplicação ***REMOVED***istrativa e operacional do ARS, com módulos de autenticação, painel, produção, relatórios, metas e cadastros ***REMOVED***istrativos. O projeto ainda possui alguns pontos de compatibilidade com a estrutura legada, mas a direção arquitetural é Laravel puro: rotas, controllers, policies, FormRequests, Eloquent/Query Builder, Blade, Sass e Laravel Mix.

## Visão Geral

O ARS centraliza fluxos operacionais relacionados a clientes, carteiras, metas, andamentos, regiões, semanas e produção. Parte dos dados locais fica no banco MySQL da aplicação e parte das consultas operacionais/faturamento integra com o banco SQL Server do Sistema Jurídico Externo.

Principais áreas:

- `login` e autenticação Laravel sobre a tabela `usuarios`;
- navegação autenticada em `index`, `carteiras`, `painel`, `producao` e `relatorio`;
- ***REMOVED***istração de usuários, clientes, setores, regiões, semanas, andamentos e metas;
- painel de produção por cliente/carteira/região;
- relatórios mensal e semanal;
- detalhes de andamentos e faturamento vindos do SQL Server/Sistema Jurídico Externo.

## Stack

- PHP `^7.2.5|^8.0`
- Laravel `6.20`
- MySQL para dados locais do ARS
- SQL Server para integração com Sistema Jurídico Externo
- PHPUnit `8/9`
- Node `16.x` recomendado no ambiente atual
- npm `8.x`
- Laravel Mix `5`
- Sass

Versões atualmente usadas em produção/local Linux:

```bash
node -v
# v16.20.2

npm -v
# 8.19.4
```

## Estrutura Principal

- `app/Http/Controllers`: entrypoints HTTP e controllers ***REMOVED***istrativos.
- `app/Services`: regras de aplicação e montagem de payloads/view models.
- `app/Repositories`: acesso a MySQL local e integrações SQL Server.
- `app/Models`: models Eloquent.
- `app/Policies`: autorização por módulo/recurso.
- `app/ViewModels`: contratos de saída nomeados para telas e relatórios.
- `app/Domain`: regras pequenas de domínio, formatadores e objetos auxiliares.
- `resources/views`: views Blade.
- `resources/sass`: Sass modularizado.
- `resources/js`: entrada JS do Mix.
- `js/modules`: JavaScript legado modularizado por tela.
- `routes/web.php`: rotas web Laravel.
- `database/migrations`: migrations da base local.
- `tests`: testes unitários e HTTP.

## Configuração Local

1. Instale as dependências PHP:

```bash
composer install
```

2. Crie o `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure no `.env` a URL do subdiretório:

```env
APP_URL=http://bvaa.test/ars
```

4. Configure o banco local MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ars_laravel
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
DB_CHARSET=utf8mb4
```

5. Configure a integração SQL Server/Sistema Jurídico Externo:

```env
SQLSRV_DB_HOST=
SQLSRV_DB_PORT=1433
SQLSRV_DB_DATABASE=
SQLSRV_DB_USERNAME=
SQLSRV_DB_PASSWORD=
SQLSRV_DB_CHARSET=UTF-8
```

6. Rode as migrations quando necessário:

```bash
php artisan migrate
```

## Frontend e Build

Instale as dependências JS:

```bash
npm install
```

Build de desenvolvimento:

```bash
npm run dev
```

Build de produção:

```bash
npm run prod
```

O Sass principal fica em:

```text
resources/sass/app.scss
```

O Mix gera:

```text
public/build/css/app.css
public/build/js/app.js
public/mix-manifest.json
```

Observação importante: no estado atual, o Apache ainda serve a raiz do projeto em `/ars`, não apenas `public`. Por isso o layout carrega o build em `/ars/public/build/...`. Quando o DocumentRoot passar a apontar para `public`, esse ajuste deve ser revisto para o padrão Laravel normal.

## Rotas Principais

Rotas públicas/guest:

- `GET /`
- `GET /login`
- `POST /login`

Rotas autenticadas:

- `GET /index`
- `GET /logout`
- `GET /carteiras`
- `GET /painel`
- `GET /producao`
- `GET /relatorio`
- `GET /***REMOVED***`
- `GET /metas`

Módulos ***REMOVED***istrativos:

- `GET /usuarios`
- `GET /clientes`
- `GET /setores`
- `GET /andamentos`
- `GET /semanas`
- `GET /regioes`

Endpoints ***REMOVED***istrativos REST/JSON:

- `/***REMOVED***/usuarios`
- `/***REMOVED***/clientes`
- `/***REMOVED***/setores`
- `/***REMOVED***/andamentos`
- `/***REMOVED***/semanas`
- `/***REMOVED***/regioes`
- `/***REMOVED***/metas`

## Autenticação e Autorização

A autenticação já usa fluxo Laravel com `auth` e `guest`. A autorização dos módulos ***REMOVED***istrativos usa policies/gates por recurso.

Perfis observados no sistema:

- `ADM`: ***REMOVED***istração completa.
- `GER`: gestão com restrições por módulo/região.
- `USU`: usuário operacional com acesso mais limitado.

A tabela principal de autenticação é configurada por:

```env
AUTH_USER_TABLE=usuarios
AUTH_CASE_SENSITIVE=false
AUTH_VALIDATE_ALWAYS=true
```

## Bancos de Dados

O projeto trabalha com duas fontes:

- MySQL local: dados próprios do ARS, cadastros, metas, usuários, regiões, semanas, setores e clientes.
- SQL Server/Sistema Jurídico Externo: dados externos de produção, andamentos, lançamentos, faturamento e detalhes operacionais.

A integração SQL Server está concentrada em repositories/services próprios, incluindo:

- `NeoSqlsrvExecutor`
- `NeoSqlsrvRepository`
- `NeoPanelRepository`
- `NeoDetailRepository`
- `GeneralProductionNeoRepository`
- `SqlsrvLookupRepository`

Evite chamadas diretas a `sqlsrv_query(...)` fora dessa camada.

## Testes

Rodar a suíte completa:

```bash
php vendor/phpunit/phpunit/phpunit
```

Rodar testes filtrados:

```bash
php vendor/phpunit/phpunit/phpunit --filter "MetaControllerTest|ClientAdminControllerTest"
```

Limpar views compiladas:

```bash
php artisan view:clear
```

Verificar sintaxe PHP de um arquivo:

```bash
php -l app/Services/GeneralProductionService.php
```

## Deploy

Fluxo recomendado em produção:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run prod
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Se houver alteração de schema:

```bash
php artisan migrate --force
```

Permissões necessárias para Laravel:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rw storage bootstrap/cache
```

Se o `composer install` for executado pelo usuário do Apache:

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

## Apache

No estado ideal Laravel, o VirtualHost deve apontar o DocumentRoot para:

```text
/var/www/html/ars/public
```

No estado híbrido atual, o projeto ainda pode estar sendo servido diretamente por:

```text
/var/www/html/ars
```

Nesse caso, é necessário garantir:

- `mod_rewrite` ativo;
- `AllowOverride All` para o diretório do projeto;
- `.htaccess` roteando para `index.php`;
- `APP_URL` com o subdiretório correto, por exemplo `http://bvaa.test/ars`.

## Estado da Modernização

Já foram avançados vários cortes importantes:

- remoção gradual de wrappers físicos `.php`;
- navegação principal em rotas Laravel;
- autenticação baseada em Laravel;
- policies nos módulos ***REMOVED***istrativos;
- endpoints REST/JSON para módulos ***REMOVED***istrativos;
- frontend ***REMOVED***istrativo migrando para client AJAX comum;
- views ***REMOVED***istrativas e operacionais em Blade;
- repositories migrados para Eloquent/Query Builder onde possível;
- integração SQL Server centralizada em camada do Sistema Jurídico Externo;
- DTOs/view models em fluxos de painel, relatórios e detalhes;
- Sass/Laravel Mix introduzidos de forma incremental.

Ainda existem pontos de compatibilidade:

- alguns arquivos `.php` físicos legados;
- alguns helpers visuais/JS herdados;
- parte do CSS legado em `css/template.css` e `css/system.css`;
- algumas views ***REMOVED***istrativas com inline styles residuais;
- DocumentRoot ainda híbrido em alguns ambientes.

## Convenções de Evolução

Ao continuar a migração, prefira:

- rotas Laravel explícitas em `routes/web.php`;
- controllers pequenos;
- FormRequests para validação;
- policies para autorização;
- services para regra de aplicação;
- repositories apenas para acesso a dados;
- DTOs/view models para contratos de entrada/saída;
- Blade + partials/components para HTML reutilizável;
- Sass em `resources/sass`;
- evitar protocolo legado `0/1/2` em respostas novas;
- evitar SQL manual fora da camada de integração apropriada.

## Comandos Úteis

```bash
php artisan route:list
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate
npm run dev
npm run prod
```

## Licença

Projeto proprietário.
