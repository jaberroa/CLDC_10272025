-- Primera migración: Agregar los nuevos tipos de organizaciones según el estatuto de la CLDCI
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'seccional_nacional';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'seccional_internacional';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'asociacion';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'gremio';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'sindicato';
ALTER TYPE tipo_organizacion ADD VALUE IF NOT EXISTS 'otra_entidad';