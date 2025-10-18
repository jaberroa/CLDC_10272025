-- Fix security issue: Protect contact information in miembros_directivos table
-- Create secure function to get public board member information without contact details
CREATE OR REPLACE FUNCTION public.get_public_miembros_directivos(org_id uuid DEFAULT NULL)
RETURNS TABLE(
  id uuid,
  miembro_id uuid,
  organo_id uuid,
  cargo_id uuid,
  periodo text,
  fecha_inicio date,
  fecha_fin date,
  estado text,
  es_presidente boolean,
  semblanza text,
  foto_url text,
  created_at timestamp with time zone,
  updated_at timestamp with time zone
)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = 'public'
AS $$
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
$$;

-- Create function to get board member contact details for authorized users only
CREATE OR REPLACE FUNCTION public.get_miembro_directivo_contact_details(miembro_directivo_id uuid)
RETURNS TABLE(
  id uuid,
  email_institucional text,
  telefono_institucional text
)
LANGUAGE plpgsql
STABLE
SECURITY DEFINER
SET search_path = 'public'
AS $$
DECLARE
  org_context uuid;
BEGIN
  -- Get organization context for the board member
  SELECT o.organizacion_id INTO org_context
  FROM miembros_directivos md
  JOIN organos_cldc o ON o.id = md.organo_id
  WHERE md.id = miembro_directivo_id;

  -- Check if user is authorized to view contact details
  IF NOT (
    has_role(auth.uid(), 'admin'::app_role) 
    OR 
    has_role(auth.uid(), 'moderador'::app_role, org_context)
    OR
    -- Board members can see contact details of other board members in their organization
    EXISTS (
      SELECT 1 FROM miembros_directivos md2
      JOIN organos_cldc o2 ON o2.id = md2.organo_id
      JOIN miembros m ON m.id = md2.miembro_id
      WHERE m.user_id = auth.uid()
      AND o2.organizacion_id = org_context
      AND md2.estado = 'activo'
    )
  ) THEN
    RAISE EXCEPTION 'Access denied: insufficient privileges to view contact details';
  END IF;

  -- Return contact details for authorized users
  RETURN QUERY
  SELECT 
    md.id,
    md.email_institucional,
    md.telefono_institucional
  FROM miembros_directivos md
  WHERE md.id = miembro_directivo_id;
END;
$$;

-- Update RLS policies for miembros_directivos to be more restrictive for contact info
-- Drop existing overly permissive policies if they exist
DROP POLICY IF EXISTS "Cualquiera puede ver miembros directivos" ON miembros_directivos;
DROP POLICY IF EXISTS "Public can view board members" ON miembros_directivos;

-- Create new secure policies
CREATE POLICY "Public can view basic board member info" 
ON miembros_directivos 
FOR SELECT 
USING (estado = 'activo');

-- Policy for contact details - only for authorized users
CREATE POLICY "Authorized users can view board member contact details" 
ON miembros_directivos 
FOR SELECT 
USING (
  -- Admins can see all contact details
  has_role(auth.uid(), 'admin'::app_role) 
  OR 
  -- Moderators can see contact details for their organization's board members
  EXISTS (
    SELECT 1 FROM organos_cldc o 
    WHERE o.id = miembros_directivos.organo_id 
    AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
  )
  OR
  -- Active board members can see contact details of other board members in their org
  EXISTS (
    SELECT 1 FROM miembros_directivos md2
    JOIN organos_cldc o2 ON o2.id = md2.organo_id
    JOIN miembros m ON m.id = md2.miembro_id
    JOIN organos_cldc o3 ON o3.id = miembros_directivos.organo_id
    WHERE m.user_id = auth.uid()
    AND o2.organizacion_id = o3.organizacion_id
    AND md2.estado = 'activo'
  )
);