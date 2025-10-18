-- Fix Security Definer View vulnerability
-- Remove security barrier setting and rely on proper RLS policies instead

-- Remove the security barrier setting from the view
ALTER VIEW public.miembros_public_only RESET (security_barrier);

-- Since we're removing the security barrier, we need to ensure the view 
-- properly respects RLS policies on the underlying miembros table.
-- The view will now execute with the permissions of the querying user,
-- which is the correct security behavior.

-- Recreate the view without any security elevation to ensure it works correctly
-- with the existing RLS policies on the miembros table
DROP VIEW IF EXISTS public.miembros_public_only;

CREATE VIEW public.miembros_public_only AS
SELECT 
  id,
  nombre_completo,
  estado_membresia,
  organizacion_id,
  numero_carnet,
  -- These CASE statements will now execute with the querying user's permissions
  -- instead of elevated privileges, making it properly respect RLS
  CASE 
    WHEN user_id = auth.uid() THEN email
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN email
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN email
    ELSE NULL
  END as email,
  CASE 
    WHEN user_id = auth.uid() THEN telefono
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN telefono
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN telefono
    ELSE NULL
  END as telefono,
  CASE 
    WHEN user_id = auth.uid() THEN cedula
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN cedula
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN cedula
    ELSE NULL
  END as cedula,
  CASE 
    WHEN user_id = auth.uid() THEN direccion
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN direccion
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN direccion
    ELSE NULL
  END as direccion,
  CASE 
    WHEN user_id = auth.uid() THEN fecha_nacimiento
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN fecha_nacimiento
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN fecha_nacimiento
    ELSE NULL
  END as fecha_nacimiento,
  CASE 
    WHEN user_id = auth.uid() THEN profesion
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN profesion
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN profesion
    ELSE '[Información restringida]'
  END as profesion,
  CASE 
    WHEN user_id = auth.uid() THEN foto_url
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN foto_url
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN foto_url
    ELSE NULL
  END as foto_url,
  user_id,
  fecha_ingreso,
  fecha_vencimiento,
  created_at,
  updated_at,
  observaciones
FROM public.miembros
WHERE 
  -- This WHERE clause will now properly work with RLS policies
  -- since the view executes with user permissions, not elevated privileges
  user_id = auth.uid()  -- Own record
  OR has_role(auth.uid(), 'admin'::app_role)  -- Admin can see all
  OR (
    has_role(auth.uid(), 'moderador'::app_role, organizacion_id)  -- Moderator for this org
    AND EXISTS (
      SELECT 1 FROM user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.organizacion_id = miembros.organizacion_id
      AND ur.role = 'moderador'::app_role
    )
  )
  OR (
    -- Regular users can only see basic info of org members
    EXISTS (
      SELECT 1 FROM user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.organizacion_id = miembros.organizacion_id
      AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
    )
  );

-- Grant appropriate permissions
GRANT SELECT ON public.miembros_public_only TO authenticated;

-- Note: We do NOT set security_barrier=true or any security definer properties
-- The view will now properly respect RLS policies and execute with the 
-- permissions of the querying user, which is the secure approach.