-- Clean all existing member policies and create ultra-strict access control

-- Drop ALL existing policies on miembros table
DROP POLICY IF EXISTS "Admins full access to members" ON public.miembros;
DROP POLICY IF EXISTS "Moderators manage org members" ON public.miembros;
DROP POLICY IF EXISTS "Admins only full member access" ON public.miembros;
DROP POLICY IF EXISTS "Moderators org member access" ON public.miembros;
DROP POLICY IF EXISTS "Users own complete record only" ON public.miembros;
DROP POLICY IF EXISTS "Users update own record only" ON public.miembros;
DROP POLICY IF EXISTS "Users update own safe fields only" ON public.miembros;

-- Create the final ultra-strict policies for maximum security

-- Policy 1: Admins can do everything
CREATE POLICY "admin_full_access"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Policy 2: Moderators can only access their organization's members
CREATE POLICY "moderator_org_access"
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

-- Policy 3: Regular users can ONLY see their own record (no other members)
CREATE POLICY "user_own_record_only"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Policy 4: Regular users can only update their own non-sensitive data
CREATE POLICY "user_update_own_only"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- NO INSERT or DELETE policies for regular users - only admins/moderators can create/delete members

-- Final security update to the safe function
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
    -- Hide profession from regular users
    CASE 
      WHEN EXISTS (
        SELECT 1 FROM user_roles ur 
        WHERE ur.user_id = auth.uid() 
        AND ur.role IN ('admin'::app_role, 'moderador'::app_role)
      ) THEN m.profesion
      ELSE '[Información protegida]'
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
  -- Critical: Only show records user is authorized to see
  AND (
    -- Admins and moderators can see all org members
    EXISTS (
      SELECT 1 FROM user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role IN ('admin'::app_role, 'moderador'::app_role)
    )
    -- Regular users can only see their own record
    OR m.user_id = auth.uid()
  );
$$;