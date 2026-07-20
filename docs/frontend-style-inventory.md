# Frontend Style Inventory

## Legacy CSS still active

- `css/template.css`
- `css/system.css`
- `css/texto.css`
- `css/custom-theme/jquery-ui.css`

These files are still the active runtime source for the application shell and old Joomla-era ***REMOVED*** layout.

## Inline style hotspots

### High priority

- `resources/views/index/shell.blade.php` - partially migrated to `ars-modern.css` / Sass.
- `resources/views/dashboard/panel.blade.php` - repeated table/title/tab styles migrated; week widths now use classes.
- `resources/views/geral/weekly.blade.php` - repeated table/title/button styles migrated; heat-map colors now use metric classes.
- `resources/views/geral/monthly.blade.php` - repeated table/title/button styles migrated; week widths now use classes.
- `resources/views/dados_anda/index.blade.php` - migrated to detail classes and `js/modules/details.js`.
- `resources/views/dados_fatur/index.blade.php` - migrated to detail classes and `js/modules/details.js`.

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
- runtime bridge while builds are not published: `css/ars-modern.css`

## Recommended migration order

1. Move repeated inline styles from detail views into Sass partials.
2. Continue moving ***REMOVED***istrative module inline styles into `resources/sass/components`.
3. Introduce layout utility classes in Blade and reduce `style=""`.
4. Only after that, decide whether `css/template.css` should be split or retired.
