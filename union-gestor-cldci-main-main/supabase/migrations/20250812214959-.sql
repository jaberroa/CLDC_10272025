-- Actualizar el enum de tipos de organizaciones según el estatuto de la CLDCI
-- Primero, necesitamos ver qué valores existen actualmente y luego agregar los nuevos

-- Agregar los nuevos tipos de organizaciones según el estatuto
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'seccional_nacional';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'seccional_internacional';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'asociacion';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'gremio';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'sindicato';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'otra_entidad';

-- Actualizar las organizaciones existentes con los nuevos tipos más específicos
UPDATE organizaciones 
SET tipo = 'seccional_nacional'::tipo_organizacion 
WHERE tipo = 'seccional'::tipo_organizacion;

-- Insertar ejemplos de los diferentes tipos de organizaciones según el estatuto
INSERT INTO public.organizaciones (
  nombre, 
  tipo, 
  codigo, 
  provincia, 
  ciudad, 
  pais,
  fecha_fundacion,
  email,
  telefono,
  estado_adecuacion
) VALUES
-- Seccionales Nacionales
('Seccional Nacional Santo Domingo', 'seccional_nacional', 'CLDCI-SN-SD', 'Santo Domingo', 'Santo Domingo', 'República Dominicana', '2020-01-15', 'santodomingo@cldci.org', '809-555-0201', 'aprobada'),
('Seccional Nacional Santiago', 'seccional_nacional', 'CLDCI-SN-ST', 'Santiago', 'Santiago de los Caballeros', 'República Dominicana', '2019-03-20', 'santiago@cldci.org', '809-555-0202', 'aprobada'),
('Seccional Nacional La Vega', 'seccional_nacional', 'CLDCI-SN-LV', 'La Vega', 'La Vega', 'República Dominicana', '2021-06-10', 'lavega@cldci.org', '809-555-0203', 'en_revision'),

-- Seccionales Internacionales
('Seccional Internacional Miami', 'seccional_internacional', 'CLDCI-SI-MIA', 'Florida', 'Miami', 'Estados Unidos', '2018-05-12', 'miami@cldci.org', '+1-305-555-0301', 'aprobada'),
('Seccional Internacional Nueva York', 'seccional_internacional', 'CLDCI-SI-NY', 'Nueva York', 'Nueva York', 'Estados Unidos', '2017-09-08', 'newyork@cldci.org', '+1-212-555-0302', 'aprobada'),
('Seccional Internacional Madrid', 'seccional_internacional', 'CLDCI-SI-MAD', 'Madrid', 'Madrid', 'España', '2022-02-14', 'madrid@cldci.org', '+34-91-555-0303', 'en_revision'),

-- Asociaciones
('Asociación de Locutores Deportivos', 'asociacion', 'CLDCI-AS-DEP', 'Santo Domingo', 'Santo Domingo', 'República Dominicana', '2020-08-25', 'deportivos@cldci.org', '809-555-0401', 'aprobada'),
('Asociación de Comunicadores Musicales', 'asociacion', 'CLDCI-AS-MUS', 'Santiago', 'Santiago', 'República Dominicana', '2021-01-18', 'musicales@cldci.org', '809-555-0402', 'aprobada'),

-- Gremios
('Gremio de Productores de Radio', 'gremio', 'CLDCI-GR-PROD', 'Santo Domingo', 'Santo Domingo', 'República Dominicana', '2019-11-30', 'productores@cldci.org', '809-555-0501', 'aprobada'),
('Gremio de Técnicos en Comunicación', 'gremio', 'CLDCI-GR-TEC', 'Santiago', 'Santiago', 'República Dominicana', '2020-04-22', 'tecnicos@cldci.org', '809-555-0502', 'en_revision'),

-- Sindicatos
('Sindicato de Trabajadores de Medios', 'sindicato', 'CLDCI-SIN-MED', 'Santo Domingo', 'Santo Domingo', 'República Dominicana', '2018-12-05', 'sindicato@cldci.org', '809-555-0601', 'aprobada'),

-- Otras Entidades
('Fundación para el Desarrollo de la Comunicación', 'otra_entidad', 'CLDCI-OE-FDC', 'Santo Domingo', 'Santo Domingo', 'República Dominicana', '2021-09-15', 'fundacion@cldci.org', '809-555-0701', 'aprobada'),
('Instituto de Capacitación en Locución', 'otra_entidad', 'CLDCI-OE-ICL', 'Santiago', 'Santiago', 'República Dominicana', '2020-10-12', 'instituto@cldci.org', '809-555-0702', 'en_revision')

ON CONFLICT (codigo) DO NOTHING;

-- Actualizar algunos miembros para que pertenezcan a los nuevos tipos de organizaciones
UPDATE miembros 
SET organizacion_id = (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-AS-DEP' LIMIT 1)
WHERE numero_carnet = 'CLDCI-003';

UPDATE miembros 
SET organizacion_id = (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-GR-PROD' LIMIT 1)
WHERE numero_carnet = 'CLDCI-004';

UPDATE miembros 
SET organizacion_id = (SELECT id FROM organizaciones WHERE codigo = 'CLDCI-SIN-MED' LIMIT 1)
WHERE numero_carnet = 'CLDCI-005';