-- Update member classification system according to Article 13 of CLDC statutes

-- Create enum for member types (clasificación)
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

-- Add additional fields to support the classification system according to Article 13
ALTER TABLE miembros 
ADD COLUMN fecha_fundacion date, -- for founding members
ADD COLUMN motivo_suspension text, -- for passive members
ADD COLUMN fecha_suspension date, -- for passive members  
ADD COLUMN institucion_educativa text, -- for student members
ADD COLUMN pais_residencia text, -- for diaspora members
ADD COLUMN reconocimiento_detalle text; -- for honorary members

-- Create indexes for better performance on member type queries
CREATE INDEX idx_miembros_tipo_membresia ON miembros(tipo_membresia);

-- Add comments to document the member types according to Article 13
COMMENT ON COLUMN miembros.tipo_membresia IS 'Clasificación según Artículo 13: fundador (participaron en Asamblea Constitutiva), activo (cumplen requisitos y al día), pasivo (incumplen requisitos o sancionados), honorífico (reconocimiento por aportes), estudiante (en formación profesional), diáspora (residen en extranjero)';
COMMENT ON COLUMN miembros.fecha_fundacion IS 'Fecha de participación en la Asamblea General Constitutiva (solo para miembros fundadores)';
COMMENT ON COLUMN miembros.motivo_suspension IS 'Motivo por el cual el miembro está en estado pasivo';
COMMENT ON COLUMN miembros.fecha_suspension IS 'Fecha en que inició el estado pasivo del miembro';
COMMENT ON COLUMN miembros.institucion_educativa IS 'Institución donde estudia locución o comunicación (solo para miembros estudiantes)';
COMMENT ON COLUMN miembros.pais_residencia IS 'País de residencia actual (para miembros de la diáspora)';
COMMENT ON COLUMN miembros.reconocimiento_detalle IS 'Detalles del reconocimiento otorgado (solo para miembros honoríficos)';