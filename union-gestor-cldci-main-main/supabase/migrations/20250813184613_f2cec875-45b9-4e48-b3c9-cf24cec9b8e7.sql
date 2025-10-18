-- Enable Row Level Security on miembros_public_only table
ALTER TABLE public.miembros_public_only ENABLE ROW LEVEL SECURITY;

-- Policy: Admins can view all member data
CREATE POLICY "admin_full_access_public_members" 
ON public.miembros_public_only 
FOR SELECT 
USING (has_role(auth.uid(), 'admin'::app_role));

-- Policy: Moderators can view members from their organization
CREATE POLICY "moderator_org_access_public_members" 
ON public.miembros_public_only 
FOR SELECT 
USING (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id) 
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros_public_only.organizacion_id 
    AND ur.role = 'moderador'::app_role
  )
);

-- Policy: Regular users can only view members from their own organization
CREATE POLICY "user_org_members_only" 
ON public.miembros_public_only 
FOR SELECT 
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros_public_only.organizacion_id
  )
);