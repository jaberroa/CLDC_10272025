-- Drop the existing insecure view
DROP VIEW IF EXISTS public.miembros_public_only;

-- Create a secure version of the view that properly restricts access
CREATE VIEW public.miembros_public_only AS
SELECT 
    id,
    nombre_completo,
    estado_membresia,
    organizacion_id,
    numero_carnet,
    -- Sensitive fields only visible to authorized users
    CASE
        WHEN user_id = auth.uid() THEN email
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN email
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN email
        ELSE NULL::text
    END AS email,
    CASE
        WHEN user_id = auth.uid() THEN telefono
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN telefono
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN telefono
        ELSE NULL::text
    END AS telefono,
    CASE
        WHEN user_id = auth.uid() THEN cedula
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN cedula
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN cedula
        ELSE NULL::text
    END AS cedula,
    CASE
        WHEN user_id = auth.uid() THEN direccion
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN direccion
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN direccion
        ELSE NULL::text
    END AS direccion,
    CASE
        WHEN user_id = auth.uid() THEN fecha_nacimiento
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN fecha_nacimiento
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN fecha_nacimiento
        ELSE NULL::date
    END AS fecha_nacimiento,
    CASE
        WHEN user_id = auth.uid() THEN profesion
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN profesion
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN profesion
        ELSE '[Información restringida]'::text
    END AS profesion,
    CASE
        WHEN user_id = auth.uid() THEN foto_url
        WHEN has_role(auth.uid(), 'admin'::app_role) THEN foto_url
        WHEN (has_role(auth.uid(), 'moderador'::app_role, organizacion_id) AND 
              EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND ur.organizacion_id = miembros.organizacion_id AND ur.role = 'moderador'::app_role)) THEN foto_url
        ELSE NULL::text
    END AS foto_url,
    user_id,
    fecha_ingreso,
    fecha_vencimiento,
    created_at,
    updated_at,
    observaciones
FROM miembros
WHERE 
    -- Only show records if user has proper authorization
    user_id = auth.uid()  -- Users can see their own record
    OR has_role(auth.uid(), 'admin'::app_role)  -- Admins can see all records
    OR (
        -- Moderators can only see records from their organization
        has_role(auth.uid(), 'moderador'::app_role, organizacion_id) 
        AND EXISTS (
            SELECT 1 FROM user_roles ur 
            WHERE ur.user_id = auth.uid() 
            AND ur.organizacion_id = miembros.organizacion_id 
            AND ur.role = 'moderador'::app_role
        )
    );