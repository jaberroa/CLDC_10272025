-- PHASE 1: Critical Security Fixes (Simplified without enum casting issues)

-- Fix 1: Resolve infinite recursion in drivers table RLS policies
-- Drop problematic policies and recreate them properly
DROP POLICY IF EXISTS "Company operators can view their drivers" ON drivers;
DROP POLICY IF EXISTS "Drivers can view their own data" ON drivers;

-- Create security definer function to check driver company access (without enum casting)
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
    AND ur.role::text = 'admin'
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

-- Fix 2: Secure database functions - add proper search_path and update existing functions
CREATE OR REPLACE FUNCTION public.update_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = ''
AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = ''
AS $$
begin
  INSERT INTO public.profiles (id, email, nombre_completo)
  VALUES (
    new.id, 
    new.email, 
    COALESCE(new.raw_user_meta_data ->> 'full_name', new.email)
  );
  RETURN new;
end;
$$;

CREATE OR REPLACE FUNCTION public.update_delivery_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = ''
AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
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

-- Create policy for audit logs (using text comparison to avoid enum issues)
CREATE POLICY "Only admins can view audit logs"
ON public.sensitive_data_access_audit
FOR ALL
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role::text = 'admin'
  )
)
WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role::text = 'admin'
  )
);

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
$$;