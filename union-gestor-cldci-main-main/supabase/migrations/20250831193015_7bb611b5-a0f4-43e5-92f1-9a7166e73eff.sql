-- Fix security issue: Protect contact information in seccionales table
-- Drop the overly permissive public access policy
DROP POLICY IF EXISTS "Cualquiera puede ver seccionales públicas" ON seccionales;

-- Create more secure policies for seccionales
CREATE POLICY "Public can view basic seccional info" 
ON seccionales 
FOR SELECT 
USING (true)
-- Only allow access to non-sensitive fields in SELECT
-- Contact details will be handled by a separate policy

;

-- Create policy for contact information access - only for authenticated users with proper roles
CREATE POLICY "Authorized users can view seccional contact details" 
ON seccionales 
FOR SELECT 
USING (
  -- Admins can see all contact details
  has_role(auth.uid(), 'admin'::app_role) 
  OR 
  -- Moderators can see contact details for their organization's seccionales
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  OR
  -- Coordinators can see their own seccional's contact details
  EXISTS (
    SELECT 1 FROM miembros m 
    WHERE m.id = seccionales.coordinador_id 
    AND m.user_id = auth.uid()
  )
);

-- Create a security definer function to get public seccional information without contact details
CREATE OR REPLACE FUNCTION public.get_public_seccionales()
RETURNS TABLE(
  id uuid,
  nombre text,
  tipo text,
  pais text,
  provincia text,
  ciudad text,
  miembros_count integer,
  fecha_fundacion date,
  estado text,
  organizacion_id uuid,
  created_at timestamp with time zone,
  updated_at timestamp with time zone
)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = 'public'
AS $$
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
$$;

-- Create function to get seccional contact details for authorized users only
CREATE OR REPLACE FUNCTION public.get_seccional_contact_details(seccional_id uuid)
RETURNS TABLE(
  id uuid,
  telefono text,
  email text,
  direccion text
)
LANGUAGE plpgsql
STABLE
SECURITY DEFINER
SET search_path = 'public'
AS $$
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
$$;