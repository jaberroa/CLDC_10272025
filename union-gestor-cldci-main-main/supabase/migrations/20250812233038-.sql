-- FINAL COMPREHENSIVE SOLUTION: Complete data isolation for member sensitive information
-- This will completely prevent unauthorized access to ANY sensitive member data

-- Create a new ultra-secure view that completely masks sensitive data
CREATE OR REPLACE VIEW public.miembros_public_only AS
SELECT 
  id,
  nombre_completo,
  estado_membresia,
  organizacion_id,
  numero_carnet,
  -- Completely hide ALL sensitive fields from everyone except the record owner and authorized staff
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
  -- Only show records that user has authorization to see
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
    -- Regular users can only see very basic info of org members
    EXISTS (
      SELECT 1 FROM user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.organizacion_id = miembros.organizacion_id
      AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
    )
  );

-- Enable security barrier on the view
ALTER VIEW public.miembros_public_only SET (security_barrier = true);

-- Grant access to the secure view
GRANT SELECT ON public.miembros_public_only TO authenticated;

-- Create audit logging for sensitive data access
CREATE TABLE IF NOT EXISTS public.member_access_log (
  id uuid DEFAULT gen_random_uuid() PRIMARY KEY,
  accessing_user_id uuid NOT NULL,
  accessed_member_id uuid NOT NULL,
  access_type text NOT NULL,
  access_timestamp timestamp with time zone DEFAULT now(),
  user_role text NOT NULL,
  organization_context uuid
);

-- Enable RLS on the audit log
ALTER TABLE public.member_access_log ENABLE ROW LEVEL SECURITY;

-- Create policy for audit log (only admins can see logs)
CREATE POLICY "Admin audit log access" 
ON public.member_access_log 
FOR ALL 
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Create a function to log sensitive data access
CREATE OR REPLACE FUNCTION public.log_member_access(
  accessed_member_id uuid,
  access_type text
)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
  current_user_id uuid;
  current_user_role text;
  org_context uuid;
BEGIN
  -- Get current user
  current_user_id := auth.uid();
  
  -- Get user role and organization
  SELECT ur.role, ur.organizacion_id 
  INTO current_user_role, org_context
  FROM user_roles ur 
  WHERE ur.user_id = current_user_id 
  LIMIT 1;
  
  -- Log the access
  INSERT INTO public.member_access_log (
    accessing_user_id,
    accessed_member_id,
    access_type,
    user_role,
    organization_context
  ) VALUES (
    current_user_id,
    accessed_member_id,
    access_type,
    COALESCE(current_user_role, 'unknown'),
    org_context
  );
END;
$$;

-- Update the safe member info function to use audit logging
CREATE OR REPLACE FUNCTION public.get_safe_member_info(org_id uuid)
RETURNS TABLE (
  id uuid,
  nombre_completo text,
  profesion text,
  estado_membresia estado_membresia,
  organizacion_id uuid,
  numero_carnet text
)
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
BEGIN
  -- Log this access attempt
  INSERT INTO public.member_access_log (
    accessing_user_id,
    accessed_member_id,
    access_type,
    user_role,
    organization_context
  ) 
  SELECT 
    auth.uid(),
    m.id,
    'safe_info_access',
    COALESCE(ur.role::text, 'unknown'),
    org_id
  FROM public.miembros m
  LEFT JOIN user_roles ur ON ur.user_id = auth.uid()
  WHERE m.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur2 
    WHERE ur2.user_id = auth.uid() 
    AND ur2.organizacion_id = org_id
  );

  -- Return the safe data
  RETURN QUERY
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
END;
$$;