-- DATOS DE PRUEBA PARA PROTOTIPO CLDCI
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
  'org-nacional-001'::uuid,
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
  'org-santiago-001'::uuid,
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
  'org-lavega-001'::uuid,
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
  'org-sancris-001'::uuid,
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
  'miembro-001'::uuid,
  'org-nacional-001'::uuid,
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
  'miembro-002'::uuid,
  'org-santiago-001'::uuid,
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
  'miembro-003'::uuid,
  'org-lavega-001'::uuid,
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
  'miembro-004'::uuid,
  'org-sancris-001'::uuid,
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
  'miembro-005'::uuid,
  'org-nacional-001'::uuid,
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
  'trans-001'::uuid,
  'org-nacional-001'::uuid,
  '2024-01-15',
  'ingreso',
  'cuotas',
  'Cuota anual 2024 - Carlos Rodríguez',
  2500.00,
  'transferencia',
  'TRF-2024-001'
),
(
  'trans-002'::uuid,
  'org-santiago-001'::uuid,
  '2024-01-20',
  'ingreso',
  'cuotas',
  'Cuota anual 2024 - Ana González',
  2500.00,
  'efectivo',
  'EFE-2024-002'
),
(
  'trans-003'::uuid,
  'org-nacional-001'::uuid,
  '2024-02-01',
  'egreso',
  'gastos_operativos',
  'Papelería y suministros oficina',
  1200.00,
  'cheque',
  'CHE-2024-001'
),
(
  'trans-004'::uuid,
  'org-lavega-001'::uuid,
  '2024-02-10',
  'ingreso',
  'donaciones',
  'Donación empresa local',
  5000.00,
  'transferencia',
  'TRF-2024-003'
)
ON CONFLICT (id) DO NOTHING;

-- Insertar asambleas de prueba
INSERT INTO public.asambleas (
  id,
  organizacion_id,
  titulo,
  descripcion,
  tipo,
  fecha_convocatoria,
  fecha_asamblea,
  lugar,
  modalidad,
  quorum_minimo,
  estado
) VALUES 
(
  'asamblea-001'::uuid,
  'org-nacional-001'::uuid,
  'Asamblea General Ordinaria 2024',
  'Asamblea anual para presentar informes y elegir nueva directiva',
  'ordinaria',
  '2024-02-01',
  '2024-03-15',
  'Salón de Eventos Hotel Nacional',
  'presencial',
  50,
  'convocada'
),
(
  'asamblea-002'::uuid,
  'org-santiago-001'::uuid,
  'Asamblea Extraordinaria Reforma Estatutos',
  'Revisión y actualización de estatutos seccional',
  'extraordinaria',
  '2024-01-15',
  '2024-02-20',
  'Centro de Convenciones Santiago',
  'hibrida',
  30,
  'realizada'
)
ON CONFLICT (id) DO NOTHING;

-- Insertar presupuestos de prueba
INSERT INTO public.presupuestos (
  id,
  organizacion_id,
  periodo,
  categoria,
  monto_presupuestado,
  monto_ejecutado
) VALUES 
(
  'pres-001'::uuid,
  'org-nacional-001'::uuid,
  '2024',
  'ingresos_cuotas',
  500000.00,
  125000.00
),
(
  'pres-002'::uuid,
  'org-nacional-001'::uuid,
  '2024',
  'gastos_operativos',
  200000.00,
  45000.00
),
(
  'pres-003'::uuid,
  'org-santiago-001'::uuid,
  '2024',
  'ingresos_cuotas',
  150000.00,
  50000.00
)
ON CONFLICT (id) DO NOTHING;

-- Insertar capacitaciones de prueba
INSERT INTO public.capacitaciones (
  id,
  organizacion_id,
  titulo,
  descripcion,
  tipo,
  modalidad,
  fecha_inicio,
  fecha_fin,
  lugar,
  capacidad_maxima,
  costo
) VALUES 
(
  'cap-001'::uuid,
  'org-nacional-001'::uuid,
  'Técnicas Avanzadas de Locución Comercial',
  'Curso intensivo sobre técnicas modernas de locución para medios comerciales',
  'tecnico',
  'presencial',
  '2024-03-01',
  '2024-03-03',
  'Estudios CLDCI Nacional',
  20,
  8500.00
),
(
  'cap-002'::uuid,
  'org-santiago-001'::uuid,
  'Locución Deportiva y Transmisiones en Vivo',
  'Especialización en narración deportiva y manejo de transmisiones',
  'especializacion',
  'virtual',
  '2024-04-15',
  '2024-04-17',
  'Plataforma Zoom',
  30,
  6000.00
)
ON CONFLICT (id) DO NOTHING;