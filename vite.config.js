import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Só o Sass passa pelo Vite. Os módulos jQuery de public/js/modules dependem de
// funções globais entre si e são servidos como estão (versão pela data do arquivo).
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
