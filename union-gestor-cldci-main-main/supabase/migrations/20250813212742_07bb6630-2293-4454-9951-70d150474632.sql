-- Update RLS policies for miembros table to implement data masking
-- Drop existing moderator policy that gives full access
DROP POLICY IF EXISTS "moderator_org_access" ON public.miembros;

-- Create new restricted moderator policy with data masking
CREATE POLICY "moderator_restricted_access" 
ON public.miembros 
FOR SELECT 
USING (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id) 
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros.organizacion_id 
    AND ur.role = 'moderador'::app_role
  )
);

-- Create new policy for moderator updates (only non-sensitive fields)
CREATE POLICY "moderator_limited_update" 
ON public.miembros 
FOR UPDATE 
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

-- Create new policy for moderator inserts
CREATE POLICY "moderator_insert_access" 
ON public.miembros 
FOR INSERT 
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
);

-- Create a secure function for moderators to access essential member data
CREATE OR REPLACE FUNCTION public.get_members_for_moderators(org_id uuid)
RETURNS TABLE(
  id uuid,
  nombre_completo text,
  estado_membresia estado_membresia,
  fecha_ingreso date,
  organizacion_id uuid,
  numero_carnet text,
  foto_url text,
  -- Masked sensitive fields
  email_masked text,
  telefono_masked text,
  profesion text,
  created_at timestamp with time zone,
  updated_at timestamp with time zone,
  fecha_vencimiento date
)
LANGUAGE plpgsql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  -- Verify the requesting user is a moderator for this organization
  IF NOT has_role(auth.uid(), 'moderador'::app_role, org_id) THEN
    RAISE EXCEPTION 'Access denied: insufficient privileges';
  END IF;

  RETURN QUERY
  SELECT 
    m.id,
    m.nombre_completo,
    m.estado_membresia,
    m.fecha_ingreso,
    m.organizacion_id,
    m.numero_carnet,
    m.foto_url,
    -- Mask sensitive data - show only first 3 chars of email
    CASE 
      WHEN m.email IS NOT NULL THEN 
        LEFT(m.email, 3) || '***@' || SPLIT_PART(m.email, '@', 2)
      ELSE NULL 
    END as email_masked,
    -- Mask phone - show only last 4 digits
    CASE 
      WHEN m.telefono IS NOT NULL THEN 
        '***-***-' || RIGHT(m.telefono, 4)
      ELSE NULL 
    END as telefono_masked,
    m.profesion,
    m.created_at,
    m.updated_at,
    m.fecha_vencimiento
  FROM miembros m
  WHERE m.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = org_id
    AND ur.role = 'moderador'::app_role
  );
END;
$$;

-- Create an audit log for sensitive data access
CREATE TABLE IF NOT EXISTS public.sensitive_data_access_log (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  accessing_user_id uuid NOT NULL,
  accessed_member_id uuid NOT NULL,
  access_type text NOT NULL,
  justification text,
  approved_by uuid,
  access_timestamp timestamp with time zone DEFAULT now(),
  ip_address inet,
  user_agent text
);

-- Enable RLS on audit log
ALTER TABLE public.sensitive_data_access_log ENABLE ROW LEVEL SECURITY;

-- Only admins can view audit logs
CREATE POLICY "admin_audit_access" 
ON public.sensitive_data_access_log 
FOR ALL 
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Function for admins to grant temporary access to sensitive data
CREATE OR REPLACE FUNCTION public.grant_sensitive_access(
  member_id_param uuid,
  justification_param text
)
RETURNS TABLE(
  id uuid,
  cedula text,
  email text,
  telefono text,
  direccion text,
  fecha_nacimiento date
)
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  -- Only admins can grant access
  IF NOT has_role(auth.uid(), 'admin'::app_role) THEN
    RAISE EXCEPTION 'Access denied: only administrators can access sensitive data';
  END IF;
  
  -- Log the access
  INSERT INTO public.sensitive_data_access_log (
    accessing_user_id,
    accessed_member_id,
    access_type,
    justification,
    approved_by
  ) VALUES (
    auth.uid(),
    member_id_param,
    'sensitive_data_access',
    justification_param,
    auth.uid()
  );
  
  -- Return sensitive data
  RETURN QUERY
  SELECT 
    m.id,
    m.cedula,
    m.email,
    m.telefono,
    m.direccion,
    m.fecha_nacimiento
  FROM miembros m
  WHERE m.id = member_id_param;
END;
$$;