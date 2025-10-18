-- Fix the security vulnerability in miembros_public_only view
-- Since we can't enable RLS on views, we'll recreate it as a security definer function

-- Drop the existing view
DROP VIEW IF EXISTS miembros_public_only;

-- Create a secure function to replace the view
CREATE OR REPLACE FUNCTION get_miembros_public_only(org_id uuid DEFAULT NULL)
RETURNS TABLE(
  id uuid,
  nombre_completo text,
  profesion text,
  estado_membresia estado_membresia,
  fecha_ingreso date,
  organizacion_id uuid,
  numero_carnet text,
  foto_url text,
  cedula text,
  email text,
  telefono text,
  direccion text,
  fecha_nacimiento date,
  user_id uuid,
  observaciones text,
  created_at timestamp with time zone,
  updated_at timestamp with time zone,
  fecha_vencimiento date
)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = 'public'
AS $$
  SELECT 
    m.id,
    m.nombre_completo,
    -- Hide sensitive fields based on user role
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.profesion 
      ELSE '[Información restringida]' 
    END as profesion,
    m.estado_membresia,
    m.fecha_ingreso,
    m.organizacion_id,
    m.numero_carnet,
    m.foto_url,
    -- Completely hide sensitive personal data from regular users
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.cedula 
      ELSE NULL 
    END as cedula,
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.email 
      ELSE NULL 
    END as email,
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.telefono 
      ELSE NULL 
    END as telefono,
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.direccion 
      ELSE NULL 
    END as direccion,
    CASE 
      WHEN has_role(auth.uid(), 'admin'::app_role) OR 
           has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) 
      THEN m.fecha_nacimiento 
      ELSE NULL 
    END as fecha_nacimiento,
    m.user_id,
    m.observaciones,
    m.created_at,
    m.updated_at,
    m.fecha_vencimiento
  FROM miembros m
  WHERE 
    -- Only show members from organizations the user has access to
    (
      EXISTS (
        SELECT 1 
        FROM user_roles ur 
        WHERE ur.user_id = auth.uid() 
        AND (
          ur.organizacion_id = m.organizacion_id OR 
          ur.role = 'admin'::app_role
        )
      )
    )
    AND (org_id IS NULL OR m.organizacion_id = org_id);
$$;