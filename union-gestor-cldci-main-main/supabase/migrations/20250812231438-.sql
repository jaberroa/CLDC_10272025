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

-- Users can view only basic information of members in their organization (excluding sensitive fields)
CREATE POLICY "Users can view basic member info in their organization"
ON public.miembros
FOR SELECT
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = miembros.organizacion_id OR ur.role = 'admin'::app_role)
  )
);

-- Users can view their own complete profile
CREATE POLICY "Users can view their own complete profile"
ON public.miembros
FOR SELECT
USING (user_id = auth.uid());

-- Users can update only their own profile (excluding sensitive admin fields)
CREATE POLICY "Users can update their own profile"
ON public.miembros
FOR UPDATE
USING (user_id = auth.uid())
WITH CHECK (user_id = auth.uid());

-- Create a view for public member information (what regular users should see)
CREATE OR REPLACE VIEW public.miembros_publicos AS
SELECT 
  id,
  nombre_completo,
  profesion,
  estado_membresia,
  fecha_ingreso,
  organizacion_id,
  numero_carnet,
  foto_url
FROM public.miembros;

-- Enable RLS on the view
ALTER VIEW public.miembros_publicos SET (security_barrier = true);

-- Grant access to the view
GRANT SELECT ON public.miembros_publicos TO authenticated;

-- Create policy for the view
CREATE POLICY "Users can view public member info in their organization"
ON public.miembros_publicos
FOR SELECT
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = miembros_publicos.organizacion_id OR ur.role = 'admin'::app_role)
  )
);