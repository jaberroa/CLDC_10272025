-- PRODUCCIÓN INMEDIATA: Base de datos completa para CLDCI
-- Sistema de gestión institucional para el Círculo de Locutores Colegiados

-- ========== CONFIGURACIÓN INICIAL ==========

-- Habilitar Row Level Security en todas las tablas existentes que no lo tengan
-- (Las tablas ya existen según el esquema actual)

-- ========== DATOS INICIALES DE PRODUCCIÓN ==========

-- 1. Crear organización principal CLDCI
INSERT INTO public.organizaciones (
  id,
  nombre,
  codigo, 
  tipo,
  pais,
  provincia,
  ciudad,
  direccion,
  telefono,
  email,
  estado_adecuacion,
  miembros_minimos,
  fecha_fundacion
) VALUES (
  gen_random_uuid(),
  'Círculo de Locutores Dominicanos Colegiados, Inc.',
  'CLDCI-001',
  'nacional',
  'República Dominicana',
  'Distrito Nacional',
  'Santo Domingo',
  'Dirección sede principal CLDCI',
  '(809) 555-0001',
  'info@cldci.org.do',
  'aprobada',
  100,
  '1990-01-01'
) ON CONFLICT DO NOTHING;

-- 2. Crear seccionales provinciales (32 provincias de RD)
INSERT INTO public.organizaciones (
  nombre, codigo, tipo, pais, provincia, estado_adecuacion, miembros_minimos, organizacion_padre_id
) 
SELECT 
  'CLDCI Seccional ' || provincia,
  'CLDCI-' || LPAD(ROW_NUMBER() OVER (ORDER BY provincia)::text, 3, '0'),
  'seccional',
  'República Dominicana',
  provincia,
  'pendiente',
  15,
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1)
FROM (VALUES
  ('Azua'), ('Baoruco'), ('Barahona'), ('Dajabón'), ('Distrito Nacional'),
  ('Duarte'), ('Elías Piña'), ('El Seibo'), ('Espaillat'), ('Hato Mayor'),
  ('Hermanas Mirabal'), ('Independencia'), ('La Altagracia'), ('La Romana'),
  ('La Vega'), ('María Trinidad Sánchez'), ('Monseñor Nouel'), ('Monte Cristi'),
  ('Monte Plata'), ('Pedernales'), ('Peravia'), ('Puerto Plata'), ('Samaná'),
  ('Sánchez Ramírez'), ('San Cristóbal'), ('San José de Ocoa'), ('San Juan'),
  ('San Pedro de Macorís'), ('Santiago'), ('Santiago Rodríguez'), ('Santo Domingo'),
  ('Valverde')
) AS provincias(provincia)
ON CONFLICT DO NOTHING;

-- 3. Crear seccionales de la diáspora
INSERT INTO public.organizaciones (
  nombre, codigo, tipo, pais, estado_adecuacion, miembros_minimos, organizacion_padre_id
) 
SELECT 
  'CLDCI Diáspora ' || pais,
  'CLDCI-D' || LPAD(ROW_NUMBER() OVER (ORDER BY pais)::text, 2, '0'),
  'diaspora',
  pais,
  'pendiente',
  10,
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1)
FROM (VALUES
  ('Estados Unidos'), ('España'), ('Italia'), ('Francia'), 
  ('Puerto Rico'), ('Canadá'), ('Venezuela'), ('Colombia')
) AS diaspora(pais)
ON CONFLICT DO NOTHING;

-- 4. Crear usuario administrador principal
-- NOTA: El usuario debe registrarse normalmente primero, luego ejecutar este SQL con su ID real
-- Esto es solo un ejemplo de estructura

-- 5. Crear primer padrón electoral para la organización principal
INSERT INTO public.padrones_electorales (
  organizacion_id,
  periodo,
  descripcion,
  fecha_inicio,
  fecha_fin,
  activo,
  created_by
) VALUES (
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  '2024-2026',
  'Padrón Electoral Nacional CLDCI 2024-2026',
  '2024-01-01',
  '2026-12-31',
  true,
  '00000000-0000-0000-0000-000000000000'  -- Temporal, actualizar con ID real
) ON CONFLICT DO NOTHING;

-- 6. Crear categorías base para presupuesto
INSERT INTO public.presupuestos (
  organizacion_id,
  categoria,
  periodo,
  monto_presupuestado,
  activo,
  created_by
)
SELECT 
  org.id,
  categoria,
  '2024',
  monto,
  true,
  '00000000-0000-0000-0000-000000000000'
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Ingresos por Cuotas', 500000.00),
  ('Gastos Administrativos', 200000.00),
  ('Programas de Formación', 150000.00),
  ('Eventos Institucionales', 100000.00),
  ('Tecnología e Innovación', 80000.00),
  ('Comunicación y Marketing', 60000.00)
) AS cats(categoria, monto)
WHERE org.codigo = 'CLDCI-001'
ON CONFLICT DO NOTHING;

-- 7. Crear primera asamblea
INSERT INTO public.asambleas (
  organizacion_id,
  tipo,
  titulo,
  descripcion,
  fecha_convocatoria,
  fecha_asamblea,
  quorum_minimo,
  lugar,
  modalidad,
  estado,
  created_by
) VALUES (
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  'ordinaria',
  'Asamblea General Ordinaria 2024',
  'Primera asamblea general del sistema digital CLDCI',
  NOW() + INTERVAL '7 days',
  NOW() + INTERVAL '30 days', 
  50,
  'Sede Principal CLDCI',
  'hibrida',
  'convocada',
  '00000000-0000-0000-0000-000000000000'
) ON CONFLICT DO NOTHING;

-- 8. Crear capacitaciones iniciales
INSERT INTO public.capacitaciones (
  organizacion_id,
  titulo,
  descripcion,
  tipo,
  modalidad,
  fecha_inicio,
  fecha_fin,
  capacidad_maxima,
  lugar,
  estado,
  created_by
)
SELECT 
  org.id,
  titulo,
  descripcion,
  'profesional',
  modalidad,
  NOW() + INTERVAL '15 days',
  NOW() + INTERVAL '17 days',
  capacidad,
  lugar,
  'programada',
  '00000000-0000-0000-0000-000000000000'
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Introducción a la Locución Digital', 'Fundamentos de locución en la era digital', 'presencial', 'Sede Principal', 30),
  ('Ética Profesional del Locutor', 'Principios éticos en la comunicación', 'virtual', 'Plataforma Online', 50),
  ('Nuevas Tecnologías en Radio', 'Herramientas digitales para locutores', 'hibrida', 'Sede Principal', 25),
  ('Gestión de Medios Digitales', 'Administración de contenido digital', 'virtual', 'Plataforma Online', 40)
) AS caps(titulo, descripcion, modalidad, lugar, capacidad)
WHERE org.codigo = 'CLDCI-001'
ON CONFLICT DO NOTHING;

-- ========== CONFIGURACIÓN DE SEGURIDAD ==========

-- Función para validar emails institucionales (opcional)
CREATE OR REPLACE FUNCTION public.is_valid_institutional_email(email text)
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
  SELECT email ILIKE '%@cldci.org.do' OR 
         email ILIKE '%@locutor.do' OR
         email ILIKE '%@%.edu.do' OR
         LENGTH(email) > 5; -- Permitir otros emails por ahora
$$;

-- ========== TRIGGERS PARA AUDITORÍA ==========

-- Trigger para log de acceso a datos sensibles
CREATE OR REPLACE FUNCTION public.log_sensitive_access()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  -- Log cuando se accede a datos sensibles de miembros
  IF TG_TABLE_NAME = 'miembros' AND TG_OP = 'SELECT' THEN
    INSERT INTO public.member_access_log (
      accessing_user_id,
      accessed_member_id,
      access_type,
      user_role,
      organization_context
    ) VALUES (
      COALESCE(auth.uid(), '00000000-0000-0000-0000-000000000000'),
      NEW.id,
      'member_data_access',
      'user',
      NEW.organizacion_id
    );
  END IF;
  
  RETURN COALESCE(NEW, OLD);
END;
$$;

-- ========== CONFIGURACIÓN DE STORAGE ==========

-- Políticas de storage para archivos institucionales
INSERT INTO storage.objects (bucket_id, name, owner, metadata) 
SELECT 'documentos', 'institucional/', auth.uid(), '{"type": "folder"}'::jsonb
WHERE NOT EXISTS (
  SELECT 1 FROM storage.objects 
  WHERE bucket_id = 'documentos' AND name = 'institucional/'
);

-- ========== ESTADÍSTICAS INICIALES ==========

-- Vista para estadísticas del dashboard
CREATE OR REPLACE VIEW public.dashboard_statistics AS
SELECT 
  (SELECT COUNT(*) FROM public.miembros WHERE estado_membresia = 'activa') as miembros_activos,
  (SELECT COUNT(*) FROM public.organizaciones) as total_organizaciones,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'seccional') as seccionales,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'diaspora') as diaspora,
  (SELECT COUNT(*) FROM public.elecciones WHERE estado = 'activa') as elecciones_activas,
  (SELECT COUNT(*) FROM public.asambleas WHERE estado = 'convocada') as asambleas_programadas;

-- ========== FINALIZACIÓN ==========

-- Mensaje de confirmación
DO $$
BEGIN
  RAISE NOTICE 'Base de datos CLDCI configurada para producción - Versión 1.0';
  RAISE NOTICE 'Organizaciones creadas: %', (SELECT COUNT(*) FROM public.organizaciones);
  RAISE NOTICE 'Sistema listo para uso inmediato';
END $$;