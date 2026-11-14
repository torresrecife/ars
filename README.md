# ARS - Sector Results Tracking

ARS Online is an administrative and operational system currently being modernized into a cleaner Laravel application.

The project includes authentication, dashboards, production views, reports, goals, and administrative modules for users, clients, sectors, regions, weeks, progress items, and operational targets. Some compatibility points from the legacy PHP application still exist, but the intended architecture is Laravel-first: routes, controllers, policies, Form Requests, Eloquent/Query Builder, Blade, Sass, Vite, JSON endpoints, and named service/view-model contracts.

## Overview

ARS centralizes operational flows related to clients, wallets, goals, progress items, regions, weeks, and production. Local application data is stored in MySQL, while production and financial operational data is read from an external SQL Server database used by the NEO Legal system.

Main areas:

- Laravel authentication over the `usuarios` table.
- Authenticated navigation through `index`, `carteiras`, `painel`, `producao`, and `relatorio`.
- Administrative modules for users, clients, sectors, regions, weeks, progress items, and goals.
- Production dashboard by client, wallet, region, and area.
- Monthly and weekly reports.
- Progress and billing details loaded from the external SQL Server integration.

## Stack

- PHP `8.5` (composer `platform.php` 8.5.0; minimum `^8.4`)
- Laravel `13`
- MySQL `8.4` (utf8mb4) for local ARS data
- SQL Server (NEO Legal) through `sqlsrv` + Microsoft ODBC Driver 18
- PHPUnit `12`
- Node `22`, Vite `8`, Sass (`sass-embedded`)
- Local environment: DDEV (nginx-fpm), same versions as production

## Project Structure

- `app/Http/Controllers`: HTTP entry points and administrative controllers.
- `app/Services`: application rules, payload assembly, and view model orchestration.
- `app/Repositories`: MySQL access and SQL Server integration.
- `app/Models`: Eloquent models.
- `app/Policies`: module/resource authorization.
- `app/ViewModels`: named output contracts for screens and reports.
- `app/Domain`: small domain helpers, formatters, and support objects.
- `resources/views`: Blade views.
- `lang`: JSON translation files.
- `resources/sass`: modular Sass source.
- `public/js/modules`: modularized legacy JavaScript by screen/module.
- `deploy`: production install/update/backup scripts (EUROPA-HD).
- `routes/web.php`: Laravel web routes.
- `database/migrations`: local database migrations.
- `tests`: unit and HTTP tests.

## Local Setup (DDEV)

```bash
ddev start
cp .env.example .env          # DB_* already point to the DDEV database (db/db/db)
ddev composer install
ddev artisan key:generate
ddev artisan migrate
ddev npm install
ddev npm run build
```

The app runs at `http://ars.ddev.site:8080`. Fill the `SQLSRV_DB_*` variables to reach NEO
(`SQLSRV_DB_TRUST_SERVER_CERTIFICATE=true`: the SQL Server uses a self-signed certificate).

After the production database moves to REIA-HD, `ddev importar-producao` replaces the local
database with a production copy (snapshot first).

Legacy latin1 databases are converted with:

```bash
ddev artisan ars:converter-utf8mb4          # report only
ddev artisan ars:converter-utf8mb4 --apply  # convert
```

## Localization

The system language is configured through `.env`. There is no runtime language switch button in the UI.

Default configuration:

```env
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en_CA
APP_FAKER_LOCALE=pt_BR
```

Supported application locales:

- `pt_BR`: Brazilian Portuguese (default).
- `en_CA`: English (fallback; the translation keys are in English).

Translation files are JSON-based:

```text
lang/en_CA.json
lang/pt_BR.json
```

The project uses English strings as translation keys:

```php
__('Users')
__('Create a new user')
__('Error saving client.')
```

For Portuguese output, add or update the corresponding key in `lang/pt_BR.json`:

```json
{
    "Users": "Usuários",
    "Create a new user": "Crie um novo usuário",
    "Error saving client.": "Erro ao salvar o cliente."
}
```

The English file usually maps each key to itself:

```json
{
    "Users": "Users",
    "Create a new user": "Create a new user",
    "Error saving client.": "Error saving client."
}
```

JavaScript translations are exposed by the main shell view through:

```js
window.arsTranslations
```

Client-side modules should use:

```js
arsTranslate("Save")
arsTranslate("Error saving user.")
arsFormat("The field :field is required.", { field: fieldName })
```

jQuery UI dialog buttons must use computed object keys so the visible button label can be translated:

```js
buttons: {
    [arsTranslate("Save")]: function () {
        // submit form
    },
    [arsTranslate("Exit")]: function () {
        $(this).dialog("close");
    }
}
```

Confirmation dialogs follow the same pattern:

```js
{
    [arsTranslate("Yes")]: function () {
        // confirmed action
    },
    [arsTranslate("No")]: function () {
        $(this).dialog("close");
    }
}
```

When adding a new JavaScript-facing string, add it in three places:

1. The `$arsTranslations` array in `resources/views/index/shell.blade.php`.
2. `lang/en_CA.json`.
3. `lang/pt_BR.json`.

After changing locale configuration in production, clear cached configuration:

```bash
php artisan config:clear
php artisan cache:clear
```

If configuration caching is used, rebuild it:

```bash
php artisan config:cache
```

## Frontend and Build

```bash
ddev npm install
ddev npm run build   # or: ddev npm run dev
```

- Vite compiles only the Sass entrypoint `resources/sass/app.scss` (loaded with `@vite` in
  `resources/views/index/shell.blade.php`). Without a build, the shell falls back to
  `public/css/ars-modern.css`.
- The jQuery modules in `public/js/modules` share global functions, so they are served as plain
  files (cache-busted by file modification time), not bundled.
- Static assets live in `public/css`, `public/js` and `public/img`; the web server document root is
  `public/`.
- In a subfolder (production `/ars`), set `ASSET_URL=/ars` before `npm run build`.

## Main Routes

Guest routes:

- `GET /`
- `GET /login`
- `POST /login`

Authenticated routes:

- `GET /index`
- `GET /logout`
- `GET /carteiras`
- `GET /painel`
- `GET /producao`
- `GET /relatorio`
- `GET /admin`
- `GET /metas`

Administrative screen routes:

- `GET /usuarios`
- `GET /clientes`
- `GET /setores`
- `GET /andamentos`
- `GET /semanas`
- `GET /regioes`

Administrative REST/JSON endpoints:

- `/admin/usuarios`
- `/admin/clientes`
- `/admin/setores`
- `/admin/andamentos`
- `/admin/semanas`
- `/admin/regioes`
- `/admin/metas`

## Authentication and Authorization

Authentication uses Laravel `auth` and `guest` middleware. Authorization for administrative modules is handled through policies and gates by resource.

Known user levels:

- `ADM`: full administration access.
- `GER`: management access with module/region restrictions.
- `USU`: operational user with limited access.

The authentication table and behavior are configured with:

```env
AUTH_USER_TABLE=usuarios
AUTH_CASE_SENSITIVE=false
AUTH_VALIDATE_ALWAYS=true
```

## Databases

The project uses two data sources:

- Local MySQL: ARS-owned data, including users, clients, sectors, regions, weeks, progress items, and goals.
- External SQL Server: NEO Legal production, financial, progress, billing, and operational detail data.

SQL Server integration should remain concentrated in dedicated repositories/services, including:

- `NeoSqlsrvExecutor`
- `NeoSqlsrvRepository`
- `NeoPanelRepository`
- `NeoDetailRepository`
- `GeneralProductionNeoRepository`
- `SqlsrvLookupRepository`

Avoid direct `sqlsrv_query(...)` calls outside the SQL Server integration layer.

## Tests

```bash
ddev testes
ddev testes --filter "MetaControllerTest|ClientAdminControllerTest"
```

## Deployment

Production: `http://172.31.20.60/ars` (EUROPA-HD, nginx + PHP-FPM 8.5, subfolder of the Petição
Fácil site), database `ars` on REIA-HD (MySQL 8.4). Full guide in `deploy/README.md`; updates with
`./deploy/atualizar.sh` on the server.

## Modernization Status

Already completed or partially completed:

- Gradual removal of physical `.php` wrappers.
- Main navigation through Laravel routes.
- Laravel-based authentication.
- Policies for administrative modules.
- REST/JSON endpoints for administrative modules.
- Administrative frontend using a common AJAX client.
- Administrative and operational views migrated to Blade.
- Repositories migrated to Eloquent/Query Builder where practical.
- SQL Server integration centralized in a NEO integration layer.
- DTOs/view models introduced for dashboards, reports, and details.
- Sass introduced incrementally (Vite since the Laravel 13 upgrade).
- Laravel 13 / PHP 8.5 / MySQL 8.4 utf8mb4 (branch `ars_php8`).
- JSON-based localization introduced with `.env` locale selection.

Remaining compatibility areas:

- Some inherited visual and JavaScript helpers remain.
- Part of the legacy CSS still exists in `public/css/template.css` and `public/css/system.css`.
- Some inline styles may still exist in older views.

## Evolution Guidelines

When continuing the migration, prefer:

- Explicit Laravel routes in `routes/web.php`.
- Small controllers.
- FormRequests for validation.
- Policies for authorization.
- Services for application rules.
- Repositories only for data access.
- DTOs/view models for input and output contracts.
- Blade partials/components for reusable HTML.
- Sass in `resources/sass`.
- JSON responses instead of legacy `0/1/2` protocols.
- No manual SQL outside the correct integration layer.
- English translation keys with localized values in JSON files.

## Useful Commands

```bash
ddev artisan route:list
ddev artisan optimize:clear
ddev artisan migrate
ddev npm run build
```

## License

Proprietary project.
