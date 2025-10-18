-- First check and drop all existing policies on miembros table
DROP POLICY IF EXISTS "Admins can manage all members" ON public.miembros;
DROP POLICY IF EXISTS "Moderators can manage members in their organization" ON public.miembros;
DROP POLICY IF EXISTS "Users can view their own complete profile" ON public.miembros;
DROP POLICY IF EXISTS "Users can view basic member info in their organization" ON public.miembros;
DROP POLICY IF EXISTS "Users can update their own profile" ON public.miembros;

-- Now create the new secure policies

-- Admins have full access
CREATE POLICY "Admins full access to members"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can manage members in their organization
CREATE POLICY "Moderators manage org members"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

-- Users can view their own complete profile
CREATE POLICY "Users view own complete profile"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Users can update only their own profile
CREATE POLICY "Users update own profile"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- Create a security definer function to get safe member data for regular users
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
STABLE
AS $$
  SELECT 
    m.id,
    m.nombre_completo,
    m.profesion,
    m.estado_membresia,
    m.organizacion_id,
    m.numero_carnet
  FROM public.miembros m
  WHERE m.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = org_id OR ur.role = 'admin'::app_role)
  );
$$;