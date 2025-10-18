-- Poblado de datos de prueba para testing del sistema CLDCI

-- Primero, agregar algunos miembros de prueba a las organizaciones existentes
INSERT INTO public.miembros (
  nombre_completo, 
  cedula, 
  email, 
  telefono, 
  profesion, 
  organizacion_id, 
  numero_carnet, 
  estado_membresia,
  fecha_ingreso
) VALUES 
  (
    'Juan Carlos Pérez',
    '001-1234567-8',
    'jperez@cldci.org',
    '+1 809-555-0101',
    'Locutor Comercial',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'CLDCI-DN-001',
    'activa',
    CURRENT_DATE - INTERVAL '2 years'
  ),
  (
    'María Elena González',
    '001-2345678-9',
    'mgonzalez@cldci.org',
    '+1 809-555-0102',
    'Locutora de Noticias',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'CLDCI-DN-002',
    'activa',
    CURRENT_DATE - INTERVAL '1 year'
  ),
  (
    'Roberto Miguel Santos',
    '001-3456789-0',
    'rsantos@cldci.org',
    '+1 809-555-0103',
    'Locutor Deportivo',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    'CLDCI-STI-001',
    'activa',
    CURRENT_DATE - INTERVAL '3 years'
  ),
  (
    'Ana Teresa Martínez',
    '001-4567890-1',
    'amartinez@cldci.org',
    '+1 809-555-0104',
    'Productora de Radio',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    'CLDCI-STI-002',
    'activa',
    CURRENT_DATE - INTERVAL '6 months'
  ),
  (
    'Carlos Eduardo Valdez',
    '001-5678901-2',
    'cvaldez@cldci.org',
    '+1 212-555-0105',
    'Locutor Internacional',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-NY' LIMIT 1),
    'CLDCI-NY-001',
    'activa',
    CURRENT_DATE - INTERVAL '1 year'
  );

-- Agregar roles de moderador para algunas organizaciones
INSERT INTO public.user_roles (user_id, role, organizacion_id) VALUES
  (
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1),
    'moderador',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1)
  ),
  (
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1),
    'moderador',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1)
  );

-- Agregar algunos presupuestos de prueba
INSERT INTO public.presupuestos (
  organizacion_id,
  categoria,
  periodo,
  monto_presupuestado,
  monto_ejecutado,
  created_by
) VALUES
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'Capacitación',
    '2024',
    50000.00,
    35000.00,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'Eventos',
    '2024',
    75000.00,
    45000.00,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    'Equipamiento',
    '2024',
    30000.00,
    28000.00,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  );

-- Agregar transacciones financieras de prueba
INSERT INTO public.transacciones_financieras (
  organizacion_id,
  tipo,
  categoria,
  concepto,
  monto,
  fecha,
  created_by
) VALUES
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'ingreso',
    'Membresías',
    'Cuotas mensuales enero',
    15000.00,
    '2024-01-15',
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    'gasto',
    'Capacitación',
    'Taller de locución comercial',
    8000.00,
    '2024-01-20',
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    'ingreso',
    'Donaciones',
    'Donación empresa local',
    25000.00,
    '2024-02-10',
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    'gasto',
    'Equipamiento',
    'Compra de micrófonos',
    12000.00,
    '2024-02-15',
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  );

-- Agregar algunos padrones electorales de prueba
INSERT INTO public.padrones_electorales (
  organizacion_id,
  periodo,
  descripcion,
  fecha_inicio,
  fecha_fin,
  total_electores,
  created_by
) VALUES
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    '2025-2028',
    'Padrón electoral para elecciones de directiva CLDCI Distrito Nacional',
    '2024-12-01',
    '2025-03-31',
    25,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    '2025-2028',
    'Padrón electoral para elecciones de directiva CLDCI Santiago',
    '2024-12-01',
    '2025-03-31',
    18,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  );

-- Agregar capacitaciones de prueba
INSERT INTO public.capacitaciones (
  titulo,
  descripcion,
  tipo,
  modalidad,
  fecha_inicio,
  fecha_fin,
  organizacion_id,
  capacidad_maxima,
  costo,
  created_by
) VALUES
  (
    'Taller de Locución Comercial Avanzada',
    'Técnicas avanzadas de locución para publicidad y comerciales de radio y televisión',
    'Taller',
    'presencial',
    '2024-03-15 09:00:00',
    '2024-03-15 17:00:00',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-DN' LIMIT 1),
    20,
    2500.00,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  ),
  (
    'Seminario de Ética en Comunicación',
    'Principios éticos y responsabilidad social en la comunicación masiva',
    'Seminario',
    'virtual',
    '2024-04-10 19:00:00',
    '2024-04-10 21:00:00',
    (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-STI' LIMIT 1),
    50,
    0.00,
    (SELECT id FROM auth.users ORDER BY created_at DESC LIMIT 1)
  );