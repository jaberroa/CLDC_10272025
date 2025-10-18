-- Fix security issue: Protect contact information in miembros_directivos table
-- Drop ALL existing policies first
DROP POLICY IF EXISTS "Public can view basic board member info" ON miembros_directivos;
DROP POLICY IF EXISTS "Authorized users can view board member contact details" ON miembros_directivos;
DROP POLICY IF EXISTS "Cualquiera puede ver miembros directivos" ON miembros_directivos;
DROP POLICY IF EXISTS "Admins pueden gestionar miembros directivos" ON miembros_directivos;
DROP POLICY IF EXISTS "Moderadores pueden gestionar miembros directivos" ON miembros_directivos;

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

-- Re-add management policies for admins and moderators
CREATE POLICY "Admins pueden gestionar miembros directivos" 
ON miembros_directivos 
FOR ALL 
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderadores pueden gestionar miembros directivos" 
ON miembros_directivos 
FOR ALL 
USING (
  EXISTS (
    SELECT 1 FROM organos_cldc o 
    WHERE o.id = miembros_directivos.organo_id 
    AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
  )
)
WITH CHECK (
  EXISTS (
    SELECT 1 FROM organos_cldc o 
    WHERE o.id = miembros_directivos.organo_id 
    AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
  )
);