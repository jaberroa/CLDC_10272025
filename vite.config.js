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
                'resources/js/carnet/carnet-editor.js',
                'resources/js/delete-confirmation.js',
                'resources/js/toast-simple.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Separar por tipo de módulo
                    if (id.includes('node_modules')) {
                        // Vendor libraries
                        if (id.includes('jquery')) return 'vendor-jquery';
                        if (id.includes('bootstrap')) return 'vendor-bootstrap';
                        if (id.includes('chart') || id.includes('apexcharts')) return 'vendor-charts';
                        if (id.includes('swiper') || id.includes('sweetalert') || id.includes('simplebar')) return 'vendor-ui';
                        if (id.includes('html2canvas') || id.includes('jspdf') || id.includes('qrcode')) return 'vendor-utils';
                        
                        // Otros vendor modules
                        return 'vendor';
                    }
                    
                    // Separar por módulos de la aplicación
                    if (id.includes('carnet')) return 'app-carnet';
                    if (id.includes('miembros')) return 'app-miembros';
                    if (id.includes('toast') || id.includes('delete-confirmation')) return 'app-components';
                }
            }
        },
        // Aumentar límite de warning a 1MB
        chunkSizeWarningLimit: 1000
    }
});
