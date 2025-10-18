-- Implement complete field-level access control for member sensitive data
-- Create a view that completely hides sensitive fields from unauthorized users

-- First, remove all existing problematic policies
DROP POLICY IF EXISTS "Block unauthorized member access" ON public.miembros;
DROP POLICY IF EXISTS "Users view own complete profile" ON public.miembros;
DROP POLICY IF EXISTS "Users can view their own member record" ON public.miembros;
DROP POLICY IF EXISTS "Users update own profile" ON public.miembros;
DROP POLICY IF EXISTS "Users can update their own member record" ON public.miembros;

-- Create ultra-strict policies that completely prevent unauthorized access

-- Policy 1: Only admins can see all fields of all members
CREATE POLICY "Admins only full member access"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Policy 2: Moderators can see all fields but only for their organization
CREATE POLICY "Moderators org member access"
ON public.miembros
FOR ALL
USING (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros.organizacion_id
    AND ur.role = 'moderador'::app_role
  )
)
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
);

-- Policy 3: Users can only see their own complete record
CREATE POLICY "Users own complete record only"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Policy 4: Users can only update their own non-sensitive fields
CREATE POLICY "Users update own safe fields only"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (
  user_id = auth.uid()
  -- Additional check: ensure sensitive fields aren't being modified by regular users
  AND (OLD.cedula IS NOT DISTINCT FROM NEW.cedula)  -- cedula cannot be changed
  AND (OLD.email IS NOT DISTINCT FROM NEW.email)   -- email cannot be changed  
  AND (OLD.organizacion_id IS NOT DISTINCT FROM NEW.organizacion_id) -- org cannot be changed
);

-- Create a completely safe view for public member information
CREATE OR REPLACE VIEW public.miembros_safe_view AS
SELECT 
  id,
  nombre_completo,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN profesion
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN profesion
    ELSE 'Información restringida'
  END as profesion,
  estado_membresia,
  organizacion_id,
  numero_carnet,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN foto_url
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN foto_url
    WHEN user_id = auth.uid() THEN foto_url
    ELSE NULL
  END as foto_url,
  -- Completely hide all sensitive fields from regular users
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN email
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN email
    WHEN user_id = auth.uid() THEN email
    ELSE NULL
  END as email,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN telefono
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN telefono
    WHEN user_id = auth.uid() THEN telefono
    ELSE NULL
  END as telefono,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN cedula
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN cedula
    WHEN user_id = auth.uid() THEN cedula
    ELSE NULL
  END as cedula,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN direccion
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN direccion
    WHEN user_id = auth.uid() THEN direccion
    ELSE NULL
  END as direccion,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN fecha_nacimiento
    WHEN has_role(auth.uid(), 'moderador'::app_role, organizacion_id) THEN fecha_nacimiento
    WHEN user_id = auth.uid() THEN fecha_nacimiento
    ELSE NULL
  END as fecha_nacimiento
FROM public.miembros
WHERE 
  -- Only show records user has permission to see
  has_role(auth.uid(), 'admin'::app_role)
  OR has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  OR user_id = auth.uid()
  OR EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros.organizacion_id
  );

-- Enable RLS on the view  
ALTER VIEW public.miembros_safe_view SET (security_barrier = true);

-- Grant access to the safe view
GRANT SELECT ON public.miembros_safe_view TO authenticated;

-- Update the existing safe function to be even more restrictive
CREATE OR REPLACE FUNCTION public.get_safe_member_info(org_id uuid)
RETURNS TABLE (
  id uuid,
  nombre_completo text,
  profesion text,
  estado_membresia estado_membresia,
  organizacion_id uuid,
  numero_carnet text
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
  SELECT 
    m.id,
    m.nombre_completo,
    -- Only show profession to admin/moderators
    CASE 
      WHEN EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.role IN ('admin'::app_role, 'moderador'::app_role)) 
      THEN m.profesion
      ELSE 'Información restringida'
    END as profesion,
    m.estado_membresia,
    m.organizacion_id,
    m.numero_carnet
  FROM public.miembros m
  WHERE m.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = org_id
  );
$$;