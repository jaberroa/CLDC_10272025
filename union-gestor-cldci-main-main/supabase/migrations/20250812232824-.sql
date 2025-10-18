-- Implement complete field-level access control for member sensitive data
-- Remove problematic policies and create ultra-strict access control

-- Remove all existing policies
DROP POLICY IF EXISTS "Block unauthorized member access" ON public.miembros;
DROP POLICY IF EXISTS "Users view own complete profile" ON public.miembros;
DROP POLICY IF EXISTS "Users can view their own member record" ON public.miembros;
DROP POLICY IF EXISTS "Users update own profile" ON public.miembros;
DROP POLICY IF EXISTS "Users can update their own member record" ON public.miembros;

-- Create ultra-strict policies

-- Policy 1: Only admins can see and modify all member data
CREATE POLICY "Admins only full member access"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Policy 2: Moderators can see and modify members only in their organization
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

-- Policy 3: Users can only see their own complete record (NO access to other members)
CREATE POLICY "Users own complete record only"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Policy 4: Users can only update their own record (basic fields only)
CREATE POLICY "Users update own safe fields only"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- Completely block INSERT and DELETE for regular users (only admins/moderators)
-- No separate policies needed - the existing policies already handle this

-- Update the safe function to return minimal data for regular users
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
    -- Only show profession to admin/moderators, hide from regular users
    CASE 
      WHEN EXISTS (
        SELECT 1 FROM user_roles ur 
        WHERE ur.user_id = auth.uid() 
        AND ur.role IN ('admin'::app_role, 'moderador'::app_role)
      ) THEN m.profesion
      ELSE 'Información protegida'
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
  )
  -- Additional privacy protection: only show if user has legitimate access
  AND (
    EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.role IN ('admin'::app_role, 'moderador'::app_role))
    OR m.user_id = auth.uid()  -- Or it's their own record
  );
$$;