-- Insert a test directivo member to test functionality
INSERT INTO miembros_directivos (
  miembro_id,
  organo_id,
  cargo_id,
  fecha_inicio,
  periodo,
  semblanza,
  es_presidente,
  email_institucional,
  telefono_institucional,
  estado
) VALUES (
  (SELECT id FROM miembros WHERE nombre_completo = 'Juan Carlos Pérez' LIMIT 1),
  (SELECT id FROM organos_cldc WHERE nombre = 'Consejo Directivo Nacional' LIMIT 1),
  (SELECT id FROM cargos_organos WHERE nombre_cargo = 'Presidente' LIMIT 1),
  '2025-01-01',
  '2025-2028',
  'Presidente del CLDC, líder experimentado en la industria de las comunicaciones con más de 15 años de trayectoria profesional.',
  true,
  'presidente@cldc.org.do', 
  '+1 809-555-0100',
  'activo'
);

-- Insert another test directivo
INSERT INTO miembros_directivos (
  miembro_id,
  organo_id,
  cargo_id,
  fecha_inicio,
  periodo,
  semblanza,
  es_presidente,
  email_institucional,
  telefono_institucional,
  estado
) VALUES (
  (SELECT id FROM miembros WHERE nombre_completo = 'María Elena González' LIMIT 1),
  (SELECT id FROM organos_cldc WHERE nombre = 'Consejo Directivo Nacional' LIMIT 1),
  (SELECT id FROM cargos_organos WHERE nombre_cargo = 'Vicepresidente' LIMIT 1),
  '2025-01-01',
  '2025-2028',
  'Vicepresidenta del CLDC, especialista en comunicación corporativa y desarrollo organizacional.',
  false,
  'vicepresidente@cldc.org.do',
  '+1 809-555-0101',
  'activo'
);

-- Create a test asamblea general
INSERT INTO asambleas_generales (
  tipo_asamblea,
  fecha_convocatoria,
  fecha_celebracion,
  lugar,
  modalidad,
  quorum_minimo,
  tema_principal,
  orden_dia,
  estado,
  organizacion_id
) VALUES (
  'ordinaria',
  '2025-02-15 09:00:00',
  '2025-03-15 10:00:00',
  'Auditorio CLDC, Santo Domingo',
  'presencial',
  50,
  'Asamblea General Ordinaria 2025',
  ARRAY['Informe de Gestión', 'Estados Financieros', 'Elección de Directiva', 'Asuntos Varios'],
  'convocada',
  (SELECT id FROM organizaciones WHERE nombre = 'Colegio de Locutores de la República Dominicana' LIMIT 1)
);

-- Create test seccionales
INSERT INTO seccionales (
  nombre,
  tipo,
  pais,
  provincia,
  ciudad,
  direccion,
  telefono,
  email,
  coordinador_id,
  miembros_count,
  fecha_fundacion,
  estado,
  organizacion_id
) VALUES (
  'Seccional Santiago',
  'provincial',
  'República Dominicana',
  'Santiago',
  'Santiago de los Caballeros',
  'Calle Principal No. 123, Santiago',
  '+1 809-555-0200',
  'santiago@cldc.org.do',
  (SELECT id FROM miembros WHERE nombre_completo = 'Roberto Miguel Santos' LIMIT 1),
  25,
  '2020-05-15',
  'activa',
  (SELECT id FROM organizaciones WHERE nombre = 'Colegio de Locutores de la República Dominicana' LIMIT 1)
),
(
  'Seccional Nueva York',
  'diaspora',
  'Estados Unidos',
  'Nueva York',
  'New York City',
  '123 Broadway, New York, NY 10001',
  '+1 212-555-0300',
  'nuevayork@cldc.org.do',
  (SELECT id FROM miembros WHERE nombre_completo = 'Carlos Eduardo Valdez' LIMIT 1),
  15,
  '2021-08-10',
  'activa',
  (SELECT id FROM organizaciones WHERE nombre = 'Colegio de Locutores de la República Dominicana' LIMIT 1)
);