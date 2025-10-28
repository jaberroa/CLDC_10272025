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
        // Optimizaciones de build
        target: 'es2015',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Separar vendor libraries
                    if (id.includes('node_modules')) {
                        if (id.includes('jquery')) return 'vendor-jquery';
                        if (id.includes('bootstrap')) return 'vendor-bootstrap';
                        if (id.includes('chart') || id.includes('apexcharts')) return 'vendor-charts';
                        if (id.includes('swiper') || id.includes('sweetalert') || id.includes('simplebar')) return 'vendor-ui';
                        if (id.includes('html2canvas') || id.includes('jspdf') || id.includes('qrcode')) return 'vendor-utils';
                        return 'vendor';
                    }
                    
                    // Separar módulos de la aplicación
                    if (id.includes('carnet')) return 'app-carnet';
                    if (id.includes('miembros')) return 'app-miembros';
                    if (id.includes('toast') || id.includes('delete-confirmation')) return 'app-components';
                },
                // Optimizar nombres de archivos
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(css)$/.test(assetInfo.name)) {
                        return `assets/css/[name]-[hash].${ext}`;
                    }
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
                        return `assets/images/[name]-[hash].${ext}`;
                    }
                    if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return `assets/fonts/[name]-[hash].${ext}`;
                    }
                    return `assets/[name]-[hash].${ext}`;
                }
            }
        },
        // Configuración de chunking
        chunkSizeWarningLimit: 1000,
        // Optimizaciones de CSS
        cssCodeSplit: true,
        cssMinify: true,
    },
    // Configuración de desarrollo
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    // Optimizaciones de dependencias
    optimizeDeps: {
        include: [
            'jquery',
            'bootstrap',
            'html2canvas',
            'jspdf',
            'qrcode'
        ],
    },
});