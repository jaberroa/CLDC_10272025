import { VitePWAOptions } from 'vite-plugin-pwa';

/**
 * PWA Configuration for Vite
 * Configures service worker, caching strategies, and offline functionality
 */

export const pwaConfig: Partial<VitePWAOptions> = {
  registerType: 'prompt',
  includeAssets: ['favicon.ico', 'robots.txt', 'cldc-logo.png'],
  
  manifest: {
    name: 'Colegio de Licenciados en Derecho Civil Inmobiliario',
    short_name: 'CLDCI',
    description: 'Sistema de gestión del Colegio de Licenciados en Derecho Civil Inmobiliario',
    theme_color: '#1e40af',
    background_color: '#ffffff',
    display: 'standalone',
    orientation: 'portrait',
    scope: '/',
    start_url: '/',
    icons: [
      {
        src: '/lovable-uploads/b866c3ad-ac0d-4ecd-94bf-f90d153c49d5.png',
        sizes: '192x192',
        type: 'image/png',
        purpose: 'any maskable',
      },
      {
        src: '/lovable-uploads/b866c3ad-ac0d-4ecd-94bf-f90d153c49d5.png',
        sizes: '512x512',
        type: 'image/png',
        purpose: 'any maskable',
      },
    ],
    shortcuts: [
      {
        name: 'Dashboard',
        short_name: 'Dashboard',
        description: 'Ver dashboard principal',
        url: '/dashboard',
        icons: [{ src: '/lovable-uploads/b866c3ad-ac0d-4ecd-94bf-f90d153c49d5.png', sizes: '192x192' }],
      },
      {
        name: 'Miembros',
        short_name: 'Miembros',
        description: 'Gestionar miembros',
        url: '/miembros',
        icons: [{ src: '/lovable-uploads/b866c3ad-ac0d-4ecd-94bf-f90d153c49d5.png', sizes: '192x192' }],
      },
    ],
    categories: ['business', 'education', 'productivity'],
  },

  workbox: {
    globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
    
    // Cache runtime resources
    runtimeCaching: [
      {
        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
        handler: 'CacheFirst',
        options: {
          cacheName: 'google-fonts-cache',
          expiration: {
            maxEntries: 10,
            maxAgeSeconds: 60 * 60 * 24 * 365, // 1 year
          },
          cacheableResponse: {
            statuses: [0, 200],
          },
        },
      },
      {
        urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
        handler: 'CacheFirst',
        options: {
          cacheName: 'gstatic-fonts-cache',
          expiration: {
            maxEntries: 10,
            maxAgeSeconds: 60 * 60 * 24 * 365, // 1 year
          },
          cacheableResponse: {
            statuses: [0, 200],
          },
        },
      },
      {
        urlPattern: /^https:\/\/.*\.supabase\.co\/rest\/v1\/.*/i,
        handler: 'NetworkFirst',
        options: {
          cacheName: 'supabase-api-cache',
          expiration: {
            maxEntries: 50,
            maxAgeSeconds: 60 * 5, // 5 minutes
          },
          cacheableResponse: {
            statuses: [0, 200],
          },
          networkTimeoutSeconds: 10,
        },
      },
      {
        urlPattern: /^https:\/\/.*\.supabase\.co\/storage\/v1\/.*/i,
        handler: 'CacheFirst',
        options: {
          cacheName: 'supabase-storage-cache',
          expiration: {
            maxEntries: 100,
            maxAgeSeconds: 60 * 60 * 24 * 30, // 30 days
          },
          cacheableResponse: {
            statuses: [0, 200],
          },
        },
      },
    ],
    
    // Clean old caches
    cleanupOutdatedCaches: true,
    
    // Skip waiting for new service worker
    skipWaiting: true,
    clientsClaim: true,
  },

  devOptions: {
    enabled: false, // Disable in development for faster reload
    type: 'module',
  },
};
