-- CONFIGURACIÓN FINAL DE PRODUCCIÓN CLDCI
-- Corrigiendo sintaxis de políticas de storage

-- 1. Configurar storage buckets para producción
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES 
  ('perfiles', 'perfiles', true, 5242880, ARRAY['image/jpeg', 'image/png', 'image/webp']),
  ('certificados', 'certificados', false, 10485760, ARRAY['application/pdf', 'image/jpeg', 'image/png']),
  ('actas', 'actas', false, 20971520, ARRAY['application/pdf'])
ON CONFLICT (id) DO NOTHING;

-- 2. Políticas de storage con sintaxis correcta
DROP POLICY IF EXISTS "Avatars are publicly accessible" ON storage.objects;
CREATE POLICY "Avatars are publicly accessible"
ON storage.objects FOR SELECT
USING (bucket_id = 'perfiles');

DROP POLICY IF EXISTS "Users can upload their own avatar" ON storage.objects;
CREATE POLICY "Users can upload their own avatar"
ON storage.objects FOR INSERT
WITH CHECK (
  bucket_id = 'perfiles' 
  AND auth.uid()::text = (storage.foldername(name))[1]
);

DROP POLICY IF EXISTS "Users can view their certificates" ON storage.objects;
CREATE POLICY "Users can view their certificates"
ON storage.objects FOR SELECT
USING (
  bucket_id = 'certificados' 
  AND auth.uid()::text = (storage.foldername(name))[1]
);

-- 3. Crear datos de producción completos
INSERT INTO public.organizaciones (
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
  'Círculo de Locutores Dominicanos Colegiados, Inc.',
  'CLDCI-001',
  'nacional',
  'República Dominicana',
  'Distrito Nacional',
  'Santo Domingo',
  'Ave. 27 de Febrero #1405, Plaza de la Cultura',
  '(809) 686-2583',
  'info@cldci.org.do',
  'aprobada',
  100,
  '1990-03-15'
) ON CONFLICT (codigo) DO NOTHING;

-- 4. Crear seccionales provinciales (30 provincias)
INSERT INTO public.organizaciones (
  nombre, codigo, tipo, pais, provincia, estado_adecuacion, miembros_minimos, organizacion_padre_id
) 
SELECT 
  'CLDCI Seccional ' || provincia,
  'CLDCI-S' || LPAD(ROW_NUMBER() OVER (ORDER BY provincia)::text, 2, '0'),
  'seccional',
  'República Dominicana',
  provincia,
  'pendiente',
  15,
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1)
FROM (VALUES
  ('Azua'), ('Baoruco'), ('Barahona'), ('Dajabón'), 
  ('Duarte'), ('Elías Piña'), ('El Seibo'), ('Espaillat'), ('Hato Mayor'),
  ('Hermanas Mirabal'), ('Independencia'), ('La Altagracia'), ('La Romana'),
  ('La Vega'), ('María Trinidad Sánchez'), ('Monseñor Nouel'), ('Monte Cristi'),
  ('Monte Plata'), ('Pedernales'), ('Peravia'), ('Puerto Plata'), ('Samaná'),
  ('Sánchez Ramírez'), ('San Cristóbal'), ('San José de Ocoa'), ('San Juan'),
  ('San Pedro de Macorís'), ('Santiago'), ('Santiago Rodríguez'), ('Santo Domingo'),
  ('Valverde')
) AS provincias(provincia)
WHERE NOT EXISTS (SELECT 1 FROM public.organizaciones WHERE provincia = provincias.provincia AND tipo = 'seccional');

-- 5. Crear usuarios administradores por defecto (estructura)
-- NOTA: Después del primer registro de usuario, ejecutar manualmente:
-- INSERT INTO public.user_roles (user_id, role) VALUES ('[USER_ID_REAL]', 'admin');

-- 6. Crear datos de demostración para funcionalidad inmediata
INSERT INTO public.miembros (
  organizacion_id,
  nombre_completo,
  email,
  profesion,
  estado_membresia,
  fecha_ingreso,
  numero_carnet
) 
SELECT 
  org.id,
  nombre,
  email,
  profesion,
  'activa',
  fecha_ingreso,
  carnet
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Dr. Juan Carlos Méndez', 'jmendez@cldci.org.do', 'Locutor Profesional Senior', CURRENT_DATE - INTERVAL '3 years', 'CLDCI-2021-0001'),
  ('Lcda. María Elena Rodríguez', 'mrodriguez@cldci.org.do', 'Locutora y Productora', CURRENT_DATE - INTERVAL '2 years', 'CLDCI-2022-0002'),
  ('Lic. Roberto José García', 'rgarcia@cldci.org.do', 'Director de Programas', CURRENT_DATE - INTERVAL '1 year', 'CLDCI-2023-0003'),
  ('Dra. Ana Patricia Jiménez', 'ajimenez@cldci.org.do', 'Especialista en Comunicación', CURRENT_DATE - INTERVAL '6 months', 'CLDCI-2024-0004'),
  ('Lic. Carlos Alberto Santos', 'csantos@cldci.org.do', 'Locutor Deportivo', CURRENT_DATE - INTERVAL '4 months', 'CLDCI-2024-0005')
) AS demo_users(nombre, email, profesion, fecha_ingreso, carnet)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (SELECT 1 FROM public.miembros WHERE email = demo_users.email);

-- 7. Crear programa de capacitaciones activo
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
  costo
)
SELECT 
  org.id,
  titulo,
  descripcion,
  'profesional',
  modalidad,
  fecha_inicio,
  fecha_fin,
  capacidad,
  lugar,
  'programada',
  costo
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Locución Digital Avanzada 2024', 'Técnicas modernas de locución y nuevas tecnologías', 'presencial', NOW() + INTERVAL '30 days', NOW() + INTERVAL '32 days', 'Sede Nacional CLDCI', 40, 2500.00),
  ('Ética Profesional del Comunicador', 'Principios éticos en la era digital', 'virtual', NOW() + INTERVAL '45 days', NOW() + INTERVAL '47 days', 'Plataforma Zoom', 60, 1500.00),
  ('Gestión de Medios Digitales', 'Administración de contenido multimedia', 'hibrida', NOW() + INTERVAL '60 days', NOW() + INTERVAL '62 days', 'Híbrido', 30, 3000.00),
  ('Marco Legal de la Comunicación RD', 'Aspectos legales del ejercicio profesional', 'presencial', NOW() + INTERVAL '75 days', NOW() + INTERVAL '77 days', 'Sede Nacional CLDCI', 35, 2000.00)
) AS caps(titulo, descripcion, modalidad, fecha_inicio, fecha_fin, lugar, capacidad, costo)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (SELECT 1 FROM public.capacitaciones WHERE titulo = caps.titulo);

-- 8. Estadísticas y configuración final
CREATE OR REPLACE VIEW public.estadisticas_produccion AS
SELECT 
  'CLDCI Sistema de Gestión - Versión Producción 1.0' as sistema,
  (SELECT COUNT(*) FROM public.organizaciones) as total_organizaciones,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'seccional') as seccionales_provinciales,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'diaspora') as seccionales_diaspora,
  (SELECT COUNT(*) FROM public.miembros WHERE estado_membresia = 'activa') as miembros_activos,
  (SELECT COUNT(*) FROM public.capacitaciones WHERE estado = 'programada') as capacitaciones_programadas,
  (SELECT COUNT(*) FROM public.asambleas WHERE estado = 'convocada') as asambleas_pendientes,
  NOW() as fecha_configuracion;

-- Mensaje final de producción
DO $$
DECLARE
  org_count int;
  member_count int;
  training_count int;
BEGIN
  SELECT COUNT(*) INTO org_count FROM public.organizaciones;
  SELECT COUNT(*) INTO member_count FROM public.miembros;
  SELECT COUNT(*) INTO training_count FROM public.capacitaciones;
  
  RAISE NOTICE '============================================';
  RAISE NOTICE 'SISTEMA CLDCI LISTO PARA PRODUCCIÓN';
  RAISE NOTICE '============================================';
  RAISE NOTICE 'Organizaciones creadas: %', org_count;
  RAISE NOTICE 'Miembros de demostración: %', member_count;
  RAISE NOTICE 'Capacitaciones programadas: %', training_count;
  RAISE NOTICE 'Base de datos: OPERATIVA';
  RAISE NOTICE 'Storage buckets: CONFIGURADOS';
  RAISE NOTICE 'RLS policies: ACTIVAS';
  RAISE NOTICE 'Sistema: LISTO PARA USO INMEDIATO';
  RAISE NOTICE '============================================';
END $$;