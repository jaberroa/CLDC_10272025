import { Workbox } from 'workbox-window';
import { toast } from '@/hooks/use-toast';

/**
 * Service Worker registration and lifecycle management
 * Handles PWA installation, updates, and offline functionality
 */

let wb: Workbox | undefined;

export function registerServiceWorker() {
  if ('serviceWorker' in navigator && import.meta.env.PROD) {
    wb = new Workbox('/sw.js');

    // Service worker activated
    wb.addEventListener('activated', (event) => {
      // No need to show toast on first activation
      if (!event.isUpdate) {
        console.log('Service Worker activated for the first time');
      }
    });

    // Service worker waiting (update available)
    wb.addEventListener('waiting', () => {
      const toastInstance = toast({
        title: 'Nueva versión disponible',
        description: 'Haz clic en actualizar para obtener la última versión',
        duration: Infinity, // Keep toast visible until user acts
      });

      // Add a button to trigger update (handled outside toast API)
      setTimeout(() => {
        const updateButton = document.createElement('button');
        updateButton.textContent = 'Actualizar';
        updateButton.className = 'btn-primary';
        updateButton.onclick = () => {
          wb?.messageSkipWaiting();
          toastInstance.dismiss();
        };
      }, 100);
    });

    // Service worker controlling
    wb.addEventListener('controlling', () => {
      window.location.reload();
    });

    // Register the service worker
    wb.register().catch((error) => {
      console.error('Service Worker registration failed:', error);
    });
  }
}

/**
 * Check for service worker updates manually
 */
export function checkForUpdates() {
  if (wb) {
    wb.update().catch((error) => {
      console.error('Service Worker update check failed:', error);
    });
  }
}

/**
 * Unregister service worker (for development/debugging)
 */
export async function unregisterServiceWorker() {
  if ('serviceWorker' in navigator) {
    const registrations = await navigator.serviceWorker.getRegistrations();
    for (const registration of registrations) {
      await registration.unregister();
    }
  }
}
