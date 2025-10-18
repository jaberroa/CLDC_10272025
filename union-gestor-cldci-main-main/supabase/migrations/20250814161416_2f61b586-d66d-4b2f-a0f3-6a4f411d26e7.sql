-- PHASE 1: Critical Security Fixes (Corrected enum values)

-- Fix 1: Resolve infinite recursion in drivers table RLS policies
-- Drop problematic policies and recreate them properly
DROP POLICY IF EXISTS "Company operators can view their drivers" ON drivers;
DROP POLICY IF EXISTS "Drivers can view their own data" ON drivers;

-- Create security definer function to check driver company access
CREATE OR REPLACE FUNCTION public.user_can_access_driver_company(driver_company_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = ''
AS $$
  SELECT EXISTS (
    SELECT 1 
    FROM public.drivers d
    WHERE d.company_id = driver_company_id 
    AND d.user_id = auth.uid()
  ) OR EXISTS (
    SELECT 1 
    FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role = 'admin'::app_role
  );
$$;

-- Recreate driver policies without recursion
CREATE POLICY "Company operators can view their drivers"
ON drivers
FOR SELECT
TO authenticated
USING (public.user_can_access_driver_company(company_id));

CREATE POLICY "Drivers can view their own data"
ON drivers
FOR SELECT
TO authenticated
USING (user_id = auth.uid());

-- Fix 2: Secure database functions - add proper search_path
CREATE OR REPLACE FUNCTION public.has_role(_user_id uuid, _role app_role, _org_id uuid DEFAULT NULL::uuid)
RETURNS boolean
LANGUAGE sql
STABLE 
SECURITY DEFINER
SET search_path = ''
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.user_roles
    WHERE user_id = _user_id
      AND role = _role
      AND (organizacion_id = _org_id OR organizacion_id IS NULL OR _org_id IS NULL)
  )
$$;

CREATE OR REPLACE FUNCTION public.user_organizations(_user_id uuid)
RETURNS TABLE(org_id uuid, role app_role)
LANGUAGE sql
STABLE 
SECURITY DEFINER
SET search_path = ''
AS $$
  SELECT organizacion_id, role
  FROM public.user_roles
  WHERE user_id = _user_id
$$;

CREATE OR REPLACE FUNCTION public.increment_vote_count(election_id uuid)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = ''
AS $$
BEGIN
  UPDATE public.elecciones 
  SET votos_totales = COALESCE(votos_totales, 0) + 1
  WHERE id = election_id;
END;
$$;

CREATE OR REPLACE FUNCTION public.get_public_members(org_id uuid DEFAULT NULL::uuid)
RETURNS TABLE(id uuid, nombre_completo text, profesion text, estado_membresia estado_membresia, fecha_ingreso date, organizacion_id uuid, numero_carnet text, foto_url text)
LANGUAGE sql
STABLE 
SECURITY DEFINER
SET search_path = ''
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
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND (ur.organizacion_id = m.organizacion_id OR ur.role = 'admin'::app_role)
    )
    AND (org_id IS NULL OR m.organizacion_id = org_id);
$$;

CREATE OR REPLACE FUNCTION public.get_budget_summary(org_id uuid)
RETURNS TABLE(categoria text, periodo text, presupuesto_total numeric, organizacion_id uuid)
LANGUAGE sql
STABLE 
SECURITY DEFINER
SET search_path = ''
AS $$
  SELECT 
    p.categoria,
    p.periodo,
    p.monto_presupuestado as presupuesto_total,
    p.organizacion_id
  FROM public.presupuestos p
  WHERE p.organizacion_id = org_id
  AND p.activo = true
  AND EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = org_id
  );
$$;

-- Fix 3: Enhanced member data security with access logging
CREATE TABLE IF NOT EXISTS public.sensitive_data_access_audit (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  accessing_user_id uuid NOT NULL,
  accessed_member_id uuid NOT NULL,
  access_type text NOT NULL,
  accessed_fields text[] NOT NULL,
  justification text,
  ip_address inet,
  user_agent text,
  access_timestamp timestamp with time zone DEFAULT now()
);

ALTER TABLE public.sensitive_data_access_audit ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Only admins can view audit logs"
ON public.sensitive_data_access_audit
FOR ALL
TO authenticated
USING (public.has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (public.has_role(auth.uid(), 'admin'::app_role));

-- Enhanced function for secure member data access with automatic logging
CREATE OR REPLACE FUNCTION public.get_member_sensitive_data(member_id_param uuid, justification_param text DEFAULT NULL)
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
SET search_path = ''
AS $$
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
    public.has_role(auth.uid(), 'admin'::app_role) OR 
    EXISTS (
      SELECT 1 FROM public.miembros m 
      JOIN public.user_roles ur ON ur.organizacion_id = m.organizacion_id
      WHERE m.id = member_id_param 
      AND ur.user_id = auth.uid() 
      AND ur.role = 'moderador'::app_role
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
$$;

-- Fix 4: Strengthen member RLS policies with better data protection
DROP POLICY IF EXISTS "moderator_restricted_access" ON miembros;

CREATE POLICY "moderator_restricted_access_enhanced"
ON miembros
FOR SELECT
TO authenticated
USING (
  -- Admins can see everything
  public.has_role(auth.uid(), 'admin'::app_role) OR
  -- Moderators can see limited data for their organization
  (
    public.has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.organizacion_id = miembros.organizacion_id 
      AND ur.role = 'moderador'::app_role
    )
  ) OR
  -- Users can only see their own record
  user_id = auth.uid()
);