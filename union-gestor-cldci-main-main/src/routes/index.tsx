import { lazy, Suspense } from 'react';
import { Routes, Route } from 'react-router-dom';
import { Loader2 } from 'lucide-react';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { SecurityMonitor } from '@/components/security/SecurityMonitor';
import { SiteHeader } from '@/components/layout/SiteHeader';
import { SiteFooter } from '@/components/layout/SiteFooter';
import ResetPassword from '@/pages/ResetPassword';
import NotFound from '@/pages/NotFound';

// Lazy load pages for code splitting and better performance
const Index = lazy(() => import('@/pages/Index'));
const Dashboard = lazy(() => import('@/pages/Dashboard'));
const Miembros = lazy(() => import('@/pages/Miembros'));
const Directiva = lazy(() => import('@/pages/Directiva'));
const Elecciones = lazy(() => import('@/pages/Elecciones'));
const FormacionProfesional = lazy(() => import('@/pages/FormacionProfesional'));
const Diagnostico = lazy(() => import('@/pages/Diagnostico'));
const Reportes = lazy(() => import('@/pages/Reportes'));
const Transparencia = lazy(() => import('@/pages/Transparencia'));
const DocumentosLegales = lazy(() => import('@/pages/DocumentosLegales'));
const RegistroAdecuacion = lazy(() => import('@/pages/RegistroAdecuacion'));
const Integraciones = lazy(() => import('@/pages/Integraciones'));
const Delivery = lazy(() => import('@/pages/Delivery'));
const Perfil = lazy(() => import('@/pages/Perfil'));
const Premios = lazy(() => import('@/pages/Premios'));

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

/**
 * Main application routes with lazy loading
 */
export function AppRoutes() {
  return (
    <Routes>
      {/* Public route */}
      <Route path="/auth/reset-password" element={<ResetPassword />} />

      {/* Protected routes with layout */}
      <Route
        path="/*"
        element={
          <ProtectedRoute>
            <SecurityMonitor />
            <SiteHeader />
            <main className="min-h-screen">
              <Suspense fallback={<LoadingFallback />}>
                <Routes>
                  <Route index element={<Index />} />
                  <Route path="/dashboard" element={<Dashboard />} />
                  <Route path="/miembros" element={<Miembros />} />
                  <Route path="/directiva" element={<Directiva />} />
                  <Route path="/elecciones" element={<Elecciones />} />
                  <Route path="/formacion" element={<FormacionProfesional />} />
                  <Route path="/diagnostico" element={<Diagnostico />} />
                  <Route path="/reportes" element={<Reportes />} />
                  <Route path="/transparencia" element={<Transparencia />} />
                  <Route path="/documentos-legales" element={<DocumentosLegales />} />
                  <Route path="/registro-adecuacion" element={<RegistroAdecuacion />} />
                  <Route path="/integraciones" element={<Integraciones />} />
                  <Route path="/delivery" element={<Delivery />} />
                  <Route path="/perfil" element={<Perfil />} />
                  <Route path="/premios" element={<Premios />} />
                  <Route path="*" element={<NotFound />} />
                </Routes>
              </Suspense>
            </main>
            <SiteFooter />
          </ProtectedRoute>
        }
      />
    </Routes>
  );
}
