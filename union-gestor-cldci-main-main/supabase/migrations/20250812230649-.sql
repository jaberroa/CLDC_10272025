-- Eliminar la política pública insegura en la tabla organizaciones
DROP POLICY IF EXISTS "Public can view organizations" ON public.organizaciones;

-- Crear políticas restrictivas y seguras para la tabla organizaciones

-- Solo admins pueden gestionar organizaciones (crear, actualizar, eliminar)
CREATE POLICY "Admins can manage all organizations" 
ON public.organizaciones 
FOR ALL 
TO authenticated 
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderadores pueden ver organizaciones donde tienen rol
CREATE POLICY "Moderators can view their organizations" 
ON public.organizaciones 
FOR SELECT 
TO authenticated 
USING (
  EXISTS (
    SELECT 1 
    FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = organizaciones.id 
    AND ur.role = 'moderador'::app_role
  )
  OR has_role(auth.uid(), 'admin'::app_role)
);

-- Usuarios autenticados pueden ver organizaciones donde son miembros
CREATE POLICY "Users can view organizations where they are members" 
ON public.organizaciones 
FOR SELECT 
TO authenticated 
USING (
  EXISTS (
    SELECT 1 
    FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = organizaciones.id
  )
  OR has_role(auth.uid(), 'admin'::app_role)
);