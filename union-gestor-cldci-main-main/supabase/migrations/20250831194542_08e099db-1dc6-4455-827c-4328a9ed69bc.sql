-- Crear tablas para el módulo de formación profesional

-- Tabla de cursos
CREATE TABLE public.cursos (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  titulo TEXT NOT NULL,
  descripcion TEXT,
  categoria TEXT NOT NULL DEFAULT 'general',
  modalidad TEXT NOT NULL DEFAULT 'presencial',
  nivel TEXT NOT NULL DEFAULT 'basico',
  duracion_horas INTEGER NOT NULL DEFAULT 40,
  precio NUMERIC(10,2) DEFAULT 0,
  instructor TEXT,
  organizacion_id UUID REFERENCES public.organizaciones(id),
  fecha_inicio TIMESTAMP WITH TIME ZONE NOT NULL,
  fecha_fin TIMESTAMP WITH TIME ZONE NOT NULL,
  capacidad_maxima INTEGER DEFAULT 30,
  inscritos_count INTEGER DEFAULT 0,
  lugar TEXT,
  enlace_virtual TEXT,
  requisitos TEXT[],
  objetivos TEXT[],
  temario JSONB,
  certificado_template_url TEXT,
  imagen_url TEXT,
  estado TEXT NOT NULL DEFAULT 'programado',
  created_by UUID,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-- Tabla de diplomados
CREATE TABLE public.diplomados (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  titulo TEXT NOT NULL,
  descripcion TEXT,
  categoria TEXT NOT NULL DEFAULT 'profesional',
  modalidad TEXT NOT NULL DEFAULT 'presencial',
  duracion_meses INTEGER NOT NULL DEFAULT 6,
  creditos_academicos INTEGER DEFAULT 0,
  precio NUMERIC(10,2) DEFAULT 0,
  coordinador_academico TEXT,
  organizacion_id UUID REFERENCES public.organizaciones(id),
  fecha_inicio TIMESTAMP WITH TIME ZONE NOT NULL,
  fecha_fin TIMESTAMP WITH TIME ZONE NOT NULL,
  capacidad_maxima INTEGER DEFAULT 25,
  inscritos_count INTEGER DEFAULT 0,
  lugar TEXT,
  enlace_virtual TEXT,
  requisitos_ingreso TEXT[],
  perfil_egreso TEXT[],
  plan_estudios JSONB,
  certificado_template_url TEXT,
  imagen_url TEXT,
  estado TEXT NOT NULL DEFAULT 'programado',
  created_by UUID,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-- Tabla de inscripciones a cursos
CREATE TABLE public.inscripciones_cursos (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  curso_id UUID NOT NULL REFERENCES public.cursos(id) ON DELETE CASCADE,
  miembro_id UUID NOT NULL REFERENCES public.miembros(id) ON DELETE CASCADE,
  fecha_inscripcion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  estado_inscripcion TEXT NOT NULL DEFAULT 'activa',
  fecha_pago TIMESTAMP WITH TIME ZONE,
  monto_pagado NUMERIC(10,2) DEFAULT 0,
  metodo_pago TEXT,
  comprobante_pago_url TEXT,
  asistencia_porcentaje NUMERIC(5,2) DEFAULT 0,
  calificacion_final NUMERIC(5,2),
  certificado_obtenido BOOLEAN DEFAULT false,
  certificado_url TEXT,
  observaciones TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  UNIQUE(curso_id, miembro_id)
);

-- Tabla de inscripciones a diplomados
CREATE TABLE public.inscripciones_diplomados (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  diplomado_id UUID NOT NULL REFERENCES public.diplomados(id) ON DELETE CASCADE,
  miembro_id UUID NOT NULL REFERENCES public.miembros(id) ON DELETE CASCADE,
  fecha_inscripcion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  estado_inscripcion TEXT NOT NULL DEFAULT 'activa',
  fecha_pago TIMESTAMP WITH TIME ZONE,
  monto_pagado NUMERIC(10,2) DEFAULT 0,
  metodo_pago TEXT,
  comprobante_pago_url TEXT,
  promedio_general NUMERIC(5,2),
  creditos_obtenidos INTEGER DEFAULT 0,
  diploma_obtenido BOOLEAN DEFAULT false,
  diploma_url TEXT,
  observaciones TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  UNIQUE(diplomado_id, miembro_id)
);

-- Tabla de módulos de diplomados
CREATE TABLE public.modulos_diplomados (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  diplomado_id UUID NOT NULL REFERENCES public.diplomados(id) ON DELETE CASCADE,
  nombre_modulo TEXT NOT NULL,
  descripcion TEXT,
  orden INTEGER NOT NULL,
  duracion_horas INTEGER NOT NULL DEFAULT 20,
  instructor TEXT,
  fecha_inicio TIMESTAMP WITH TIME ZONE,
  fecha_fin TIMESTAMP WITH TIME ZONE,
  contenido JSONB,
  recursos_url TEXT[],
  evaluacion_tipo TEXT DEFAULT 'examen',
  peso_evaluacion NUMERIC(5,2) DEFAULT 100,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-- Tabla de evaluaciones de módulos
CREATE TABLE public.evaluaciones_modulos (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  modulo_id UUID NOT NULL REFERENCES public.modulos_diplomados(id) ON DELETE CASCADE,
  inscripcion_diplomado_id UUID NOT NULL REFERENCES public.inscripciones_diplomados(id) ON DELETE CASCADE,
  fecha_evaluacion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  calificacion NUMERIC(5,2) NOT NULL,
  observaciones TEXT,
  evaluador TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  UNIQUE(modulo_id, inscripcion_diplomado_id)
);

-- Habilitar RLS en todas las tablas
ALTER TABLE public.cursos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.diplomados ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.inscripciones_cursos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.inscripciones_diplomados ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.modulos_diplomados ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.evaluaciones_modulos ENABLE ROW LEVEL SECURITY;

-- Políticas RLS para cursos
CREATE POLICY "Todos pueden ver cursos públicos" ON public.cursos
FOR SELECT USING (estado IN ('programado', 'en_curso', 'finalizado'));

CREATE POLICY "Admins pueden gestionar todos los cursos" ON public.cursos
FOR ALL USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderadores pueden gestionar cursos de su organización" ON public.cursos
FOR ALL USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

-- Políticas RLS para diplomados
CREATE POLICY "Todos pueden ver diplomados públicos" ON public.diplomados
FOR SELECT USING (estado IN ('programado', 'en_curso', 'finalizado'));

CREATE POLICY "Admins pueden gestionar todos los diplomados" ON public.diplomados
FOR ALL USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderadores pueden gestionar diplomados de su organización" ON public.diplomados
FOR ALL USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

-- Políticas RLS para inscripciones de cursos
CREATE POLICY "Los usuarios pueden ver sus propias inscripciones de cursos" ON public.inscripciones_cursos
FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM public.miembros m 
    WHERE m.id = inscripciones_cursos.miembro_id 
    AND m.user_id = auth.uid()
  )
);

CREATE POLICY "Los usuarios pueden inscribirse en cursos" ON public.inscripciones_cursos
FOR INSERT WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.miembros m 
    WHERE m.id = inscripciones_cursos.miembro_id 
    AND m.user_id = auth.uid()
  )
);

CREATE POLICY "Admins y moderadores pueden gestionar inscripciones de cursos" ON public.inscripciones_cursos
FOR ALL USING (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM public.cursos c
    WHERE c.id = inscripciones_cursos.curso_id
    AND has_role(auth.uid(), 'moderador'::app_role, c.organizacion_id)
  )
);

-- Políticas RLS para inscripciones de diplomados
CREATE POLICY "Los usuarios pueden ver sus propias inscripciones de diplomados" ON public.inscripciones_diplomados
FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM public.miembros m 
    WHERE m.id = inscripciones_diplomados.miembro_id 
    AND m.user_id = auth.uid()
  )
);

CREATE POLICY "Los usuarios pueden inscribirse en diplomados" ON public.inscripciones_diplomados
FOR INSERT WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.miembros m 
    WHERE m.id = inscripciones_diplomados.miembro_id 
    AND m.user_id = auth.uid()
  )
);

CREATE POLICY "Admins y moderadores pueden gestionar inscripciones de diplomados" ON public.inscripciones_diplomados
FOR ALL USING (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM public.diplomados d
    WHERE d.id = inscripciones_diplomados.diplomado_id
    AND has_role(auth.uid(), 'moderador'::app_role, d.organizacion_id)
  )
);

-- Políticas RLS para módulos de diplomados
CREATE POLICY "Todos pueden ver módulos de diplomados" ON public.modulos_diplomados
FOR SELECT USING (true);

CREATE POLICY "Admins y moderadores pueden gestionar módulos" ON public.modulos_diplomados
FOR ALL USING (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM public.diplomados d
    WHERE d.id = modulos_diplomados.diplomado_id
    AND has_role(auth.uid(), 'moderador'::app_role, d.organizacion_id)
  )
);

-- Políticas RLS para evaluaciones de módulos
CREATE POLICY "Los usuarios pueden ver sus propias evaluaciones" ON public.evaluaciones_modulos
FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM public.inscripciones_diplomados id
    JOIN public.miembros m ON m.id = id.miembro_id
    WHERE id.id = evaluaciones_modulos.inscripcion_diplomado_id
    AND m.user_id = auth.uid()
  )
);

CREATE POLICY "Admins y moderadores pueden gestionar evaluaciones" ON public.evaluaciones_modulos
FOR ALL USING (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM public.modulos_diplomados md
    JOIN public.diplomados d ON d.id = md.diplomado_id
    WHERE md.id = evaluaciones_modulos.modulo_id
    AND has_role(auth.uid(), 'moderador'::app_role, d.organizacion_id)
  )
);

-- Crear triggers para actualizar updated_at
CREATE TRIGGER update_cursos_updated_at
BEFORE UPDATE ON public.cursos
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

CREATE TRIGGER update_diplomados_updated_at
BEFORE UPDATE ON public.diplomados
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

CREATE TRIGGER update_inscripciones_cursos_updated_at
BEFORE UPDATE ON public.inscripciones_cursos
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

CREATE TRIGGER update_inscripciones_diplomados_updated_at
BEFORE UPDATE ON public.inscripciones_diplomados
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

CREATE TRIGGER update_modulos_diplomados_updated_at
BEFORE UPDATE ON public.modulos_diplomados
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

-- Crear índices para mejorar performance
CREATE INDEX ix_cursos_organizacion_id ON public.cursos(organizacion_id);
CREATE INDEX ix_cursos_estado ON public.cursos(estado);
CREATE INDEX ix_cursos_fecha_inicio ON public.cursos(fecha_inicio);

CREATE INDEX ix_diplomados_organizacion_id ON public.diplomados(organizacion_id);
CREATE INDEX ix_diplomados_estado ON public.diplomados(estado);
CREATE INDEX ix_diplomados_fecha_inicio ON public.diplomados(fecha_inicio);

CREATE INDEX ix_inscripciones_cursos_curso_id ON public.inscripciones_cursos(curso_id);
CREATE INDEX ix_inscripciones_cursos_miembro_id ON public.inscripciones_cursos(miembro_id);

CREATE INDEX ix_inscripciones_diplomados_diplomado_id ON public.inscripciones_diplomados(diplomado_id);
CREATE INDEX ix_inscripciones_diplomados_miembro_id ON public.inscripciones_diplomados(miembro_id);