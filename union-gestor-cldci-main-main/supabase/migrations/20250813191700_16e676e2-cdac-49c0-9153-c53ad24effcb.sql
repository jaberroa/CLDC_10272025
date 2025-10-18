-- Secure the miembros_public_only view with RLS policies
-- This fixes the critical security vulnerability

-- Enable RLS on the miembros_public_only view
ALTER TABLE miembros_public_only ENABLE ROW LEVEL SECURITY;

-- Policy: Users can only view members from their own organization
CREATE POLICY "Users can view members from their organization" 
ON miembros_public_only 
FOR SELECT 
USING (
  EXISTS (
    SELECT 1 
    FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = miembros_public_only.organizacion_id
  )
);

-- Policy: Admins can view all member data
CREATE POLICY "Admins can view all members" 
ON miembros_public_only 
FOR SELECT 
USING (has_role(auth.uid(), 'admin'::app_role));

-- Policy: Moderators can view members from their organization
CREATE POLICY "Moderators can view organization members" 
ON miembros_public_only 
FOR SELECT 
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

-- Update the view to hide sensitive data for regular users
CREATE OR REPLACE VIEW miembros_public_only AS
SELECT 
  id,
  nombre_completo,
  -- Hide sensitive fields based on user role
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN profesion 
    ELSE '[Información restringida]' 
  END as profesion,
  estado_membresia,
  fecha_ingreso,
  organizacion_id,
  numero_carnet,
  foto_url,
  -- Completely hide sensitive personal data from regular users
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN cedula 
    ELSE NULL 
  END as cedula,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN email 
    ELSE NULL 
  END as email,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN telefono 
    ELSE NULL 
  END as telefono,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN direccion 
    ELSE NULL 
  END as direccion,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) OR 
         has_role(auth.uid(), 'moderador'::app_role) 
    THEN fecha_nacimiento 
    ELSE NULL 
  END as fecha_nacimiento,
  user_id,
  observaciones,
  created_at,
  updated_at,
  fecha_vencimiento
FROM miembros
WHERE 
  -- Only show members from organizations the user has access to
  EXISTS (
    SELECT 1 
    FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (
      ur.organizacion_id = miembros.organizacion_id OR 
      ur.role = 'admin'::app_role
    )
  );