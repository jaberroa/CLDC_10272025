import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Download, X } from 'lucide-react';

/**
 * PWA Install Prompt Component
 * Shows a prompt to install the app when the browser supports it
 */

interface BeforeInstallPromptEvent extends Event {
  prompt: () => Promise<void>;
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

export const InstallPrompt = () => {
  const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null);
  const [showPrompt, setShowPrompt] = useState(false);

  useEffect(() => {
    const handler = (e: Event) => {
      e.preventDefault();
      setDeferredPrompt(e as BeforeInstallPromptEvent);
      
      // Check if user has dismissed the prompt before
      const dismissed = localStorage.getItem('pwa-install-dismissed');
      if (!dismissed) {
        setShowPrompt(true);
      }
    };

    window.addEventListener('beforeinstallprompt', handler);

    // Check if app is already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
      setShowPrompt(false);
    }

    return () => {
      window.removeEventListener('beforeinstallprompt', handler);
    };
  }, []);

  const handleInstall = async () => {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    if (outcome === 'accepted') {
      console.log('User accepted the install prompt');
    }

    setDeferredPrompt(null);
    setShowPrompt(false);
  };

  const handleDismiss = () => {
    setShowPrompt(false);
    localStorage.setItem('pwa-install-dismissed', 'true');
  };

  if (!showPrompt) return null;

  return (
    <div className="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-sm z-50 animate-in slide-in-from-bottom-5">
      <div className="bg-card border border-border rounded-lg shadow-lg p-4">
        <div className="flex items-start justify-between gap-3">
          <div className="flex-1">
            <h3 className="font-semibold text-sm mb-1">
              Instalar CLDCI
            </h3>
            <p className="text-sm text-muted-foreground mb-3">
              Instala la aplicación para acceder rápidamente y usar sin conexión
            </p>
            <div className="flex gap-2">
              <Button
                size="sm"
                onClick={handleInstall}
                className="flex-1"
              >
                <Download className="h-4 w-4 mr-2" />
                Instalar
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={handleDismiss}
              >
                Más tarde
              </Button>
            </div>
          </div>
          <Button
            size="icon"
            variant="ghost"
            className="h-6 w-6"
            onClick={handleDismiss}
          >
            <X className="h-4 w-4" />
          </Button>
        </div>
      </div>
    </div>
  );
};
