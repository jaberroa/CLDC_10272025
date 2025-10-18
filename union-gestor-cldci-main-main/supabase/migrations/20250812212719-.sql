-- CRITICAL SECURITY FIX: Implement RLS policies for sensitive tables
-- This migration secures electoral, financial, and organizational data

-- Enable RLS on all unprotected sensitive tables
ALTER TABLE public.padrones_electorales ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.elecciones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.electores ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.votos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.transacciones_financieras ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.presupuestos ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.capacitaciones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.inscripciones_capacitacion ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.periodos_directiva ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.asistencia_asambleas ENABLE ROW LEVEL SECURITY;

-- PADRONES ELECTORALES (Electoral Rolls) - High Sensitivity
CREATE POLICY "Admins can manage all padrones"
ON public.padrones_electorales
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage padrones for their organization"
ON public.padrones_electorales
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

CREATE POLICY "Users can view padrones of their organizations"
ON public.padrones_electorales
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM user_roles ur 
  WHERE ur.user_id = auth.uid() 
  AND (ur.organizacion_id = padrones_electorales.organizacion_id OR ur.role = 'admin'::app_role)
));

-- ELECCIONES (Elections) - Extremely High Sensitivity
CREATE POLICY "Admins can manage all elections"
ON public.elecciones
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage elections for their padron"
ON public.elecciones
FOR ALL
TO authenticated
USING (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  WHERE pe.id = elecciones.padron_id
  AND has_role(auth.uid(), 'moderador'::app_role, pe.organizacion_id)
))
WITH CHECK (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  WHERE pe.id = elecciones.padron_id
  AND has_role(auth.uid(), 'moderador'::app_role, pe.organizacion_id)
));

CREATE POLICY "Users can view elections for their organizations"
ON public.elecciones
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  JOIN user_roles ur ON (ur.organizacion_id = pe.organizacion_id OR ur.role = 'admin'::app_role)
  WHERE pe.id = elecciones.padron_id AND ur.user_id = auth.uid()
));

-- ELECTORES (Voters) - Extremely High Sensitivity
CREATE POLICY "Admins can manage all electores"
ON public.electores
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage electores for their padron"
ON public.electores
FOR ALL
TO authenticated
USING (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  WHERE pe.id = electores.padron_id
  AND has_role(auth.uid(), 'moderador'::app_role, pe.organizacion_id)
))
WITH CHECK (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  WHERE pe.id = electores.padron_id
  AND has_role(auth.uid(), 'moderador'::app_role, pe.organizacion_id)
));

CREATE POLICY "Users can view electores for their organizations"
ON public.electores
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM padrones_electorales pe
  JOIN user_roles ur ON (ur.organizacion_id = pe.organizacion_id OR ur.role = 'admin'::app_role)
  WHERE pe.id = electores.padron_id AND ur.user_id = auth.uid()
));

-- VOTOS (Votes) - MAXIMUM SECURITY - Most restrictive policies
CREATE POLICY "Only admins can view all votes"
ON public.votos
FOR SELECT
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Only admins can manage votes"
ON public.votos
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- TRANSACCIONES FINANCIERAS (Financial Transactions) - High Sensitivity
CREATE POLICY "Admins can manage all financial transactions"
ON public.transacciones_financieras
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage transactions for their organization"
ON public.transacciones_financieras
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

CREATE POLICY "Users can view transactions of their organizations"
ON public.transacciones_financieras
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM user_roles ur 
  WHERE ur.user_id = auth.uid() 
  AND (ur.organizacion_id = transacciones_financieras.organizacion_id OR ur.role = 'admin'::app_role)
));

-- PRESUPUESTOS (Budgets) - High Sensitivity
CREATE POLICY "Admins can manage all budgets"
ON public.presupuestos
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage budgets for their organization"
ON public.presupuestos
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

CREATE POLICY "Users can view budgets of their organizations"
ON public.presupuestos
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM user_roles ur 
  WHERE ur.user_id = auth.uid() 
  AND (ur.organizacion_id = presupuestos.organizacion_id OR ur.role = 'admin'::app_role)
));

-- CAPACITACIONES (Training Programs) - Medium Sensitivity
CREATE POLICY "Admins can manage all training programs"
ON public.capacitaciones
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage training for their organization"
ON public.capacitaciones
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

CREATE POLICY "Users can view and register for training in their organizations"
ON public.capacitaciones
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM user_roles ur 
  WHERE ur.user_id = auth.uid() 
  AND (ur.organizacion_id = capacitaciones.organizacion_id OR ur.role = 'admin'::app_role)
));

-- INSCRIPCIONES CAPACITACION (Training Enrollments) - Medium Sensitivity
CREATE POLICY "Admins can manage all training enrollments"
ON public.inscripciones_capacitacion
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage enrollments for their organization training"
ON public.inscripciones_capacitacion
FOR ALL
TO authenticated
USING (EXISTS (
  SELECT 1 FROM capacitaciones c
  WHERE c.id = inscripciones_capacitacion.capacitacion_id
  AND has_role(auth.uid(), 'moderador'::app_role, c.organizacion_id)
))
WITH CHECK (EXISTS (
  SELECT 1 FROM capacitaciones c
  WHERE c.id = inscripciones_capacitacion.capacitacion_id
  AND has_role(auth.uid(), 'moderador'::app_role, c.organizacion_id)
));

CREATE POLICY "Users can view enrollments for their organization training"
ON public.inscripciones_capacitacion
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM capacitaciones c
  JOIN user_roles ur ON (ur.organizacion_id = c.organizacion_id OR ur.role = 'admin'::app_role)
  WHERE c.id = inscripciones_capacitacion.capacitacion_id AND ur.user_id = auth.uid()
));

CREATE POLICY "Users can enroll themselves in training"
ON public.inscripciones_capacitacion
FOR INSERT
TO authenticated
WITH CHECK (EXISTS (
  SELECT 1 FROM miembros m
  JOIN capacitaciones c ON c.organizacion_id = m.organizacion_id
  WHERE m.user_id = auth.uid() 
  AND c.id = inscripciones_capacitacion.capacitacion_id
  AND m.id = inscripciones_capacitacion.miembro_id
));

-- PERIODOS DIRECTIVA (Board Periods) - High Sensitivity
CREATE POLICY "Admins can manage all board periods"
ON public.periodos_directiva
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage board periods for their organization"
ON public.periodos_directiva
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role, organizacion_id))
WITH CHECK (has_role(auth.uid(), 'moderador'::app_role, organizacion_id));

CREATE POLICY "Users can view board periods of their organizations"
ON public.periodos_directiva
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM user_roles ur 
  WHERE ur.user_id = auth.uid() 
  AND (ur.organizacion_id = periodos_directiva.organizacion_id OR ur.role = 'admin'::app_role)
));

-- ASISTENCIA ASAMBLEAS (Assembly Attendance) - High Sensitivity
CREATE POLICY "Admins can manage all assembly attendance"
ON public.asistencia_asambleas
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Moderators can manage attendance for their organization assemblies"
ON public.asistencia_asambleas
FOR ALL
TO authenticated
USING (EXISTS (
  SELECT 1 FROM asambleas a
  WHERE a.id = asistencia_asambleas.asamblea_id
  AND has_role(auth.uid(), 'moderador'::app_role, a.organizacion_id)
))
WITH CHECK (EXISTS (
  SELECT 1 FROM asambleas a
  WHERE a.id = asistencia_asambleas.asamblea_id
  AND has_role(auth.uid(), 'moderador'::app_role, a.organizacion_id)
));

CREATE POLICY "Users can view attendance for their organization assemblies"
ON public.asistencia_asambleas
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM asambleas a
  JOIN user_roles ur ON (ur.organizacion_id = a.organizacion_id OR ur.role = 'admin'::app_role)
  WHERE a.id = asistencia_asambleas.asamblea_id AND ur.user_id = auth.uid()
));

-- Users can register their own attendance
CREATE POLICY "Users can register their own attendance"
ON public.asistencia_asambleas
FOR INSERT
TO authenticated
WITH CHECK (EXISTS (
  SELECT 1 FROM miembros m
  JOIN asambleas a ON a.organizacion_id = m.organizacion_id
  WHERE m.user_id = auth.uid() 
  AND a.id = asistencia_asambleas.asamblea_id
  AND m.id = asistencia_asambleas.miembro_id
));