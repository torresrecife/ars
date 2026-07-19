const mix = require('laravel-mix');

mix
    .js('resources/js/app.js', 'public/build/js')
    .sass('resources/sass/app.scss', 'public/build/css')
    .options({
        processCssUrls: false,
    });

if (!mix.inProduction()) {
    mix.sourceMaps();
}
