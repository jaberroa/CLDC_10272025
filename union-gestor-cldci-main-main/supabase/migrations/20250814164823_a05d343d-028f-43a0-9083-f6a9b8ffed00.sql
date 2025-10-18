-- Update member classification system according to Article 13 of CLDC statutes

-- First, let's add a new enum for member types (clasificación)
CREATE TYPE tipo_membresia AS ENUM (
  'fundador',
  'activo', 
  'pasivo',
  'honorifico',
  'estudiante',
  'diaspora'
);

-- Add the new column for member classification
ALTER TABLE miembros 
ADD COLUMN tipo_membresia tipo_membresia DEFAULT 'activo';

-- Update existing estado_membresia enum to be more specific about membership status
-- Create new enum for membership status
DROP TYPE IF EXISTS estado_membresia CASCADE;
CREATE TYPE estado_membresia AS ENUM (
  'pendiente',
  'aprobado',
  'activo',
  'suspendido',
  'inactivo',
  'vencido',
  'expulsado'
);

-- Re-add the estado_membresia column with the updated enum
ALTER TABLE miembros 
ALTER COLUMN estado_membresia TYPE estado_membresia USING estado_membresia::text::estado_membresia;

-- Add additional fields to support the classification system
ALTER TABLE miembros 
ADD COLUMN fecha_fundacion date,
ADD COLUMN motivo_suspension text,
ADD COLUMN fecha_suspension date,
ADD COLUMN institucion_educativa text, -- for student members
ADD COLUMN pais_residencia text, -- for diaspora members
ADD COLUMN reconocimiento_detalle text; -- for honorary members

-- Create index for better performance on member type queries
CREATE INDEX idx_miembros_tipo_membresia ON miembros(tipo_membresia);
CREATE INDEX idx_miembros_estado_membresia ON miembros(estado_membresia);

-- Update RLS policies to handle the new classification system
-- Add comment to document the member types according to Article 13
COMMENT ON COLUMN miembros.tipo_membresia IS 'Clasificación según Artículo 13: fundador, activo, pasivo, honorífico, estudiante, diáspora';
COMMENT ON COLUMN miembros.estado_membresia IS 'Estado actual de la membresía: pendiente, aprobado, activo, suspendido, inactivo, vencido, expulsado';