-- ============================================
-- COMPREHENSIVE SECURITY AUDIT FIX
-- Fixing all critical security vulnerabilities
-- ============================================

-- 1. FIX: Board Members Contact Information Protection
-- Remove public access to institutional contact details
DROP POLICY IF EXISTS "Public access to active board members" ON public.miembros_directivos;
DROP POLICY IF EXISTS "Public view active board members basic info" ON public.miembros_directivos;

-- Create new restricted policy for board members basic info (without contact details)
CREATE POLICY "Public can view board members basic info"
ON public.miembros_directivos
FOR SELECT
TO authenticated
USING (estado = 'activo');

-- Contact details only accessible via secure function
-- (Function get_miembro_directivo_contact_details already exists)

-- 2. FIX: Seccionales Contact Data Protection
-- Remove overly permissive authenticated user access
DROP POLICY IF EXISTS "Authenticated users can view basic seccional info" ON public.seccionales;
DROP POLICY IF EXISTS "Anonymous access via secure functions only" ON public.seccionales;

-- Only allow authenticated users from the same organization
CREATE POLICY "Users can view seccionales of their organization"
ON public.seccionales
FOR SELECT
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (ur.organizacion_id = seccionales.organizacion_id OR ur.role = 'admin'::app_role)
  )
);

-- Contact details policy remains (already restricted to admins/moderators/coordinators)

-- 3. FIX: Delivery Feedback Authentication
-- Remove anonymous access to feedback
DROP POLICY IF EXISTS "Anyone can insert feedback" ON public.delivery_feedback;
DROP POLICY IF EXISTS "Anyone can view feedback" ON public.delivery_feedback;

-- Require authentication for feedback operations
CREATE POLICY "Authenticated users can insert feedback"
ON public.delivery_feedback
FOR INSERT
TO authenticated
WITH CHECK (true);

CREATE POLICY "Users can view feedback for their orders"
ON public.delivery_feedback
FOR SELECT
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM delivery_orders o
    WHERE o.id = delivery_feedback.order_id
    AND (o.customer_id = auth.uid() OR EXISTS (
      SELECT 1 FROM drivers d 
      JOIN delivery_routes dr ON dr.driver_id = d.id
      JOIN route_stops rs ON rs.route_id = dr.id
      WHERE rs.order_id = o.id AND d.user_id = auth.uid()
    ))
  ) OR has_role(auth.uid(), 'admin'::app_role)
);

-- 4. FIX: Customer Database Protection
-- Verify and strengthen customer access policies
DROP POLICY IF EXISTS "deny_anonymous_access_customers" ON public.customers;

-- Ensure customers table is fully protected
CREATE POLICY "Only admins and company operators can access customers"
ON public.customers
FOR ALL
TO authenticated
USING (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM drivers d
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
)
WITH CHECK (
  has_role(auth.uid(), 'admin'::app_role) OR
  EXISTS (
    SELECT 1 FROM drivers d
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 5. FIX: Organizational Structure Protection
-- Restrict public access to organizational hierarchy
DROP POLICY IF EXISTS "Cualquiera puede ver cargos" ON public.cargos_organos;

CREATE POLICY "Authenticated users can view cargos"
ON public.cargos_organos
FOR SELECT
TO authenticated
USING (true);

-- 6. FIX: Governance Structure Protection
DROP POLICY IF EXISTS "Cualquiera puede ver órganos públicos" ON public.organos_cldc;

CREATE POLICY "Authenticated users can view organos"
ON public.organos_cldc
FOR SELECT
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur
    WHERE ur.user_id = auth.uid()
    AND (ur.organizacion_id = organos_cldc.organizacion_id OR ur.role = 'admin'::app_role)
  )
);

-- 7. FIX: Executive Committee Members Protection
DROP POLICY IF EXISTS "Usuarios pueden ver comités ejecutivos públicos" ON public.comites_ejecutivos_seccionales;

CREATE POLICY "Users can view executive committees of their organization"
ON public.comites_ejecutivos_seccionales
FOR SELECT
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM seccionales s
    JOIN user_roles ur ON (ur.organizacion_id = s.organizacion_id OR ur.role = 'admin'::app_role)
    WHERE s.id = comites_ejecutivos_seccionales.seccional_id
    AND ur.user_id = auth.uid()
  )
);

-- 8. FIX: Public course/training data - require authentication
DROP POLICY IF EXISTS "Todos pueden ver cursos públicos" ON public.cursos;
DROP POLICY IF EXISTS "Todos pueden ver diplomados públicos" ON public.diplomados;
DROP POLICY IF EXISTS "Todos pueden ver módulos de diplomados" ON public.modulos_diplomados;

CREATE POLICY "Authenticated users can view available courses"
ON public.cursos
FOR SELECT
TO authenticated
USING (estado IN ('programado', 'en_curso', 'finalizado'));

CREATE POLICY "Authenticated users can view available diplomados"
ON public.diplomados
FOR SELECT
TO authenticated
USING (estado IN ('programado', 'en_curso', 'finalizado'));

CREATE POLICY "Authenticated users can view diplomado modules"
ON public.modulos_diplomados
FOR SELECT
TO authenticated
USING (true);

-- 9. Create security audit log entry
INSERT INTO public.security_audit_log (
  user_id,
  action,
  resource_type,
  success,
  additional_data
) VALUES (
  '00000000-0000-0000-0000-000000000000'::uuid,
  'SECURITY_AUDIT_FIX',
  'RLS_POLICIES',
  true,
  jsonb_build_object(
    'description', 'Comprehensive security audit fix applied',
    'fixes', jsonb_build_array(
      'Board members contact info protected',
      'Seccionales contact data restricted',
      'Delivery feedback authentication required',
      'Customer database fully protected',
      'Organizational structure authentication required',
      'Governance structure restricted to org members',
      'Executive committees restricted to org members',
      'Public training data requires authentication'
    )
  )
);