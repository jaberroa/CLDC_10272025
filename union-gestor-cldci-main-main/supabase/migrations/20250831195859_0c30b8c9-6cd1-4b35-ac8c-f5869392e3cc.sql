-- CRITICAL SECURITY FIXES - Phase 2
-- Fix infinite recursion and policy conflicts in miembros_directivos

-- First, check if we need to drop existing policies more carefully
DO $$ 
BEGIN
    -- Drop conflicting policies if they exist
    DROP POLICY IF EXISTS "Admins pueden gestionar directivos" ON public.miembros_directivos;
    DROP POLICY IF EXISTS "Moderadores pueden gestionar directivos de su org" ON public.miembros_directivos;
    DROP POLICY IF EXISTS "Cualquiera puede ver directivos públicos" ON public.miembros_directivos;
    
    -- Drop any duplicate admin policies
    IF EXISTS (SELECT 1 FROM pg_policies WHERE tablename = 'miembros_directivos' AND policyname = 'Admin full access to board members') THEN
        DROP POLICY "Admin full access to board members" ON public.miembros_directivos;
    END IF;

EXCEPTION WHEN OTHERS THEN
    -- Continue if policies don't exist
    NULL;
END $$;

-- Create clean, consolidated policies for miembros_directivos
CREATE POLICY "Admin complete access to board members"
ON public.miembros_directivos
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderator organization board member access"
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

CREATE POLICY "Public access to active board members"
ON public.miembros_directivos
FOR SELECT
USING (estado = 'activo');

-- Add comprehensive security audit logging
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

-- Create policy for audit log access (only admins)
DROP POLICY IF EXISTS "Admins can access security audit logs" ON public.security_audit_log;
CREATE POLICY "Admin security audit log access"
ON public.security_audit_log
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Create function to log security events
CREATE OR REPLACE FUNCTION public.log_security_event(
  p_action text,
  p_resource_type text,
  p_resource_id uuid DEFAULT NULL,
  p_success boolean DEFAULT true,
  p_error_message text DEFAULT NULL,
  p_additional_data jsonb DEFAULT NULL
)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = 'public'
AS $function$
BEGIN
  INSERT INTO security_audit_log (
    user_id,
    action,
    resource_type,
    resource_id,
    success,
    error_message,
    additional_data
  ) VALUES (
    auth.uid(),
    p_action,
    p_resource_type,
    p_resource_id,
    p_success,
    p_error_message,
    p_additional_data
  );
END;
$function$;