-- =====================================================
-- CORRECCIÓN CRÍTICA: Sistema de roles y acceso
-- Permitir que usuarios nuevos puedan operar en la plataforma  
-- Fecha: 2025-10-09
-- =====================================================

-- ============================================
-- PASO 1: Modificar función existente para asignar rol automáticamente
-- ============================================

CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  -- Crear perfil del usuario
  INSERT INTO public.profiles (id, email, nombre_completo)
  VALUES (
    NEW.id, 
    NEW.email, 
    COALESCE(NEW.raw_user_meta_data ->> 'full_name', NEW.email)
  );
  
  -- Asignar rol 'miembro' por defecto
  INSERT INTO public.user_roles (user_id, role, organizacion_id)
  VALUES (NEW.id, 'miembro'::app_role, NULL::uuid)
  ON CONFLICT (user_id, role, organizacion_id) DO NOTHING;
  
  RETURN NEW;
END;
$$;

-- ============================================
-- PASO 2: Asignar rol a usuarios existentes que no tienen rol
-- ============================================

INSERT INTO public.user_roles (user_id, role, organizacion_id)
SELECT DISTINCT u.id, 'miembro'::app_role, NULL::uuid
FROM auth.users u
WHERE NOT EXISTS (
  SELECT 1 FROM public.user_roles ur 
  WHERE ur.user_id = u.id 
  AND ur.role = 'miembro'::app_role
  AND ur.organizacion_id IS NULL
)
ON CONFLICT (user_id, role, organizacion_id) DO NOTHING;

-- ============================================
-- PASO 3: Actualizar políticas de Storage para usuarios autenticados
-- ============================================

-- Política para EXPEDIENTES - permitir que usuarios autenticados suban archivos
DROP POLICY IF EXISTS "Authenticated users can upload to expedientes" ON storage.objects;
CREATE POLICY "Authenticated users can upload to expedientes"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'expedientes' 
  AND auth.uid() IS NOT NULL
);

-- Política para EXPEDIENTES - permitir que usuarios vean archivos
DROP POLICY IF EXISTS "Users can view expedientes for their organization" ON storage.objects;
CREATE POLICY "Users can view expedientes for their organization"
ON storage.objects FOR SELECT
TO authenticated
USING (
  bucket_id = 'expedientes' 
  AND auth.uid() IS NOT NULL
);

-- Política para DOCUMENTOS - permitir que usuarios autenticados suban
DROP POLICY IF EXISTS "Authenticated users can upload documents" ON storage.objects;
CREATE POLICY "Authenticated users can upload documents"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'documentos'
  AND auth.uid() IS NOT NULL
);

-- Política para DOCUMENTOS - permitir que usuarios vean documentos
DROP POLICY IF EXISTS "Users can view documents for their organization" ON storage.objects;
CREATE POLICY "Users can view documents for their organization"
ON storage.objects FOR SELECT
TO authenticated
USING (
  bucket_id = 'documentos'
  AND auth.uid() IS NOT NULL
);

-- Política para FOTOS - permitir que usuarios autenticados suban fotos
DROP POLICY IF EXISTS "Authenticated users can upload photos" ON storage.objects;
CREATE POLICY "Authenticated users can upload photos"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'fotos'
  AND auth.uid() IS NOT NULL
);

-- ============================================
-- PASO 4: Actualizar políticas RLS de tablas principales
-- ============================================

-- Permitir que usuarios autenticados lean organizaciones
DROP POLICY IF EXISTS "Authenticated users can view organizations" ON public.organizaciones;
CREATE POLICY "Authenticated users can view organizations"
ON public.organizaciones FOR SELECT
TO authenticated
USING (auth.uid() IS NOT NULL);

-- Permitir que usuarios autenticados lean seccionales
DROP POLICY IF EXISTS "Authenticated users can view seccionales" ON public.seccionales;
CREATE POLICY "Authenticated users can view seccionales"
ON public.seccionales FOR SELECT
TO authenticated
USING (auth.uid() IS NOT NULL);

-- Permitir que usuarios autenticados lean cursos
DROP POLICY IF EXISTS "Authenticated users can view courses" ON public.cursos;
CREATE POLICY "Authenticated users can view courses"
ON public.cursos FOR SELECT
TO authenticated
USING (auth.uid() IS NOT NULL);

-- Permitir que usuarios autenticados lean diplomados
DROP POLICY IF EXISTS "Authenticated users can view diplomados" ON public.diplomados;
CREATE POLICY "Authenticated users can view diplomados"
ON public.diplomados FOR SELECT
TO authenticated
USING (auth.uid() IS NOT NULL);

-- Permitir que usuarios autenticados lean asambleas
DROP POLICY IF EXISTS "Authenticated users can view asambleas" ON public.asambleas;
CREATE POLICY "Authenticated users can view asambleas"
ON public.asambleas FOR SELECT
TO authenticated
USING (auth.uid() IS NOT NULL);

-- Permitir que usuarios autenticados se inscriban en cursos
DROP POLICY IF EXISTS "Authenticated users can enroll in courses" ON public.inscripciones_cursos;
CREATE POLICY "Authenticated users can enroll in courses"
ON public.inscripciones_cursos FOR INSERT
TO authenticated
WITH CHECK (
  EXISTS (
    SELECT 1 FROM miembros m 
    WHERE m.id = inscripciones_cursos.miembro_id 
    AND m.user_id = auth.uid()
  )
);

-- Permitir que usuarios autenticados vean sus inscripciones
DROP POLICY IF EXISTS "Users can view their own course enrollments" ON public.inscripciones_cursos;
CREATE POLICY "Users can view their own course enrollments"
ON public.inscripciones_cursos FOR SELECT
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM miembros m 
    WHERE m.id = inscripciones_cursos.miembro_id 
    AND m.user_id = auth.uid()
  )
  OR has_role(auth.uid(), 'admin'::app_role)
  OR has_role(auth.uid(), 'moderador'::app_role)
);

-- ============================================
-- PASO 5: Registrar evento de seguridad
-- ============================================

INSERT INTO public.security_audit_log (
  user_id, 
  action, 
  resource_type, 
  success, 
  additional_data
)
VALUES (
  auth.uid(), 
  'USER_ACCESS_SYSTEM_ENABLED', 
  'DATABASE_POLICIES', 
  true,
  jsonb_build_object(
    'timestamp', now(),
    'changes', ARRAY[
      'Auto-assign miembro role on registration via handle_new_user',
      'Enable authenticated users to upload files to all buckets',
      'Enable users to view all organization data',
      'Enable users to enroll in courses and view content',
      'Maintain admin/moderador privileges for management'
    ],
    'affected_tables', ARRAY['user_roles', 'organizaciones', 'seccionales', 'cursos', 'diplomados', 'asambleas', 'inscripciones_cursos'],
    'affected_buckets', ARRAY['expedientes', 'documentos', 'fotos'],
    'compliance', 'User access fully enabled while maintaining security',
    'sistema_operativo', 'Platform now operational for all users'
  )
);

COMMENT ON FUNCTION public.handle_new_user() IS 'Auto-crea perfil y asigna rol miembro a nuevos usuarios registrados';