-- Crear enum para roles de usuario
CREATE TYPE public.app_role AS ENUM ('admin', 'moderador', 'miembro');

-- Crear enum para estados de membresía
CREATE TYPE public.estado_membresia AS ENUM ('activa', 'vencida', 'pendiente', 'suspendida');

-- Crear enum para tipos de organización
CREATE TYPE public.tipo_organizacion AS ENUM ('filial', 'seccional', 'delegacion');

-- Crear enum para estados de adecuación
CREATE TYPE public.estado_adecuacion AS ENUM ('pendiente', 'en_revision', 'aprobada', 'rechazada');

-- Crear tabla de perfiles de usuario
CREATE TABLE public.profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    email TEXT NOT NULL,
    nombre_completo TEXT NOT NULL,
    telefono TEXT,
    avatar_url TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de roles de usuario
CREATE TABLE public.user_roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
    role app_role NOT NULL,
    organizacion_id UUID,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    UNIQUE (user_id, role, organizacion_id)
);

-- Crear tabla de organizaciones (filiales, seccionales, delegaciones)
CREATE TABLE public.organizaciones (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nombre TEXT NOT NULL,
    tipo tipo_organizacion NOT NULL,
    codigo TEXT UNIQUE NOT NULL,
    pais TEXT,
    provincia TEXT,
    ciudad TEXT,
    direccion TEXT,
    telefono TEXT,
    email TEXT,
    organizacion_padre_id UUID REFERENCES public.organizaciones(id),
    fecha_fundacion DATE,
    estado_adecuacion estado_adecuacion DEFAULT 'pendiente',
    estatutos_url TEXT,
    actas_fundacion_url TEXT,
    miembros_minimos INTEGER DEFAULT 15,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de períodos de directiva
CREATE TABLE public.periodos_directiva (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    directiva JSONB, -- {presidente: "nombre", secretario: "nombre", etc}
    acta_eleccion_url TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de miembros
CREATE TABLE public.miembros (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    numero_carnet TEXT UNIQUE NOT NULL,
    nombre_completo TEXT NOT NULL,
    cedula TEXT,
    email TEXT,
    telefono TEXT,
    direccion TEXT,
    fecha_nacimiento DATE,
    profesion TEXT,
    estado_membresia estado_membresia DEFAULT 'pendiente',
    fecha_ingreso DATE DEFAULT CURRENT_DATE,
    fecha_vencimiento DATE,
    foto_url TEXT,
    observaciones TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de asambleas
CREATE TABLE public.asambleas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    tipo TEXT NOT NULL, -- 'ordinaria', 'extraordinaria'
    titulo TEXT NOT NULL,
    descripcion TEXT,
    fecha_convocatoria TIMESTAMP WITH TIME ZONE NOT NULL,
    fecha_asamblea TIMESTAMP WITH TIME ZONE NOT NULL,
    lugar TEXT,
    modalidad TEXT DEFAULT 'presencial', -- 'presencial', 'virtual', 'mixta'
    enlace_virtual TEXT,
    quorum_minimo INTEGER NOT NULL,
    convocatoria_url TEXT,
    acta_url TEXT,
    estado TEXT DEFAULT 'convocada', -- 'convocada', 'realizada', 'cancelada'
    asistentes_count INTEGER DEFAULT 0,
    quorum_alcanzado BOOLEAN DEFAULT false,
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de asistencia a asambleas
CREATE TABLE public.asistencia_asambleas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    asamblea_id UUID REFERENCES public.asambleas(id) ON DELETE CASCADE NOT NULL,
    miembro_id UUID REFERENCES public.miembros(id) ON DELETE CASCADE NOT NULL,
    presente BOOLEAN NOT NULL,
    modalidad TEXT, -- 'presencial', 'virtual'
    hora_registro TIMESTAMP WITH TIME ZONE DEFAULT now(),
    UNIQUE (asamblea_id, miembro_id)
);

-- Crear tabla de padrones electorales
CREATE TABLE public.padrones_electorales (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    periodo TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT true,
    total_electores INTEGER DEFAULT 0,
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de electores
CREATE TABLE public.electores (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    padron_id UUID REFERENCES public.padrones_electorales(id) ON DELETE CASCADE NOT NULL,
    miembro_id UUID REFERENCES public.miembros(id) ON DELETE CASCADE NOT NULL,
    elegible BOOLEAN DEFAULT true,
    observaciones TEXT,
    UNIQUE (padron_id, miembro_id)
);

-- Crear tabla de elecciones
CREATE TABLE public.elecciones (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    padron_id UUID REFERENCES public.padrones_electorales(id) ON DELETE CASCADE NOT NULL,
    cargo TEXT NOT NULL,
    candidatos JSONB NOT NULL, -- [{id: uuid, nombre: string, propuesta: string}]
    fecha_inicio TIMESTAMP WITH TIME ZONE NOT NULL,
    fecha_fin TIMESTAMP WITH TIME ZONE NOT NULL,
    modalidad TEXT DEFAULT 'presencial', -- 'presencial', 'virtual', 'mixta'
    estado TEXT DEFAULT 'programada', -- 'programada', 'activa', 'finalizada'
    votos_totales INTEGER DEFAULT 0,
    resultados JSONB,
    auditoria_hash TEXT,
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de votos
CREATE TABLE public.votos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    eleccion_id UUID REFERENCES public.elecciones(id) ON DELETE CASCADE NOT NULL,
    elector_id UUID REFERENCES public.electores(id) ON DELETE CASCADE NOT NULL,
    candidato_id UUID,
    voto_hash TEXT NOT NULL,
    timestamp_voto TIMESTAMP WITH TIME ZONE DEFAULT now(),
    modalidad TEXT, -- 'presencial', 'virtual'
    verificado BOOLEAN DEFAULT false,
    UNIQUE (eleccion_id, elector_id)
);

-- Crear tabla de finanzas
CREATE TABLE public.transacciones_financieras (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    tipo TEXT NOT NULL, -- 'ingreso', 'gasto'
    categoria TEXT NOT NULL, -- 'cuotas', 'eventos', 'patrocinios', 'operativo', etc
    concepto TEXT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha DATE NOT NULL,
    comprobante_url TEXT,
    metodo_pago TEXT, -- 'efectivo', 'transferencia', 'tarjeta', etc
    referencia TEXT,
    aprobado_por UUID REFERENCES auth.users(id),
    observaciones TEXT,
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de presupuestos
CREATE TABLE public.presupuestos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE NOT NULL,
    periodo TEXT NOT NULL, -- '2024', '2024-Q1', etc
    categoria TEXT NOT NULL,
    monto_presupuestado DECIMAL(12,2) NOT NULL,
    monto_ejecutado DECIMAL(12,2) DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    UNIQUE (organizacion_id, periodo, categoria)
);

-- Crear tabla de capacitaciones
CREATE TABLE public.capacitaciones (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organizacion_id UUID REFERENCES public.organizaciones(id) ON DELETE CASCADE,
    titulo TEXT NOT NULL,
    descripcion TEXT,
    tipo TEXT NOT NULL, -- 'curso', 'taller', 'conferencia', 'seminario'
    modalidad TEXT DEFAULT 'presencial', -- 'presencial', 'virtual', 'mixta'
    fecha_inicio TIMESTAMP WITH TIME ZONE NOT NULL,
    fecha_fin TIMESTAMP WITH TIME ZONE,
    lugar TEXT,
    enlace_virtual TEXT,
    capacidad_maxima INTEGER,
    costo DECIMAL(10,2) DEFAULT 0,
    certificado_template_url TEXT,
    estado TEXT DEFAULT 'programada', -- 'programada', 'activa', 'finalizada', 'cancelada'
    created_by UUID REFERENCES auth.users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Crear tabla de inscripciones a capacitaciones
CREATE TABLE public.inscripciones_capacitacion (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    capacitacion_id UUID REFERENCES public.capacitaciones(id) ON DELETE CASCADE NOT NULL,
    miembro_id UUID REFERENCES public.miembros(id) ON DELETE CASCADE NOT NULL,
    fecha_inscripcion TIMESTAMP WITH TIME ZONE DEFAULT now(),
    asistio BOOLEAN DEFAULT false,
    calificacion DECIMAL(3,1),
    certificado_url TEXT,
    observaciones TEXT,
    UNIQUE (capacitacion_id, miembro_id)
);

-- Habilitar RLS en todas las tablas
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.user_roles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.organizaciones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.periodos_directiva ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.miembros ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.asambleas ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.asistencia_asambleas ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.padrones_electorales ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.electores ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.elecciones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.votos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.transacciones_financieras ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.presupuestos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.capacitaciones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.inscripciones_capacitacion ENABLE ROW LEVEL SECURITY;

-- Función para verificar roles de usuario
CREATE OR REPLACE FUNCTION public.has_role(_user_id uuid, _role app_role, _org_id uuid DEFAULT NULL)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.user_roles
    WHERE user_id = _user_id
      AND role = _role
      AND (organizacion_id = _org_id OR organizacion_id IS NULL OR _org_id IS NULL)
  )
$$;

-- Función para obtener organizaciones del usuario
CREATE OR REPLACE FUNCTION public.user_organizations(_user_id uuid)
RETURNS TABLE(org_id uuid, role app_role)
LANGUAGE sql
STABLE
SECURITY DEFINER
AS $$
  SELECT organizacion_id, role
  FROM public.user_roles
  WHERE user_id = _user_id
$$;

-- Trigger para actualizar timestamps
CREATE OR REPLACE FUNCTION public.update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Aplicar triggers a las tablas relevantes
CREATE TRIGGER update_profiles_updated_at BEFORE UPDATE ON public.profiles FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
CREATE TRIGGER update_organizaciones_updated_at BEFORE UPDATE ON public.organizaciones FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
CREATE TRIGGER update_miembros_updated_at BEFORE UPDATE ON public.miembros FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
CREATE TRIGGER update_asambleas_updated_at BEFORE UPDATE ON public.asambleas FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();

-- Políticas RLS básicas para profiles
CREATE POLICY "Users can view own profile" ON public.profiles FOR SELECT USING (auth.uid() = id);
CREATE POLICY "Users can update own profile" ON public.profiles FOR UPDATE USING (auth.uid() = id);
CREATE POLICY "Users can insert own profile" ON public.profiles FOR INSERT WITH CHECK (auth.uid() = id);

-- Políticas RLS para organizaciones (lectura pública, escritura para admins)
CREATE POLICY "Public can view organizations" ON public.organizaciones FOR SELECT USING (true);
CREATE POLICY "Admins can manage organizations" ON public.organizaciones FOR ALL USING (public.has_role(auth.uid(), 'admin'));

-- Políticas RLS para user_roles
CREATE POLICY "Users can view own roles" ON public.user_roles FOR SELECT USING (auth.uid() = user_id);
CREATE POLICY "Admins can manage roles" ON public.user_roles FOR ALL USING (public.has_role(auth.uid(), 'admin'));

-- Políticas RLS para miembros
CREATE POLICY "Users can view members of their organizations" ON public.miembros FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = miembros.organizacion_id OR ur.role = 'admin')
  )
);
CREATE POLICY "Admins and moderators can manage members" ON public.miembros FOR ALL USING (
  public.has_role(auth.uid(), 'admin') OR 
  public.has_role(auth.uid(), 'moderador', organizacion_id)
);

-- Políticas similares para otras tablas
CREATE POLICY "Users can view asambleas of their organizations" ON public.asambleas FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = asambleas.organizacion_id OR ur.role = 'admin')
  )
);
CREATE POLICY "Admins and moderators can manage asambleas" ON public.asambleas FOR ALL USING (
  public.has_role(auth.uid(), 'admin') OR 
  public.has_role(auth.uid(), 'moderador', organizacion_id)
);

-- Insertar datos de prueba para organizaciones
INSERT INTO public.organizaciones (nombre, tipo, codigo, pais, provincia, ciudad, miembros_minimos, estado_adecuacion) VALUES
('CLDCI Distrito Nacional', 'filial', 'CLDCI-DN', 'República Dominicana', 'Distrito Nacional', 'Santo Domingo', 25, 'aprobada'),
('CLDCI Santiago', 'seccional', 'CLDCI-STI', 'República Dominicana', 'Santiago', 'Santiago de los Caballeros', 15, 'aprobada'),
('CLDCI San Cristóbal', 'seccional', 'CLDCI-SC', 'República Dominicana', 'San Cristóbal', 'San Cristóbal', 15, 'en_revision'),
('CLDCI La Vega', 'seccional', 'CLDCI-LV', 'República Dominicana', 'La Vega', 'La Vega', 15, 'pendiente'),
('CLDCI Nueva York', 'delegacion', 'CLDCI-NY', 'Estados Unidos', 'New York', 'Nueva York', 10, 'aprobada');

-- Función para handle de nuevo usuario (trigger en auth.users)
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER SET search_path = ''
AS $$
begin
  INSERT INTO public.profiles (id, email, nombre_completo)
  VALUES (
    new.id, 
    new.email, 
    COALESCE(new.raw_user_meta_data ->> 'full_name', new.email)
  );
  RETURN new;
end;
$$;

-- Trigger para crear perfil automáticamente
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();