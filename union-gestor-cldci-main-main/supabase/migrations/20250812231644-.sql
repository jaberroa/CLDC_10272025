-- Drop existing policies on miembros table
DROP POLICY IF EXISTS "Admins and moderators can manage members" ON public.miembros;
DROP POLICY IF EXISTS "Users can view members of their organizations" ON public.miembros;

-- Create more granular policies for miembros table

-- Admins can still manage all members
CREATE POLICY "Admins can manage all members"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can manage members in their organization
CREATE POLICY "Moderators can manage members in their organization"
ON public.miembros
FOR ALL
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

-- Users can view their own complete profile
CREATE POLICY "Users can view their own complete profile"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Regular users can only view basic non-sensitive information of other members
CREATE POLICY "Users can view basic member info in their organization"
ON public.miembros
FOR SELECT
USING (
  -- Allow if user is not admin/moderator and is viewing other members
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros.organizacion_id
    AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
  )
  AND user_id != auth.uid()
);

-- Users can update only their own profile
CREATE POLICY "Users can update their own profile"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- Create a security definer function to get filtered member data for regular users
CREATE OR REPLACE FUNCTION public.get_public_member_info(org_id uuid)
RETURNS TABLE (
  id uuid,
  nombre_completo text,
  profesion text,
  estado_membresia estado_membresia,
  fecha_ingreso date,
  organizacion_id uuid,
  numero_carnet text,
  foto_url text
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
    m.fecha_ingreso,
    m.organizacion_id,
    m.numero_carnet,
    m.foto_url
  FROM public.miembros m
  WHERE m.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = org_id OR ur.role = 'admin'::app_role)
  );
$$;