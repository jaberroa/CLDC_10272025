-- Crear tablas para la estructura organizativa completa del CLDC

-- Tabla para órganos organizativos (directivos, consultivos, operativos, territoriales)
CREATE TABLE public.organos_cldc (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    nombre TEXT NOT NULL,
    tipo_organo TEXT NOT NULL CHECK (tipo_organo IN ('direccion', 'consultivo', 'operativo', 'territorial')),
    descripcion TEXT,
    funciones TEXT[],
    nivel_jerarquico INTEGER DEFAULT 1,
    organizacion_id UUID REFERENCES public.organizaciones(id),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Tabla para cargos dentro de cada órgano
CREATE TABLE public.cargos_organos (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    organo_id UUID NOT NULL REFERENCES public.organos_cldc(id) ON DELETE CASCADE,
    nombre_cargo TEXT NOT NULL,
    descripcion TEXT,
    nivel_autoridad INTEGER DEFAULT 1,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Tabla para miembros directivos (actualizada)
CREATE TABLE public.miembros_directivos (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    miembro_id UUID REFERENCES public.miembros(id),
    organo_id UUID NOT NULL REFERENCES public.organos_cldc(id),
    cargo_id UUID NOT NULL REFERENCES public.cargos_organos(id),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    periodo TEXT NOT NULL,
    estado TEXT DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo', 'suspendido')),
    semblanza TEXT,
    es_presidente BOOLEAN DEFAULT false,
    foto_url TEXT,
    email_institucional TEXT,
    telefono_institucional TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Tabla para Asamblea General de Delegados
CREATE TABLE public.asambleas_generales (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    tipo_asamblea TEXT NOT NULL CHECK (tipo_asamblea IN ('ordinaria', 'extraordinaria')),
    fecha_convocatoria TIMESTAMP WITH TIME ZONE NOT NULL,
    fecha_celebracion TIMESTAMP WITH TIME ZONE NOT NULL,
    lugar TEXT,
    modalidad TEXT DEFAULT 'presencial' CHECK (modalidad IN ('presencial', 'virtual', 'mixta')),
    enlace_virtual TEXT,
    quorum_minimo INTEGER NOT NULL,
    asistentes_count INTEGER DEFAULT 0,
    quorum_alcanzado BOOLEAN DEFAULT false,
    tema_principal TEXT NOT NULL,
    orden_dia TEXT[],
    acta_url TEXT,
    estado TEXT DEFAULT 'convocada' CHECK (estado IN ('convocada', 'celebrada', 'suspendida', 'cancelada')),
    organizacion_id UUID REFERENCES public.organizaciones(id),
    created_by UUID,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Tabla para delegados de la Asamblea General
CREATE TABLE public.delegados_asamblea (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    asamblea_id UUID NOT NULL REFERENCES public.asambleas_generales(id) ON DELETE CASCADE,
    miembro_id UUID REFERENCES public.miembros(id),
    tipo_delegado TEXT NOT NULL CHECK (tipo_delegado IN ('consejo_directivo', 'asociacion_afiliada', 'seccional_provincial', 'seccional_diaspora')),
    organizacion_origen_id UUID REFERENCES public.organizaciones(id),
    presente BOOLEAN DEFAULT false,
    fecha_registro TIMESTAMP WITH TIME ZONE DEFAULT now(),
    observaciones TEXT
);

-- Tabla para seccionales provinciales y de la diáspora
CREATE TABLE public.seccionales (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    nombre TEXT NOT NULL,
    tipo TEXT NOT NULL CHECK (tipo IN ('provincial', 'regional', 'diaspora')),
    pais TEXT DEFAULT 'República Dominicana',
    provincia TEXT,
    ciudad TEXT,
    direccion TEXT,
    telefono TEXT,
    email TEXT,
    coordinador_id UUID REFERENCES public.miembros(id),
    miembros_count INTEGER DEFAULT 0,
    fecha_fundacion DATE,
    estado TEXT DEFAULT 'activa' CHECK (estado IN ('activa', 'inactiva', 'en_formacion')),
    organizacion_id UUID REFERENCES public.organizaciones(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Tabla para comités ejecutivos seccionales
CREATE TABLE public.comites_ejecutivos_seccionales (
    id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
    seccional_id UUID NOT NULL REFERENCES public.seccionales(id) ON DELETE CASCADE,
    miembro_id UUID NOT NULL REFERENCES public.miembros(id),
    cargo TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    periodo TEXT NOT NULL,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Poblar órganos básicos del CLDC
INSERT INTO public.organos_cldc (nombre, tipo_organo, descripcion, funciones, nivel_jerarquico, activo) VALUES
-- Órganos de Dirección
('Asamblea General de Delegados', 'direccion', 'Máximo órgano de dirección del CLDC', ARRAY['Dirigir la política general del CLDC', 'Elegir la directiva nacional', 'Aprobar estatutos y reglamentos'], 1, true),
('Consejo Directivo Nacional', 'direccion', 'Órgano ejecutivo del CLDC', ARRAY['Ejecutar las decisiones de la Asamblea General', 'Administrar la federación', 'Representar el gremio'], 2, true),
('Presidencia', 'direccion', 'Máxima autoridad ejecutiva', ARRAY['Representar legalmente al CLDC', 'Dirigir el Consejo Directivo', 'Ejecutar acuerdos'], 3, true),

-- Órganos Consultivos
('Consejo Consultivo de Ex Presidentes', 'consultivo', 'Órgano asesor integrado por ex presidentes', ARRAY['Asesorar al Consejo Directivo Nacional', 'Velar por la continuidad institucional', 'Mediar en conflictos internos'], 1, true),
('Comité de Ética y Disciplina', 'consultivo', 'Órgano disciplinario y de ética profesional', ARRAY['Velar por el cumplimiento del código de ética', 'Instruir procedimientos disciplinarios', 'Proponer sanciones'], 1, true),
('Comisión Electoral', 'consultivo', 'Órgano electoral interno', ARRAY['Organizar procesos electorales', 'Verificar requisitos de candidatos', 'Resolver impugnaciones', 'Proclamar resultados'], 1, true),

-- Órganos Operativos
('Dirección Ejecutiva', 'operativo', 'Dirección administrativa general', ARRAY['Coordinar la administración general', 'Ejecutar decisiones del Consejo Directivo'], 1, true),
('Dirección de Formación y Desarrollo Profesional', 'operativo', 'Área de capacitación y desarrollo', ARRAY['Coordinar programas de capacitación', 'Desarrollar competencias profesionales'], 1, true),
('Dirección de Tecnología e Innovación Digital', 'operativo', 'Área de tecnología e innovación', ARRAY['Gestionar sistemas tecnológicos', 'Promover innovación digital'], 1, true),
('Dirección de Comunicación y Relaciones Públicas', 'operativo', 'Área de comunicación institucional', ARRAY['Gestionar comunicación institucional', 'Manejar relaciones públicas'], 1, true),
('Dirección de Asuntos Legales y Gremiales', 'operativo', 'Área legal y gremial', ARRAY['Gestionar asuntos legales', 'Defender intereses gremiales'], 1, true),
('Dirección de Deporte y Recreación', 'operativo', 'Área deportiva y recreativa', ARRAY['Organizar actividades deportivas', 'Coordinar eventos recreativos'], 1, true),
('Dirección de Programas Estudiantiles y Voluntariado', 'operativo', 'Área estudiantil y voluntariado', ARRAY['Coordinar programas estudiantiles', 'Gestionar voluntariado'], 1, true),
('Dirección de Asuntos de la Diáspora', 'operativo', 'Área de la diáspora', ARRAY['Coordinar con locutores en el exterior', 'Gestionar seccionales internacionales'], 1, true),

-- Órganos Territoriales
('Seccionales Provinciales y Regionales', 'territorial', 'Representaciones territoriales nacionales', ARRAY['Representar al CLDC territorialmente', 'Ejecutar políticas locales', 'Recaudar cuotas'], 1, true),
('Seccionales de la Diáspora', 'territorial', 'Representaciones internacionales', ARRAY['Representar al CLDC en el exterior', 'Coordinar con la diáspora'], 1, true),
('Coordinación de Asociaciones Afiliadas', 'territorial', 'Coordinación con asociaciones afiliadas', ARRAY['Coordinar asociaciones afiliadas', 'Mantener vínculos institucionales'], 1, true);

-- Crear cargos para el Consejo Directivo Nacional
INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Presidente', 'Máxima autoridad del CLDC', 1 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Vicepresidente', 'Segunda autoridad del CLDC', 2 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director General', 'Administrador principal', 3 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Finanzas', 'Responsable financiero', 4 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Comunicación', 'Responsable de comunicación', 5 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Tecnología', 'Responsable tecnológico', 6 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Formación Profesional', 'Responsable de capacitación', 7 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Asuntos Legales', 'Responsable legal', 8 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Relaciones Internacionales', 'Responsable internacional', 9 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Deporte y Recreación', 'Responsable deportivo', 10 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Programas Estudiantiles y Voluntariado', 'Responsable estudiantil', 11 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Director de Asuntos de la Diáspora', 'Responsable diáspora', 12 FROM public.organos_cldc WHERE nombre = 'Consejo Directivo Nacional';

-- Crear cargos para Comité de Ética y Disciplina
INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Presidente', 'Presidente del Comité de Ética', 1 FROM public.organos_cldc WHERE nombre = 'Comité de Ética y Disciplina';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Miembro', 'Miembro del Comité de Ética', 2 FROM public.organos_cldc WHERE nombre = 'Comité de Ética y Disciplina';

-- Crear cargos para Comisión Electoral
INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Presidente', 'Presidente de la Comisión Electoral', 1 FROM public.organos_cldc WHERE nombre = 'Comisión Electoral';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Miembro', 'Miembro de la Comisión Electoral', 2 FROM public.organos_cldc WHERE nombre = 'Comisión Electoral';

-- Crear cargos para seccionales
INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Coordinador Seccional', 'Coordinador de la seccional', 1 FROM public.organos_cldc WHERE nombre = 'Seccionales Provinciales y Regionales';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Secretario', 'Secretario de la seccional', 2 FROM public.organos_cldc WHERE nombre = 'Seccionales Provinciales y Regionales';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Tesorero', 'Tesorero de la seccional', 3 FROM public.organos_cldc WHERE nombre = 'Seccionales Provinciales y Regionales';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Vocal de Comunicación', 'Responsable de comunicación', 4 FROM public.organos_cldc WHERE nombre = 'Seccionales Provinciales y Regionales';

INSERT INTO public.cargos_organos (organo_id, nombre_cargo, descripcion, nivel_autoridad) 
SELECT id, 'Vocal de Formación', 'Responsable de formación', 5 FROM public.organos_cldc WHERE nombre = 'Seccionales Provinciales y Regionales';

-- RLS Policies
ALTER TABLE public.organos_cldc ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cargos_organos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.miembros_directivos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.asambleas_generales ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.delegados_asamblea ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.seccionales ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.comites_ejecutivos_seccionales ENABLE ROW LEVEL SECURITY;

-- Políticas para órganos CLDC
CREATE POLICY "Cualquiera puede ver órganos públicos" ON public.organos_cldc FOR SELECT USING (true);
CREATE POLICY "Solo admins pueden gestionar órganos" ON public.organos_cldc FOR ALL USING (has_role(auth.uid(), 'admin'::app_role));

-- Políticas para cargos de órganos
CREATE POLICY "Cualquiera puede ver cargos" ON public.cargos_organos FOR SELECT USING (true);
CREATE POLICY "Solo admins pueden gestionar cargos" ON public.cargos_organos FOR ALL USING (has_role(auth.uid(), 'admin'::app_role));

-- Políticas para miembros directivos
CREATE POLICY "Cualquiera puede ver directivos públicos" ON public.miembros_directivos FOR SELECT USING (true);
CREATE POLICY "Admins pueden gestionar directivos" ON public.miembros_directivos FOR ALL USING (has_role(auth.uid(), 'admin'::app_role));
CREATE POLICY "Moderadores pueden gestionar directivos de su org" ON public.miembros_directivos FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.organos_cldc o 
        WHERE o.id = miembros_directivos.organo_id 
        AND has_role(auth.uid(), 'moderador'::app_role, o.organizacion_id)
    )
);

-- Políticas para asambleas generales
CREATE POLICY "Usuarios pueden ver asambleas de su org" ON public.asambleas_generales FOR SELECT USING (
    EXISTS (
        SELECT 1 FROM user_roles ur 
        WHERE ur.user_id = auth.uid() 
        AND (ur.organizacion_id = asambleas_generales.organizacion_id OR ur.role = 'admin'::app_role)
    )
);
CREATE POLICY "Admins y moderadores pueden gestionar asambleas" ON public.asambleas_generales FOR ALL USING (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
);

-- Políticas para delegados
CREATE POLICY "Usuarios pueden ver delegados de asambleas de su org" ON public.delegados_asamblea FOR SELECT USING (
    EXISTS (
        SELECT 1 FROM public.asambleas_generales ag
        JOIN user_roles ur ON (ur.organizacion_id = ag.organizacion_id OR ur.role = 'admin'::app_role)
        WHERE ag.id = delegados_asamblea.asamblea_id AND ur.user_id = auth.uid()
    )
);
CREATE POLICY "Admins y moderadores pueden gestionar delegados" ON public.delegados_asamblea FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.asambleas_generales ag
        WHERE ag.id = delegados_asamblea.asamblea_id 
        AND (has_role(auth.uid(), 'admin'::app_role) OR has_role(auth.uid(), 'moderador'::app_role, ag.organizacion_id))
    )
);

-- Políticas para seccionales
CREATE POLICY "Cualquiera puede ver seccionales públicas" ON public.seccionales FOR SELECT USING (true);
CREATE POLICY "Admins pueden gestionar todas las seccionales" ON public.seccionales FOR ALL USING (has_role(auth.uid(), 'admin'::app_role));
CREATE POLICY "Moderadores pueden gestionar seccionales de su org" ON public.seccionales FOR ALL USING (
    has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
);

-- Políticas para comités ejecutivos seccionales
CREATE POLICY "Usuarios pueden ver comités ejecutivos públicos" ON public.comites_ejecutivos_seccionales FOR SELECT USING (true);
CREATE POLICY "Admins pueden gestionar comités ejecutivos" ON public.comites_ejecutivos_seccionales FOR ALL USING (has_role(auth.uid(), 'admin'::app_role));
CREATE POLICY "Coordinadores pueden gestionar su comité" ON public.comites_ejecutivos_seccionales FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.seccionales s
        WHERE s.id = comites_ejecutivos_seccionales.seccional_id 
        AND (s.coordinador_id IN (
            SELECT id FROM public.miembros WHERE user_id = auth.uid()
        ) OR has_role(auth.uid(), 'moderador'::app_role, s.organizacion_id))
    )
);

-- Triggers para updated_at
CREATE OR REPLACE FUNCTION public.update_updated_at_organos()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_organos_cldc_updated_at
    BEFORE UPDATE ON public.organos_cldc
    FOR EACH ROW
    EXECUTE FUNCTION public.update_updated_at_organos();

CREATE TRIGGER update_miembros_directivos_updated_at
    BEFORE UPDATE ON public.miembros_directivos
    FOR EACH ROW
    EXECUTE FUNCTION public.update_updated_at_organos();

CREATE TRIGGER update_asambleas_generales_updated_at
    BEFORE UPDATE ON public.asambleas_generales
    FOR EACH ROW
    EXECUTE FUNCTION public.update_updated_at_organos();

CREATE TRIGGER update_seccionales_updated_at
    BEFORE UPDATE ON public.seccionales
    FOR EACH ROW
    EXECUTE FUNCTION public.update_updated_at_organos();