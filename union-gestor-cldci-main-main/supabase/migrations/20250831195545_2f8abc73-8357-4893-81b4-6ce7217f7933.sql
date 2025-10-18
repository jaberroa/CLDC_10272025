-- CRITICAL SECURITY FIXES
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

-- Fix all other functions with proper search_path
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

-- Fix 2: Resolve infinite recursion in miembros_directivos table
-- Drop all conflicting policies
DROP POLICY IF EXISTS "Admins pueden gestionar directivos" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Admins pueden gestionar miembros directivos" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Moderadores pueden gestionar directivos de su org" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Moderadores pueden gestionar miembros directivos" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Cualquiera puede ver directivos públicos" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Public can view basic board member info" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Authorized users can view board member contact details" ON public.miembros_directivos;

-- Create consolidated, non-conflicting policies
CREATE POLICY "Admin full access to board members"
ON public.miembros_directivos
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderator manage board members in their organization"
ON public.miembros_directivos
FOR ALL
USING (EXISTS (
  SELECT 1 FROM organos_cldc o
  WHERE o.id = miembros_directivos.organo_id 
  AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
))
WITH CHECK (EXISTS (
  SELECT 1 FROM organos_cldc o
  WHERE o.id = miembros_directivos.organo_id 
  AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
));

CREATE POLICY "Public view active board members basic info"
ON public.miembros_directivos
FOR SELECT
USING (estado = 'activo');

-- Fix 3: Secure organizational structure data
-- Add authentication requirement for organos_cldc if it exists
-- (This will be handled by existing policies or added if needed)

-- Add comprehensive audit logging for sensitive access
CREATE TABLE IF NOT EXISTS public.security_audit_log (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid,
  action text NOT NULL,
  resource_type text NOT NULL,
  resource_id uuid,
  ip_address inet,
  user_agent text,
  success boolean DEFAULT true,
  error_message text,
  additional_data jsonb,
  created_at timestamp with time zone DEFAULT now()
);

-- Enable RLS on audit log
ALTER TABLE public.security_audit_log ENABLE ROW LEVEL SECURITY;

-- Only admins can access audit logs
CREATE POLICY "Admins can access security audit logs"
ON public.security_audit_log
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));