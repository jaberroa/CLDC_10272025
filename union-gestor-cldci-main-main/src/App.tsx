import { lazy, Suspense } from 'react';
import { Toaster } from '@/components/ui/toaster';
import { Toaster as Sonner } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { QueryClientProvider } from '@tanstack/react-query';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { HelmetProvider } from 'react-helmet-async';
import { Loader2 } from 'lucide-react';
import ResetPassword from './pages/ResetPassword';
import NotFound from './pages/NotFound';
import { SiteHeader } from './components/layout/SiteHeader';
import { SiteFooter } from './components/layout/SiteFooter';
import { AuthProvider } from './components/auth/AuthProvider';
import { ProtectedRoute } from './components/auth/ProtectedRoute';
import { SecurityMonitor } from './components/security/SecurityMonitor';
import { queryClient } from './lib/query-client';
import { InstallPrompt } from './components/pwa/InstallPrompt';
import { OfflineIndicator } from './components/pwa/OfflineIndicator';

// Lazy load pages for code splitting and better performance
const Index = lazy(() => import('./pages/Index'));
const Dashboard = lazy(() => import('./pages/Dashboard'));
const Miembros = lazy(() => import('./pages/Miembros'));
const Directiva = lazy(() => import('./pages/Directiva'));
const Elecciones = lazy(() => import('./pages/Elecciones'));
const FormacionProfesional = lazy(() => import('./pages/FormacionProfesional'));
const Diagnostico = lazy(() => import('./pages/Diagnostico'));
const Reportes = lazy(() => import('./pages/Reportes'));
const Transparencia = lazy(() => import('./pages/Transparencia'));
const DocumentosLegales = lazy(() => import('./pages/DocumentosLegales'));
const RegistroAdecuacion = lazy(() => import('./pages/RegistroAdecuacion'));
const Integraciones = lazy(() => import('./pages/Integraciones'));
const Perfil = lazy(() => import('./pages/Perfil'));
const Premios = lazy(() => import('./pages/Premios'));

/**
 * Loading fallback component
 */
function LoadingFallback() {
  return (
    <div className="flex min-h-screen items-center justify-center">
      <Loader2 className="h-8 w-8 animate-spin text-primary" />
    </div>
  );
}

const App = () => (
  <HelmetProvider>
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        <Sonner />
        <OfflineIndicator />
        <InstallPrompt />
        <BrowserRouter>
          <AuthProvider>
            <Routes>
              {/* Public routes (authentication related) */}
              <Route path="/auth/reset-password" element={<ResetPassword />} />

              {/* Protected routes with lazy loading */}
              <Route
                path="/*"
                element={
                  <ProtectedRoute>
                    <SecurityMonitor />
                    <SiteHeader />
                    <Suspense fallback={<LoadingFallback />}>
                      <Routes>
                        <Route path="/" element={<Index />} />
                        <Route path="/dashboard" element={<Dashboard />} />
                        <Route path="/registro-adecuacion" element={<RegistroAdecuacion />} />
                        <Route path="/miembros" element={<Miembros />} />
                        <Route path="/elecciones" element={<Elecciones />} />
                        <Route path="/asambleas" element={<DocumentosLegales />} />
                        <Route path="/documentos-legales" element={<DocumentosLegales />} />
                        <Route path="/premios" element={<Premios />} />
                        <Route path="/transparencia" element={<Transparencia />} />
                        <Route path="/directiva" element={<Directiva />} />
                        <Route path="/perfil" element={<Perfil />} />
                        <Route path="/reportes" element={<Reportes />} />
                        <Route path="/integraciones" element={<Integraciones />} />
                        <Route path="/formacion-profesional" element={<FormacionProfesional />} />
                        <Route path="/diagnostico" element={<Diagnostico />} />
                        {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
                        <Route path="*" element={<NotFound />} />
                      </Routes>
                    </Suspense>
                    <SiteFooter />
                  </ProtectedRoute>
                }
              />
            </Routes>
          </AuthProvider>
        </BrowserRouter>
      </TooltipProvider>
    </QueryClientProvider>
  </HelmetProvider>
);

export default App;
