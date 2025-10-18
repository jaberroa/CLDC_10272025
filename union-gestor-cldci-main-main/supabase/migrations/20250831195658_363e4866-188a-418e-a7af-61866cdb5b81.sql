-- Fix remaining functions with missing search_path
CREATE OR REPLACE FUNCTION public.update_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = 'public'
AS $function$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$function$;

CREATE OR REPLACE FUNCTION public.update_delivery_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = 'public'
AS $function$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$function$;

CREATE OR REPLACE FUNCTION public.update_updated_at_organos()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = 'public'
AS $function$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$function$;

CREATE OR REPLACE FUNCTION public.get_seccional_contact_details(seccional_id uuid)
RETURNS TABLE(id uuid, telefono text, email text, direccion text)
LANGUAGE plpgsql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
BEGIN
  -- Check if user is authorized to view contact details
  IF NOT (
    has_role(auth.uid(), 'admin'::app_role) 
    OR 
    EXISTS (
      SELECT 1 FROM seccionales s
      WHERE s.id = seccional_id
      AND (
        has_role(auth.uid(), 'moderador'::app_role, s.organizacion_id)
        OR
        EXISTS (
          SELECT 1 FROM miembros m 
          WHERE m.id = s.coordinador_id 
          AND m.user_id = auth.uid()
        )
      )
    )
  ) THEN
    RAISE EXCEPTION 'Access denied: insufficient privileges to view contact details';
  END IF;

  -- Return contact details for authorized users
  RETURN QUERY
  SELECT 
    s.id,
    s.telefono,
    s.email,
    s.direccion
  FROM seccionales s
  WHERE s.id = seccional_id;
END;
$function$;

CREATE OR REPLACE FUNCTION public.get_member_sensitive_data(member_id_param uuid, justification_param text DEFAULT NULL::text)
RETURNS TABLE(id uuid, cedula text, email text, telefono text, direccion text, fecha_nacimiento date)
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = 'public'
AS $function$
DECLARE
  current_user_role text;
  org_context uuid;
BEGIN
  -- Get current user role and organization context
  SELECT ur.role::text, ur.organizacion_id 
  INTO current_user_role, org_context
  FROM public.user_roles ur 
  WHERE ur.user_id = auth.uid() 
  LIMIT 1;
  
  -- Only admins and moderators can access sensitive data
  IF NOT (
    current_user_role = 'admin' OR 
    EXISTS (
      SELECT 1 FROM public.miembros m 
      JOIN public.user_roles ur ON ur.organizacion_id = m.organizacion_id
      WHERE m.id = member_id_param 
      AND ur.user_id = auth.uid() 
      AND ur.role::text = 'moderador'
    )
  ) THEN
    RAISE EXCEPTION 'Access denied: insufficient privileges to access sensitive member data';
  END IF;
  
  -- Log the access attempt
  INSERT INTO public.sensitive_data_access_audit (
    accessing_user_id,
    accessed_member_id,
    access_type,
    accessed_fields,
    justification
  ) VALUES (
    auth.uid(),
    member_id_param,
    'sensitive_data_access',
    ARRAY['cedula', 'email', 'telefono', 'direccion', 'fecha_nacimiento'],
    COALESCE(justification_param, 'Administrative access')
  );
  
  -- Return the sensitive data
  RETURN QUERY
  SELECT 
    m.id,
    m.cedula,
    m.email,
    m.telefono,
    m.direccion,
    m.fecha_nacimiento
  FROM public.miembros m
  WHERE m.id = member_id_param;
END;
$function$;

-- Fix all remaining SECURITY DEFINER functions
CREATE OR REPLACE FUNCTION public.get_public_members(org_id uuid DEFAULT NULL::uuid)
RETURNS TABLE(id uuid, nombre_completo text, profesion text, estado_membresia estado_membresia, fecha_ingreso date, organizacion_id uuid, numero_carnet text, foto_url text)
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
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
$function$;

-- Continue with other functions...
CREATE OR REPLACE FUNCTION public.get_public_miembros_directivos(org_id uuid DEFAULT NULL::uuid)
RETURNS TABLE(id uuid, miembro_id uuid, organo_id uuid, cargo_id uuid, periodo text, fecha_inicio date, fecha_fin date, estado text, es_presidente boolean, semblanza text, foto_url text, created_at timestamp with time zone, updated_at timestamp with time zone)
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
  SELECT 
    md.id,
    md.miembro_id,
    md.organo_id,
    md.cargo_id,
    md.periodo,
    md.fecha_inicio,
    md.fecha_fin,
    md.estado,
    md.es_presidente,
    md.semblanza,
    md.foto_url,
    md.created_at,
    md.updated_at
  FROM miembros_directivos md
  WHERE md.estado = 'activo'
  AND (org_id IS NULL OR EXISTS (
    SELECT 1 FROM organos_cldc o 
    WHERE o.id = md.organo_id 
    AND o.organizacion_id = org_id
  ));
$function$;