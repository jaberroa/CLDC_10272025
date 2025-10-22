import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/miembros/app.css',
                'resources/css/global/app.css',
        'resources/css/carnet/app.css',
        'resources/js/carnet/app.js',
        'resources/js/carnet/carnet-editor.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
