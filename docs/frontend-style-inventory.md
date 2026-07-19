# Frontend Style Inventory

## Legacy CSS still active

- `css/template.css`
- `css/system.css`
- `css/texto.css`
- `css/custom-theme/jquery-ui.css`

These files are still the active runtime source for the application shell and old Joomla-era admin layout.

## Inline style hotspots

### High priority

- `resources/views/index/shell.blade.php`
- `resources/views/dashboard/panel.blade.php`
- `resources/views/geral/weekly.blade.php`
- `resources/views/geral/monthly.blade.php`
- `resources/views/dados_anda/index.blade.php`
- `resources/views/dados_fatur/index.blade.php`

### Administrative modules

- `resources/views/usuarios/index.blade.php`
- `resources/views/clientes/index.blade.php`
- `resources/views/andamentos/index.blade.php`
- `resources/views/metas/index.blade.php`
- `resources/views/semanas/index.blade.php`
- `resources/views/regioes/index.blade.php`
- `resources/views/setores/index.blade.php`

## Modern pipeline target

- source Sass: `resources/sass`
- source JS: `resources/js`
- build output: `public/build`
- build tool: `laravel-mix`

## Recommended migration order

1. Move repeated inline styles from detail views into Sass partials.
2. Move dashboard/report inline `<style>` blocks into `resources/sass/components`.
3. Introduce layout utility classes in Blade and reduce `style=""`.
4. Only after that, decide whether `css/template.css` should be split or retired.
