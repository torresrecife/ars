# Frontend Style Inventory

## Legacy CSS still active

- `css/template.css`
- `css/system.css`
- `css/texto.css`
- `css/custom-theme/jquery-ui.css`

These files are still the active runtime source for the application shell and old Joomla-era ***REMOVED*** layout.

`resources/sass/components/_legacy-shell.scss` now contains the first migrated block from `css/template.css`: document base, shell header, status bar, content frame, control panel icons, and core panel layout. Keep `css/template.css` loaded until these blocks are verified in the compiled build and removed from the legacy file in smaller follow-up cuts.

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

1. Verify the migrated `legacy-shell` Sass block after `npm run dev`.
2. Remove the verified duplicated blocks from `css/template.css`.
3. Continue migrating remaining active `template.css` areas: login, ***REMOVED*** tables, form controls, and remaining toolbar/menu icons.
4. Retire `css/ars-modern.css` after Mix is mandatory in every environment.
