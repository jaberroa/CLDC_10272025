-- =====================================================
-- CORRECCIÓN DE SEGURIDAD: Eliminar Security Definer Views
-- Las vistas con SECURITY DEFINER son un riesgo de seguridad
-- porque ejecutan con privilegios del creador, no del usuario
-- Fecha: 2025-10-09
-- =====================================================

-- Las tablas base ya tienen políticas RLS apropiadas que controlan
-- el acceso a datos sensibles, por lo que estas vistas no son necesarias

-- Eliminar vista de miembros seguros
DROP VIEW IF EXISTS public.miembros_seguros CASCADE;

-- Eliminar vista de seccionales seguras  
DROP VIEW IF EXISTS public.seccionales_seguras CASCADE;

-- Eliminar vista de delivery_orders seguros (si existe)
DROP VIEW IF EXISTS public.delivery_orders_seguras CASCADE;

-- Comentario explicativo
COMMENT ON TABLE public.miembros IS 'Tabla de miembros con RLS policies que controlan acceso según roles. Los datos sensibles están protegidos por las políticas RLS que limitan visibilidad según el rol del usuario (admin/moderador pueden ver todo, usuarios regulares solo su propio registro).';

COMMENT ON TABLE public.seccionales IS 'Tabla de seccionales con RLS policies. Los datos de contacto (email, teléfono, dirección) están protegidos y solo visibles para admins, moderadores y coordinadores.';

COMMENT ON TABLE public.delivery_orders IS 'Tabla de órdenes de entrega con RLS policies que limitan visibilidad a conductores asignados y admins/moderadores de la compañía.';

-- Registrar evento de seguridad
INSERT INTO public.security_audit_log (
  user_id, 
  action, 
  resource_type, 
  success, 
  additional_data
)
VALUES (
  auth.uid(), 
  'REMOVE_SECURITY_DEFINER_VIEWS', 
  'DATABASE_VIEWS', 
  true,
  jsonb_build_object(
    'timestamp', now(),
    'changes', ARRAY[
      'Dropped miembros_seguros view',
      'Dropped seccionales_seguras view', 
      'Dropped delivery_orders_seguras view (if existed)'
    ],
    'reason', 'Security definer views bypass RLS and pose security risk',
    'mitigation', 'Base tables already have proper RLS policies that handle access control',
    'compliance', 'Supabase security linter requirement'
  )
);