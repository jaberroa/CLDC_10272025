-- Crear datos de ejemplo para el dashboard

-- Insertar algunos miembros activos
INSERT INTO public.miembros (
  numero_carnet, 
  nombre_completo, 
  estado_membresia,
  organizacion_id,
  email,
  telefono,
  fecha_ingreso
) VALUES
('CLDCI-001', 'Ana María González', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1), 
  'ana.gonzalez@email.com', '809-555-0101', CURRENT_DATE - INTERVAL '2 years'),
('CLDCI-002', 'Carlos Eduardo Pérez', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1), 
  'carlos.perez@email.com', '809-555-0102', CURRENT_DATE - INTERVAL '1 year'),
('CLDCI-003', 'María José Rodríguez', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'seccional' LIMIT 1), 
  'maria.rodriguez@email.com', '809-555-0103', CURRENT_DATE - INTERVAL '6 months'),
('CLDCI-004', 'José Antonio López', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'seccional' LIMIT 1), 
  'jose.lopez@email.com', '809-555-0104', CURRENT_DATE - INTERVAL '3 months'),
('CLDCI-005', 'Carmen Elena Martínez', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'filial' LIMIT 1), 
  'carmen.martinez@email.com', '809-555-0105', CURRENT_DATE - INTERVAL '1 month'),
('CLDCI-006', 'Roberto Miguel Santos', 'activa', 
  (SELECT id FROM organizaciones WHERE tipo = 'filial' LIMIT 1), 
  'roberto.santos@email.com', '809-555-0106', CURRENT_DATE - INTERVAL '15 days'),
('CLDCI-007', 'Luisa Fernanda Jiménez', 'activa', 
  (SELECT id FROM organizaciones LIMIT 1 OFFSET 1), 
  'luisa.jimenez@email.com', '809-555-0107', CURRENT_DATE - INTERVAL '1 week'),
('CLDCI-008', 'Miguel Ángel Vargas', 'activa', 
  (SELECT id FROM organizaciones LIMIT 1 OFFSET 2), 
  'miguel.vargas@email.com', '809-555-0108', CURRENT_DATE);

-- Insertar algunas asambleas programadas
INSERT INTO public.asambleas (
  titulo,
  tipo,
  organizacion_id,
  fecha_convocatoria,
  fecha_asamblea,
  quorum_minimo,
  estado,
  modalidad,
  descripcion
) VALUES
('Asamblea General Ordinaria 2025', 'ordinaria', 
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1),
  CURRENT_DATE - INTERVAL '15 days',
  CURRENT_DATE + INTERVAL '30 days',
  15,
  'convocada',
  'hibrida',
  'Asamblea general ordinaria para revisión de estados financieros y aprobación del presupuesto 2025'),
('Asamblea Extraordinaria - Elecciones', 'extraordinaria', 
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1),
  CURRENT_DATE - INTERVAL '10 days',
  CURRENT_DATE + INTERVAL '45 days',
  20,
  'convocada',
  'presencial',
  'Asamblea extraordinaria para elección de nueva junta directiva'),
('Asamblea Seccional Santo Domingo', 'ordinaria', 
  (SELECT id FROM organizaciones WHERE tipo = 'seccional' LIMIT 1),
  CURRENT_DATE - INTERVAL '5 days',
  CURRENT_DATE + INTERVAL '15 days',
  10,
  'convocada',
  'virtual',
  'Asamblea ordinaria de la seccional de Santo Domingo');

-- Insertar algunas transacciones financieras de ejemplo
INSERT INTO public.transacciones_financieras (
  tipo,
  categoria,
  concepto,
  monto,
  fecha,
  organizacion_id,
  metodo_pago,
  referencia
) VALUES
('ingreso', 'cuotas', 'Cuotas mensuales enero 2025', 15000.00, CURRENT_DATE - INTERVAL '5 days',
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1), 'transferencia', 'TRF-202501-001'),
('egreso', 'administrativos', 'Pago servicios de oficina', 2500.00, CURRENT_DATE - INTERVAL '3 days',
  (SELECT id FROM organizaciones WHERE tipo = 'federacion' LIMIT 1), 'cheque', 'CHQ-202501-001'),
('ingreso', 'eventos', 'Ingresos capacitación profesional', 8000.00, CURRENT_DATE - INTERVAL '2 days',
  (SELECT id FROM organizaciones WHERE tipo = 'seccional' LIMIT 1), 'efectivo', 'EFE-202501-001'),
('egreso', 'eventos', 'Gastos logística evento', 3200.00, CURRENT_DATE - INTERVAL '1 day',
  (SELECT id FROM organizaciones WHERE tipo = 'seccional' LIMIT 1), 'transferencia', 'TRF-202501-002'),
('ingreso', 'cuotas', 'Cuotas afiliación nuevos miembros', 4500.00, CURRENT_DATE,
  (SELECT id FROM organizaciones WHERE tipo = 'filial' LIMIT 1), 'deposito', 'DEP-202501-001');