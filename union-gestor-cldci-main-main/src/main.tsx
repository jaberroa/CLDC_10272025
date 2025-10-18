import { createRoot } from 'react-dom/client';
import App from './App.tsx';
import './index.css';
import { registerServiceWorker } from './lib/pwa/register-sw';
import { initSentry } from './lib/monitoring/sentry';
import { initPostHog } from './lib/monitoring/posthog';

// Initialize monitoring tools
initSentry();
initPostHog();

// Register service worker for PWA
registerServiceWorker();

createRoot(document.getElementById("root")!).render(<App />);
