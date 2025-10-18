-- PRODUCCIÓN CLDCI - CONFIGURACIÓN COMPLETA CON ENUMS CORRECTOS

-- 1. Crear organización principal CLDCI (usando enum correcto)
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
  'seccional_nacional',
  'República Dominicana',
  'Distrito Nacional',
  'Santo Domingo',
  'Ave. 27 de Febrero #1405, Plaza de la Cultura, Santo Domingo',
  '(809) 686-2583',
  'info@cldci.org.do',
  'aprobada',
  100,
  '1990-03-15'
) ON CONFLICT (codigo) DO NOTHING;

-- 2. Crear 32 seccionales provinciales de República Dominicana
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
  ('Valverde'), ('San José de las Matas')
) AS provincias(provincia)
WHERE NOT EXISTS (SELECT 1 FROM public.organizaciones WHERE provincia = provincias.provincia AND tipo = 'seccional');

-- 3. Crear seccionales internacionales (diáspora)
INSERT INTO public.organizaciones (
  nombre, codigo, tipo, pais, estado_adecuacion, miembros_minimos, organizacion_padre_id
) 
SELECT 
  'CLDCI Seccional Internacional ' || pais,
  'CLDCI-INT-' || LPAD(ROW_NUMBER() OVER (ORDER BY pais)::text, 2, '0'),
  'seccional_internacional',
  pais,
  'pendiente',
  10,
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1)
FROM (VALUES
  ('Estados Unidos'), ('España'), ('Italia'), ('Francia'), 
  ('Puerto Rico'), ('Canadá'), ('Venezuela'), ('Colombia')
) AS diaspora(pais)
WHERE NOT EXISTS (SELECT 1 FROM public.organizaciones WHERE nombre LIKE '%Internacional ' || diaspora.pais);

-- 4. Crear padrón electoral nacional activo
INSERT INTO public.padrones_electorales (
  organizacion_id,
  periodo,
  descripcion,
  fecha_inicio,
  fecha_fin,
  activo
) VALUES (
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  '2024-2026',
  'Padrón Electoral Nacional CLDCI 2024-2026 - Registro oficial de miembros con derecho al voto',
  '2024-01-01',
  '2026-12-31',
  true
) ON CONFLICT DO NOTHING;

-- 5. Estructura presupuestaria nacional 2024
INSERT INTO public.presupuestos (
  organizacion_id,
  categoria,
  periodo,
  monto_presupuestado,
  monto_ejecutado,
  activo
)
SELECT 
  org.id,
  categoria,
  '2024',
  monto_presupuestado,
  monto_ejecutado,
  true
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Cuotas de Membresía Nacional', 1200000.00, 400000.00),
  ('Eventos y Capacitaciones Profesionales', 350000.00, 120000.00),
  ('Gastos Administrativos y Operativos', 280000.00, 180000.00),
  ('Tecnología e Innovación Digital', 150000.00, 75000.00),
  ('Comunicación y Relaciones Públicas', 120000.00, 60000.00),
  ('Programas de Formación Continua', 200000.00, 85000.00),
  ('Asesoría Legal y Gremial', 100000.00, 25000.00),
  ('Actividades Sociales y Culturales', 80000.00, 30000.00)
) AS presupuesto(categoria, monto_presupuestado, monto_ejecutado)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (
  SELECT 1 FROM public.presupuestos p 
  WHERE p.organizacion_id = org.id AND p.categoria = presupuesto.categoria AND p.periodo = '2024'
);

-- 6. Asamblea General Ordinaria 2024
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
  estado
) VALUES (
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  'ordinaria',
  'Asamblea General Ordinaria CLDCI 2024',
  'Asamblea para presentación del informe anual, estados financieros, nuevos proyectos tecnológicos y elección de cargos vacantes de la Junta Directiva Nacional',
  NOW() + INTERVAL '10 days',
  NOW() + INTERVAL '60 days', 
  75,
  'Auditorio Nacional CLDCI, Ave. 27 de Febrero, Santo Domingo',
  'hibrida',
  'convocada'
) ON CONFLICT DO NOTHING;

-- 7. Programa Nacional de Capacitación 2024
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
  ('Locución Digital Profesional 2024', 'Dominio de herramientas digitales modernas para la locución profesional', 'presencial', NOW() + INTERVAL '25 days', NOW() + INTERVAL '27 days', 'Sede Nacional CLDCI', 50, 3500.00),
  ('Ética y Deontología del Comunicador', 'Principios éticos fundamentales en el ejercicio de la comunicación', 'virtual', NOW() + INTERVAL '40 days', NOW() + INTERVAL '42 days', 'Plataforma Microsoft Teams', 80, 2000.00),
  ('Gestión Integral de Medios Digitales', 'Administración completa de contenido multimedia y redes sociales', 'hibrida', NOW() + INTERVAL '55 days', NOW() + INTERVAL '57 days', 'Modalidad Híbrida', 40, 4000.00),
  ('Marco Jurídico de la Comunicación RD', 'Aspectos legales del ejercicio profesional en República Dominicana', 'presencial', NOW() + INTERVAL '70 days', NOW() + INTERVAL '72 days', 'Sede Nacional CLDCI', 45, 2500.00),
  ('Producción Radial Moderna', 'Técnicas avanzadas de producción para radio contemporánea', 'presencial', NOW() + INTERVAL '85 days', NOW() + INTERVAL '87 days', 'Estudios CLDCI', 25, 5000.00)
) AS caps(titulo, descripcion, modalidad, fecha_inicio, fecha_fin, lugar, capacidad, costo)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (SELECT 1 FROM public.capacitaciones WHERE titulo = caps.titulo);

-- 8. Miembros fundadores y demo para funcionalidad inmediata
INSERT INTO public.miembros (
  organizacion_id,
  nombre_completo,
  email,
  profesion,
  estado_membresia,
  fecha_ingreso,
  numero_carnet,
  cedula,
  telefono
) 
SELECT 
  org.id,
  nombre,
  email,
  profesion,
  'activa',
  fecha_ingreso,
  carnet,
  cedula,
  telefono
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Dr. Juan Carlos Méndez Pérez', 'juan.mendez@cldci.org.do', 'Locutor Profesional Senior / Director de Programas', CURRENT_DATE - INTERVAL '5 years', 'CLDCI-2019-0001', '001-0123456-7', '(809) 555-1001'),
  ('Lcda. María Elena Rodríguez Santos', 'maria.rodriguez@cldci.org.do', 'Locutora Profesional / Productora Ejecutiva', CURRENT_DATE - INTERVAL '3 years', 'CLDCI-2021-0002', '001-0234567-8', '(809) 555-1002'),
  ('Lic. Roberto José García Jiménez', 'roberto.garcia@cldci.org.do', 'Director de Noticias / Analista Político', CURRENT_DATE - INTERVAL '2 years', 'CLDCI-2022-0003', '001-0345678-9', '(809) 555-1003'),
  ('Dra. Ana Patricia Jiménez López', 'ana.jimenez@cldci.org.do', 'Especialista en Comunicación / Consultora', CURRENT_DATE - INTERVAL '1 year', 'CLDCI-2023-0004', '001-0456789-0', '(809) 555-1004'),
  ('Lic. Carlos Alberto Santos Reyes', 'carlos.santos@cldci.org.do', 'Locutor Deportivo / Comentarista', CURRENT_DATE - INTERVAL '8 months', 'CLDCI-2024-0005', '001-0567890-1', '(809) 555-1005'),
  ('Prof. Luisa Mercedes González', 'luisa.gonzalez@cldci.org.do', 'Locutora Educativa / Formadora', CURRENT_DATE - INTERVAL '6 months', 'CLDCI-2024-0006', '001-0678901-2', '(809) 555-1006'),
  ('Ing. Miguel Ángel Fernández', 'miguel.fernandez@cldci.org.do', 'Director Técnico / Especialista Audio', CURRENT_DATE - INTERVAL '4 months', 'CLDCI-2024-0007', '001-0789012-3', '(809) 555-1007')
) AS demo_users(nombre, email, profesion, fecha_ingreso, carnet, cedula, telefono)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (SELECT 1 FROM public.miembros WHERE email = demo_users.email);

-- 9. Configurar buckets de storage para producción
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES 
  ('fotos-perfiles', 'fotos-perfiles', true, 5242880, ARRAY['image/jpeg', 'image/png', 'image/webp']),
  ('documentos-oficiales', 'documentos-oficiales', false, 20971520, ARRAY['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
  ('certificados-capacitacion', 'certificados-capacitacion', false, 10485760, ARRAY['application/pdf', 'image/jpeg', 'image/png']),
  ('actas-institucionales', 'actas-institucionales', false, 52428800, ARRAY['application/pdf'])
ON CONFLICT (id) DO NOTHING;

-- 10. Vista final de estadísticas de producción
CREATE OR REPLACE VIEW public.dashboard_produccion AS
SELECT 
  'CLDCI - Sistema de Gestión Institucional v1.0' as sistema,
  'OPERATIVO EN PRODUCCIÓN' as estado,
  (SELECT COUNT(*) FROM public.organizaciones) as organizaciones_totales,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'seccional') as seccionales_provinciales,
  (SELECT COUNT(*) FROM public.organizaciones WHERE tipo = 'seccional_internacional') as seccionales_internacionales,
  (SELECT COUNT(*) FROM public.miembros WHERE estado_membresia = 'activa') as miembros_activos,
  (SELECT COUNT(*) FROM public.capacitaciones WHERE estado = 'programada') as capacitaciones_programadas,
  (SELECT COUNT(*) FROM public.asambleas WHERE estado = 'convocada') as asambleas_convocadas,
  (SELECT COUNT(*) FROM public.padrones_electorales WHERE activo = true) as padrones_activos,
  (SELECT SUM(monto_presupuestado) FROM public.presupuestos WHERE activo = true) as presupuesto_total_2024,
  NOW() as fecha_configuracion;

-- Mensaje de confirmación final
DO $$
DECLARE
  total_orgs int;
  total_members int;
  total_trainings int;
  main_org_id uuid;
BEGIN
  SELECT COUNT(*) INTO total_orgs FROM public.organizaciones;
  SELECT COUNT(*) INTO total_members FROM public.miembros WHERE estado_membresia = 'activa';
  SELECT COUNT(*) INTO total_trainings FROM public.capacitaciones WHERE estado = 'programada';
  SELECT id INTO main_org_id FROM public.organizaciones WHERE codigo = 'CLDCI-001';
  
  RAISE NOTICE '================================================';
  RAISE NOTICE '    SISTEMA CLDCI CONFIGURADO PARA PRODUCCIÓN    ';
  RAISE NOTICE '================================================';
  RAISE NOTICE 'Organización Principal ID: %', main_org_id;
  RAISE NOTICE 'Total Organizaciones: %', total_orgs;
  RAISE NOTICE 'Miembros Activos: %', total_members;
  RAISE NOTICE 'Capacitaciones Programadas: %', total_trainings;
  RAISE NOTICE '------------------------------------------------';
  RAISE NOTICE 'ESTADO: SISTEMA OPERATIVO Y LISTO';
  RAISE NOTICE 'PRÓXIMO PASO: Registrar primer usuario admin';
  RAISE NOTICE '================================================';
END $$;