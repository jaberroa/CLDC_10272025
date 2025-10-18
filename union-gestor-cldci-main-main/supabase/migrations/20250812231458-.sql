-- Drop existing policies on miembros table
DROP POLICY IF EXISTS "Admins and moderators can manage members" ON public.miembros;
DROP POLICY IF EXISTS "Users can view members of their organizations" ON public.miembros;

-- Create more granular policies for miembros table

-- Admins can manage all members
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

-- Users can only view their own complete member record
CREATE POLICY "Users can view their own member record"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Users can update only their own member record
CREATE POLICY "Users can update their own member record"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- Create a function to get public member information for organization members
CREATE OR REPLACE FUNCTION public.get_public_members(org_id uuid DEFAULT NULL)
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
  WHERE 
    -- Check if user has access to this organization
    EXISTS (
      SELECT 1 FROM user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND (ur.organizacion_id = m.organizacion_id OR ur.role = 'admin'::app_role)
    )
    AND (org_id IS NULL OR m.organizacion_id = org_id);
$$;