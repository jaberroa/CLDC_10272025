-- DATOS DE PRUEBA PARA PROTOTIPO CLDCI (UUIDs corregidos)
-- Crear organizaciones, usuarios de prueba y datos iniciales

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
  'Círculo de Locutores Dominicanos Colegiado Nacional',
  'CLDCI-NAL',
  'nacional',
  'Distrito Nacional',
  'Santo Domingo',
  'info@cldci.org',
  '(809) 555-0001',
  'aprobado'
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
  'aprobado'
),
(
  '550e8400-e29b-41d4-a716-446655440003'::uuid,
  'CLDCI Seccional La Vega',
  'CLDCI-LV',
  'seccional',
  'La Vega',
  'Concepción de La Vega',
  'lavega@cldci.org',
  '(809) 555-0003',
  'pendiente'
),
(
  '550e8400-e29b-41d4-a716-446655440004'::uuid,
  'CLDCI Seccional San Cristóbal',
  'CLDCI-SC',
  'seccional',
  'San Cristóbal',
  'San Cristóbal',
  'sancristobal@cldci.org',
  '(809) 555-0004',
  'aprobado'
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

-- Insertar transacciones financieras de prueba
INSERT INTO public.transacciones_financieras (
  id,
  organizacion_id,
  fecha,
  tipo,
  categoria,
  concepto,
  monto,
  metodo_pago,
  referencia
) VALUES 
(
  '770e8400-e29b-41d4-a716-446655440001'::uuid,
  '550e8400-e29b-41d4-a716-446655440001'::uuid,
  '2024-01-15',
  'ingreso',
  'cuotas',
  'Cuota anual 2024 - Carlos Rodríguez',
  2500.00,
  'transferencia',
  'TRF-2024-001'
),
(
  '770e8400-e29b-41d4-a716-446655440002'::uuid,
  '550e8400-e29b-41d4-a716-446655440002'::uuid,
  '2024-01-20',
  'ingreso',
  'cuotas',
  'Cuota anual 2024 - Ana González',
  2500.00,
  'efectivo',
  'EFE-2024-002'
),
(
  '770e8400-e29b-41d4-a716-446655440003'::uuid,
  '550e8400-e29b-41d4-a716-446655440001'::uuid,
  '2024-02-01',
  'egreso',
  'gastos_operativos',
  'Papelería y suministros oficina',
  1200.00,
  'cheque',
  'CHE-2024-001'
),
(
  '770e8400-e29b-41d4-a716-446655440004'::uuid,
  '550e8400-e29b-41d4-a716-446655440003'::uuid,
  '2024-02-10',
  'ingreso',
  'donaciones',
  'Donación empresa local',
  5000.00,
  'transferencia',
  'TRF-2024-003'
)
ON CONFLICT (id) DO NOTHING;