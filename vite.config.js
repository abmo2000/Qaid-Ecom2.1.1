import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js' , 'resources/js/web/order.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    resolve: {
        alias: {
            '@web': '/resources/js/web/',
        },
    }
});
