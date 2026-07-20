# ARS - Sector Results Tracking

ARS Online is an ***REMOVED***istrative and operational system currently being modernized into a cleaner Laravel application.

The project includes authentication, dashboards, production views, reports, goals, and ***REMOVED***istrative modules for users, clients, sectors, regions, weeks, progress items, and operational targets. Some compatibility points from the legacy PHP application still exist, but the intended architecture is Laravel-first: routes, controllers, policies, FormRequests, Eloquent/Query Builder, Blade, Sass, Laravel Mix, JSON endpoints, and named service/view model contracts.

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

- PHP `^7.2.5|^8.0`
- Laravel `6.20`
- MySQL for local ARS data
- SQL Server for NEO Legal integration
- PHPUnit `8/9`
- Node `16.x` recommended for the current environment
- npm `8.x`
- Laravel Mix `5`
- Sass

Known working Node/npm versions:

```bash
node -v
# v16.20.2

npm -v
# 8.19.4
```

## Project Structure

- `app/Http/Controllers`: HTTP entrypoints and ***REMOVED***istrative controllers.
- `app/Services`: application rules, payload assembly, and view model orchestration.
- `app/Repositories`: MySQL access and SQL Server integration.
- `app/Models`: Eloquent models.
- `app/Policies`: module/resource authorization.
- `app/ViewModels`: named output contracts for screens and reports.
- `app/Domain`: small domain helpers, formatters, and support objects.
- `resources/views`: Blade views.
- `resources/lang`: JSON translation files.
- `resources/sass`: modular Sass source.
- `resources/js`: Laravel Mix JavaScript entrypoint.
- `js/modules`: modularized legacy JavaScript by screen/module.
- `routes/web.php`: Laravel web routes.
- `database/migrations`: local database migrations.
- `tests`: unit and HTTP tests.

## Local Setup

Install PHP dependencies:

```bash
composer install
```

Create the local `.env` file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the application URL. In the current hybrid Apache setup, the app is usually served from a subdirectory:

```env
APP_URL=http://bvaa.test/ars
```

Configure the local MySQL database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ars_laravel
DB_USERNAME=your_user
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
```

Configure the SQL Server integration used by the external NEO Legal system:

```env
SQLSRV_DB_HOST=
SQLSRV_DB_PORT=1433
SQLSRV_DB_DATABASE=
SQLSRV_DB_USERNAME=
SQLSRV_DB_PASSWORD=
SQLSRV_DB_CHARSET=UTF-8
```

Run migrations when needed:

```bash
php artisan migrate
```

## Localization

The system language is configured through `.env`. There is no runtime language switch button in the UI.

Default configuration:

```env
APP_LOCALE=en_CA
APP_FALLBACK_LOCALE=en_CA
APP_FAKER_LOCALE=pt_BR
```

Supported application locales:

- `en_CA`: default English locale.
- `pt_BR`: Brazilian Portuguese locale.

Translation files are JSON-based:

```text
resources/lang/en_CA.json
resources/lang/pt_BR.json
```

The project uses English strings as translation keys:

```php
__('Users')
__('Create a new user')
__('Error saving client.')
```

For Portuguese output, add or update the corresponding key in `resources/lang/pt_BR.json`:

```json
{
    "Users": "Usuarios",
    "Create a new user": "Crie um novo usuario",
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
2. `resources/lang/en_CA.json`.
3. `resources/lang/pt_BR.json`.

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

Install JavaScript dependencies:

```bash
npm install
```

Development build:

```bash
npm run dev
```

Production build:

```bash
npm run prod
```

Main Sass entrypoint:

```text
resources/sass/app.scss
```

Laravel Mix generates:

```text
public/build/css/app.css
public/build/js/app.js
public/build/js/ars-modules.js
public/build/js/ars-details.js
public/mix-manifest.json
```

Important current deployment note: some environments still serve the project directly from `/ars` instead of using `public` as the Apache `DocumentRoot`. Because of that, the layout currently loads compiled assets from `/ars/public/build/...`. When the web server is moved to the standard Laravel `public` document root, asset paths should be reviewed and normalized.

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
- `GET /***REMOVED***`
- `GET /metas`

Administrative screen routes:

- `GET /usuarios`
- `GET /clientes`
- `GET /setores`
- `GET /andamentos`
- `GET /semanas`
- `GET /regioes`

Administrative REST/JSON endpoints:

- `/***REMOVED***/usuarios`
- `/***REMOVED***/clientes`
- `/***REMOVED***/setores`
- `/***REMOVED***/andamentos`
- `/***REMOVED***/semanas`
- `/***REMOVED***/regioes`
- `/***REMOVED***/metas`

## Authentication and Authorization

Authentication uses Laravel `auth` and `guest` middleware. Authorization for ***REMOVED***istrative modules is handled through policies/gates by resource.

Known user levels:

- `ADM`: full ***REMOVED***istration.
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

Run the full test suite:

```bash
php vendor/phpunit/phpunit/phpunit
```

Run filtered tests:

```bash
php vendor/phpunit/phpunit/phpunit --filter "MetaControllerTest|ClientAdminControllerTest"
```

Clear compiled views:

```bash
php artisan view:clear
```

Check PHP syntax for a file:

```bash
php -l app/Services/GeneralProductionService.php
```

Validate JavaScript syntax for a module:

```bash
node --check js/modules/usuarios.js
```

## Deployment

Recommended production flow:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run prod
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

If schema changes are included:

```bash
php artisan migrate --force
```

Required Laravel writable directories:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rw storage bootstrap/cache
```

If `composer install` must be executed as the Apache user:

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

## Apache

The ideal Laravel VirtualHost should point `DocumentRoot` to:

```text
/var/www/html/ars/public
```

Some current environments may still serve the project directly from:

```text
/var/www/html/ars
```

In the hybrid setup, make sure:

- `mod_rewrite` is enabled.
- `AllowOverride All` is configured for the project directory.
- `.htaccess` routes requests to `index.php`.
- `APP_URL` includes the correct subdirectory, for example `http://bvaa.test/ars`.

## Modernization Status

Already completed or partially completed:

- Gradual removal of physical `.php` wrappers.
- Main navigation through Laravel routes.
- Laravel-based authentication.
- Policies for ***REMOVED***istrative modules.
- REST/JSON endpoints for ***REMOVED***istrative modules.
- Administrative frontend using a common AJAX client.
- Administrative and operational views migrated to Blade.
- Repositories migrated to Eloquent/Query Builder where practical.
- SQL Server integration centralized in a NEO integration layer.
- DTOs/view models introduced for dashboards, reports, and details.
- Sass/Laravel Mix introduced incrementally.
- JSON-based localization introduced with `.env` locale selection.

Remaining compatibility areas:

- Some physical legacy PHP files may still exist.
- Some inherited visual and JavaScript helpers remain.
- Part of the legacy CSS still exists in `css/template.css` and `css/system.css`.
- Some inline styles may still exist in older views.
- Some environments still use a hybrid Apache document root.

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
php artisan route:list
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate
npm run dev
npm run prod
```

## License

Proprietary project.
