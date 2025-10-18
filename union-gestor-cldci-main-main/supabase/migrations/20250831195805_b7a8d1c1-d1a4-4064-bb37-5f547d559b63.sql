-- CRITICAL SECURITY FIXES - Phase 1
-- Fix 1: Update all SECURITY DEFINER functions to include proper search_path

-- Fix has_role function
CREATE OR REPLACE FUNCTION public.has_role(_user_id uuid, _role app_role, _org_id uuid DEFAULT NULL::uuid)
RETURNS boolean
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
  SELECT EXISTS (
    SELECT 1
    FROM public.user_roles
    WHERE user_id = _user_id
      AND role = _role
      AND (organizacion_id = _org_id OR organizacion_id IS NULL OR _org_id IS NULL)
  )
$function$;

-- Fix user_organizations function
CREATE OR REPLACE FUNCTION public.user_organizations(_user_id uuid)
RETURNS TABLE(org_id uuid, role app_role)
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
  SELECT organizacion_id, role
  FROM public.user_roles
  WHERE user_id = _user_id
$function$;

-- Fix increment_vote_count function
CREATE OR REPLACE FUNCTION public.increment_vote_count(election_id uuid)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = 'public'
AS $function$
BEGIN
  UPDATE elecciones 
  SET votos_totales = COALESCE(votos_totales, 0) + 1
  WHERE id = election_id;
END;
$function$;

-- Fix user_can_access_driver_company function
CREATE OR REPLACE FUNCTION public.user_can_access_driver_company(driver_company_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
  SELECT EXISTS (
    SELECT 1 
    FROM public.drivers d
    WHERE d.company_id = driver_company_id 
    AND d.user_id = auth.uid()
  ) OR EXISTS (
    SELECT 1 
    FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role::text = 'admin'
  );
$function$;

-- Fix handle_new_user function
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = 'public'
AS $function$
begin
  INSERT INTO public.profiles (id, email, nombre_completo)
  VALUES (
    new.id, 
    new.email, 
    COALESCE(new.raw_user_meta_data ->> 'full_name', new.email)
  );
  RETURN new;
end;
$function$;

-- Fix get_public_seccionales function
CREATE OR REPLACE FUNCTION public.get_public_seccionales()
RETURNS TABLE(id uuid, nombre text, tipo text, pais text, provincia text, ciudad text, miembros_count integer, fecha_fundacion date, estado text, organizacion_id uuid, created_at timestamp with time zone, updated_at timestamp with time zone)
LANGUAGE sql
STABLE SECURITY DEFINER
SET search_path = 'public'
AS $function$
  SELECT 
    s.id,
    s.nombre,
    s.tipo,
    s.pais,
    s.provincia,
    s.ciudad,
    s.miembros_count,
    s.fecha_fundacion,
    s.estado,
    s.organizacion_id,
    s.created_at,
    s.updated_at
  FROM seccionales s
  WHERE s.estado = 'activa';
$function$;

-- Fix remaining SECURITY DEFINER functions
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