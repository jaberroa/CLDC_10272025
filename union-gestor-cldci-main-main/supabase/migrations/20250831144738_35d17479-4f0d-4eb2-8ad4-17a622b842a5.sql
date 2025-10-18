-- CORRECCIÓN Y CONFIGURACIÓN COMPLETA PARA PRODUCCIÓN CLDCI

-- 1. Verificar y corregir enums existentes
DO $$
BEGIN
  -- Agregar valores faltantes al enum tipo_organizacion si no existen
  IF NOT EXISTS (SELECT 1 FROM pg_enum WHERE enumlabel = 'nacional' AND enumtypid = (SELECT oid FROM pg_type WHERE typname = 'tipo_organizacion')) THEN
    ALTER TYPE tipo_organizacion ADD VALUE 'nacional';
  END IF;
  
  IF NOT EXISTS (SELECT 1 FROM pg_enum WHERE enumlabel = 'diaspora' AND enumtypid = (SELECT oid FROM pg_type WHERE typname = 'tipo_organizacion')) THEN
    ALTER TYPE tipo_organizacion ADD VALUE 'diaspora';
  END IF;
END $$;

-- 2. Crear organización principal CLDCI
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

-- 3. Crear seccionales provinciales
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

-- 4. Crear seccionales diáspora
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
WHERE NOT EXISTS (SELECT 1 FROM public.organizaciones WHERE nombre LIKE '%Diáspora ' || diaspora.pais);

-- 5. Crear padrón electoral nacional
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
  'Padrón Electoral Nacional CLDCI 2024-2026',
  '2024-01-01',
  '2026-12-31',
  true
) ON CONFLICT DO NOTHING;

-- 6. Crear estructura presupuestaria
INSERT INTO public.presupuestos (
  organizacion_id,
  categoria,
  periodo,
  monto_presupuestado,
  activo
)
SELECT 
  org.id,
  categoria,
  '2024',
  monto,
  true
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Cuotas de Membresía', 750000.00),
  ('Eventos y Capacitaciones', 200000.00),
  ('Gastos Administrativos', 150000.00),
  ('Tecnología e Innovación', 100000.00),
  ('Comunicación Institucional', 80000.00),
  ('Programas de Formación', 120000.00),
  ('Representación Legal', 60000.00),
  ('Actividades Sociales', 40000.00)
) AS cats(categoria, monto)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (
  SELECT 1 FROM public.presupuestos p 
  WHERE p.organizacion_id = org.id AND p.categoria = cats.categoria
);

-- 7. Crear primera asamblea del año
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
  'Asamblea General Ordinaria 2024',
  'Primera asamblea del sistema digital CLDCI - Aprobación de nuevos estatutos digitales',
  NOW() + INTERVAL '7 days',
  NOW() + INTERVAL '45 days', 
  60,
  'Sede Nacional CLDCI, Santo Domingo',
  'hibrida',
  'convocada'
) ON CONFLICT DO NOTHING;

-- 8. Crear programa inicial de capacitaciones
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
  NOW() + INTERVAL '20 days',
  NOW() + INTERVAL '22 days',
  capacidad,
  lugar,
  'programada',
  costo
FROM public.organizaciones org
CROSS JOIN (VALUES
  ('Locución Digital y Nuevas Tecnologías', 'Capacitación en herramientas digitales para locutores modernos', 'presencial', 'Sede Nacional CLDCI', 40, 2500.00),
  ('Ética y Responsabilidad Profesional', 'Principios éticos en la comunicación radial', 'virtual', 'Plataforma Zoom', 60, 1500.00),
  ('Gestión de Contenido Digital', 'Creación y administración de contenido multimedia', 'hibrida', 'Sede Nacional + Online', 30, 3000.00),
  ('Marco Legal de la Comunicación', 'Aspectos legales del ejercicio profesional', 'presencial', 'Sede Nacional CLDCI', 35, 2000.00)
) AS caps(titulo, descripcion, modalidad, lugar, capacidad, costo)
WHERE org.codigo = 'CLDCI-001'
AND NOT EXISTS (
  SELECT 1 FROM public.capacitaciones c 
  WHERE c.organizacion_id = org.id AND c.titulo = caps.titulo
);

-- 9. Función para generar número de carnet único
CREATE OR REPLACE FUNCTION public.generate_member_carnet(org_code text DEFAULT 'CLDCI')
RETURNS text
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
  year_suffix text := EXTRACT(YEAR FROM NOW())::text;
  sequence_num int;
  carnet_number text;
BEGIN
  -- Obtener próximo número de secuencia
  SELECT COALESCE(MAX(CAST(SUBSTRING(numero_carnet FROM '[0-9]+$') AS int)), 0) + 1
  INTO sequence_num
  FROM miembros 
  WHERE numero_carnet LIKE org_code || '-' || year_suffix || '-%';
  
  -- Formatear número de carnet
  carnet_number := org_code || '-' || year_suffix || '-' || LPAD(sequence_num::text, 4, '0');
  
  RETURN carnet_number;
END;
$$;

-- 10. Trigger para generar carnet automáticamente
CREATE OR REPLACE FUNCTION public.auto_generate_carnet()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
  org_code text;
BEGIN
  -- Obtener código de organización
  SELECT codigo INTO org_code
  FROM organizaciones 
  WHERE id = NEW.organizacion_id;
  
  -- Si no tiene número de carnet, generarlo
  IF NEW.numero_carnet IS NULL OR NEW.numero_carnet = '' THEN
    NEW.numero_carnet := public.generate_member_carnet(COALESCE(org_code, 'CLDCI'));
  END IF;
  
  RETURN NEW;
END;
$$;

-- Crear trigger si no existe
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'trigger_auto_carnet') THEN
    CREATE TRIGGER trigger_auto_carnet
      BEFORE INSERT ON public.miembros
      FOR EACH ROW
      EXECUTE FUNCTION public.auto_generate_carnet();
  END IF;
END $$;

-- 11. Configurar políticas de storage para producción
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES 
  ('perfiles', 'perfiles', true, 5242880, ARRAY['image/jpeg', 'image/png', 'image/webp']),
  ('certificados', 'certificados', false, 10485760, ARRAY['application/pdf', 'image/jpeg', 'image/png'])
ON CONFLICT (id) DO NOTHING;

-- Políticas de acceso a archivos de perfil
CREATE POLICY IF NOT EXISTS "Avatars are publicly accessible"
ON storage.objects FOR SELECT
USING (bucket_id = 'perfiles');

CREATE POLICY IF NOT EXISTS "Users can upload their own avatar"
ON storage.objects FOR INSERT
WITH CHECK (
  bucket_id = 'perfiles' 
  AND auth.uid()::text = (storage.foldername(name))[1]
);

-- 12. Insertar datos de prueba para demostración inmediata
INSERT INTO public.miembros (
  organizacion_id,
  nombre_completo,
  email,
  profesion,
  estado_membresia,
  fecha_ingreso,
  user_id
) VALUES (
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  'Juan Carlos Pérez',
  'jperez@ejemplo.com',
  'Locutor Profesional',
  'activa',
  CURRENT_DATE - INTERVAL '2 years',
  '00000000-0000-0000-0000-000000000001'
),
(
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  'María Rodríguez Santos',
  'mrodriguez@ejemplo.com',
  'Locutora y Productora',
  'activa',
  CURRENT_DATE - INTERVAL '1 year',
  '00000000-0000-0000-0000-000000000002'
),
(
  (SELECT id FROM public.organizaciones WHERE codigo = 'CLDCI-001' LIMIT 1),
  'Roberto Manuel García',
  'rgarcia@ejemplo.com',
  'Director de Radio',
  'activa',
  CURRENT_DATE - INTERVAL '6 months',
  '00000000-0000-0000-0000-000000000003'
)
ON CONFLICT DO NOTHING;

-- Mensaje final
SELECT 
  'Base de datos CLDCI configurada exitosamente' as status,
  (SELECT COUNT(*) FROM public.organizaciones) as organizaciones_creadas,
  (SELECT COUNT(*) FROM public.miembros) as miembros_demo,
  (SELECT COUNT(*) FROM public.capacitaciones) as capacitaciones_disponibles;