-- DATOS DE PRUEBA PARA PROTOTIPO CLDCI (Estados enum corregidos)
-- Crear organizaciones y miembros de prueba

-- Insertar organizaciones de prueba
INSERT INTO public.organizaciones (
  id, 
  nombre, 
  codigo, 
  tipo, 
  provincia, 
  ciudad, 
  email, 
  telefono, 
  estado_adecuacion
) VALUES 
(
  '550e8400-e29b-41d4-a716-446655440001'::uuid,
  'CLDCI Seccional Distrito Nacional',
  'CLDCI-DN',
  'seccional',
  'Distrito Nacional',
  'Santo Domingo',
  'dn@cldci.org',
  '(809) 555-0001',
  'aprobada'
),
(
  '550e8400-e29b-41d4-a716-446655440002'::uuid,
  'CLDCI Seccional Santiago',
  'CLDCI-STI',
  'seccional',
  'Santiago',
  'Santiago de los Caballeros',
  'santiago@cldci.org',
  '(809) 555-0002',
  'aprobada'
),
(
  '550e8400-e29b-41d4-a716-446655440003'::uuid,
  'CLDCI Filial La Vega',
  'CLDCI-LV',
  'filial',
  'La Vega',
  'Concepción de La Vega',
  'lavega@cldci.org',
  '(809) 555-0003',
  'en_revision'
),
(
  '550e8400-e29b-41d4-a716-446655440004'::uuid,
  'CLDCI Delegación San Cristóbal',
  'CLDCI-SC',
  'delegacion',
  'San Cristóbal',
  'San Cristóbal',
  'sancristobal@cldci.org',
  '(809) 555-0004',
  'aprobada'
)
ON CONFLICT (id) DO NOTHING;

-- Insertar miembros de prueba
INSERT INTO public.miembros (
  id,
  organizacion_id,
  numero_carnet,
  nombre_completo,
  cedula,
  email,
  telefono,
  profesion,
  estado_membresia,
  fecha_ingreso
) VALUES 
(
  '660e8400-e29b-41d4-a716-446655440001'::uuid,
  '550e8400-e29b-41d4-a716-446655440001'::uuid,
  'CLDCI-001',
  'Dr. Carlos Rodríguez Pérez',
  '001-1234567-8',
  'carlos.rodriguez@cldci.org',
  '(809) 555-1001',
  'Locutor Comercial',
  'activo',
  '2020-01-15'
),
(
  '660e8400-e29b-41d4-a716-446655440002'::uuid,
  '550e8400-e29b-41d4-a716-446655440002'::uuid,
  'CLDCI-002',
  'Ana María González Jiménez',
  '001-2345678-9',
  'ana.gonzalez@cldci.org',
  '(809) 555-1002',
  'Locutora Deportiva',
  'activo',
  '2021-03-20'
),
(
  '660e8400-e29b-41d4-a716-446655440003'::uuid,
  '550e8400-e29b-41d4-a716-446655440003'::uuid,
  'CLDCI-003',
  'José Antonio Martínez López',
  '001-3456789-0',
  'jose.martinez@cldci.org',
  '(809) 555-1003',
  'Locutor Musical',
  'activo',
  '2019-06-10'
),
(
  '660e8400-e29b-41d4-a716-446655440004'::uuid,
  '550e8400-e29b-41d4-a716-446655440004'::uuid,
  'CLDCI-004',
  'María Elena Fernández Cruz',
  '001-4567890-1',
  'maria.fernandez@cldci.org',
  '(809) 555-1004',
  'Locutora Comercial',
  'activo',
  '2022-02-28'
),
(
  '660e8400-e29b-41d4-a716-446655440005'::uuid,
  '550e8400-e29b-41d4-a716-446655440001'::uuid,
  'CLDCI-005',
  'Roberto Luis Hernández Díaz',
  '001-5678901-2',
  'roberto.hernandez@cldci.org',
  '(809) 555-1005',
  'Locutor de Noticias',
  'activo',
  '2020-09-15'
)
ON CONFLICT (id) DO NOTHING;