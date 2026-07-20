const mix = require('laravel-mix');

mix
    .js('resources/js/app.js', 'public/build/js')
    .scripts([
        'js/modules/helpers.js',
        'js/modules/setores.js',
        'js/modules/usuarios.js',
        'js/modules/semanas.js',
        'js/modules/regioes.js',
        'js/modules/clientes.js',
        'js/modules/andamentos.js',
        'js/modules/metas.js',
        'js/modules/painel.js',
        'js/modules/relatorio.js',
    ], 'public/build/js/ars-modules.js')
    .scripts([
        'js/jFilterXCel2003.js',
        'js/modules/details.js',
    ], 'public/build/js/ars-details.js')
    .sass('resources/sass/app.scss', 'public/build/css')
    .options({
        processCssUrls: false,
    });

if (!mix.inProduction()) {
    mix.sourceMaps();
}
