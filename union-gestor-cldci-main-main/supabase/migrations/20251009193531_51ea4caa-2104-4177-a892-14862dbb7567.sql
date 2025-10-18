-- =====================================================
-- CORRECCIÓN: Remover SECURITY DEFINER de vistas
-- Las vistas no deben ser SECURITY DEFINER
-- =====================================================

-- Recrear vistas SIN security definer
DROP VIEW IF EXISTS public.miembros_seguros CASCADE;
DROP VIEW IF EXISTS public.seccionales_seguras CASCADE;
DROP VIEW IF EXISTS public.delivery_orders_seguras CASCADE;

-- Vista segura miembros (sin security definer)
CREATE VIEW public.miembros_seguros AS
SELECT 
  m.id, m.nombre_completo, m.profesion, m.estado_membresia, m.fecha_ingreso,
  m.organizacion_id, m.numero_carnet, m.foto_url, m.user_id, m.fecha_vencimiento,
  m.created_at, m.updated_at, m.observaciones,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN m.cedula
    WHEN has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) THEN mask_cedula(m.cedula)
    WHEN m.user_id = auth.uid() THEN m.cedula
    ELSE NULL
  END as cedula,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN m.email
    WHEN has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) THEN mask_email(m.email)
    WHEN m.user_id = auth.uid() THEN m.email
    ELSE NULL
  END as email,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN m.telefono
    WHEN has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) THEN mask_phone(m.telefono)
    WHEN m.user_id = auth.uid() THEN m.telefono
    ELSE NULL
  END as telefono,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN m.direccion
    WHEN has_role(auth.uid(), 'moderador'::app_role, m.organizacion_id) THEN mask_address(m.direccion)
    WHEN m.user_id = auth.uid() THEN m.direccion
    ELSE NULL
  END as direccion,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN m.fecha_nacimiento
    WHEN m.user_id = auth.uid() THEN m.fecha_nacimiento
    ELSE NULL
  END as fecha_nacimiento
FROM public.miembros m
WHERE 
  EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND (ur.organizacion_id = m.organizacion_id OR ur.role = 'admin'::app_role))
  OR m.user_id = auth.uid();

-- Vista segura seccionales
CREATE VIEW public.seccionales_seguras AS
SELECT 
  s.id, s.nombre, s.tipo, s.pais, s.provincia, s.ciudad, s.miembros_count,
  s.fecha_fundacion, s.estado, s.organizacion_id, s.coordinador_id, s.created_at, s.updated_at,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN s.email
    WHEN has_role(auth.uid(), 'moderador'::app_role, s.organizacion_id) THEN s.email
    WHEN EXISTS (SELECT 1 FROM miembros m WHERE m.id = s.coordinador_id AND m.user_id = auth.uid()) THEN s.email
    ELSE mask_email(s.email)
  END as email,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN s.telefono
    WHEN has_role(auth.uid(), 'moderador'::app_role, s.organizacion_id) THEN s.telefono
    WHEN EXISTS (SELECT 1 FROM miembros m WHERE m.id = s.coordinador_id AND m.user_id = auth.uid()) THEN s.telefono
    ELSE mask_phone(s.telefono)
  END as telefono,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN s.direccion
    WHEN has_role(auth.uid(), 'moderador'::app_role, s.organizacion_id) THEN s.direccion
    WHEN EXISTS (SELECT 1 FROM miembros m WHERE m.id = s.coordinador_id AND m.user_id = auth.uid()) THEN s.direccion
    ELSE mask_address(s.direccion)
  END as direccion
FROM public.seccionales s
WHERE EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = auth.uid() AND (ur.organizacion_id = s.organizacion_id OR ur.role = 'admin'::app_role));

-- Vista segura delivery orders
CREATE VIEW public.delivery_orders_seguras AS
SELECT 
  ord.id, ord.order_number, ord.company_id, ord.customer_id, ord.status, ord.priority,
  ord.notes, ord.created_at, ord.updated_at, ord.pickup_address, ord.pickup_lat, ord.pickup_lng,
  ord.preferred_time_start, ord.preferred_time_end, ord.weight, ord.volume,
  ord.requires_signature, ord.requires_pin, ord.pin_code, ord.tracking_token,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN ord.delivery_address
    WHEN ord.status IN ('in_transit', 'delivered') THEN ord.delivery_address
    ELSE '[Dirección oculta hasta asignación]'
  END as delivery_address,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN ord.delivery_lat
    WHEN ord.status IN ('in_transit', 'delivered') THEN ord.delivery_lat
    ELSE NULL
  END as delivery_lat,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN ord.delivery_lng
    WHEN ord.status IN ('in_transit', 'delivered') THEN ord.delivery_lng
    ELSE NULL
  END as delivery_lng,
  CASE 
    WHEN has_role(auth.uid(), 'admin'::app_role) THEN ord.delivery_instructions
    WHEN ord.status IN ('in_transit', 'delivered') THEN ord.delivery_instructions
    ELSE '[Instrucciones ocultas]'
  END as delivery_instructions
FROM public.delivery_orders ord;

COMMENT ON VIEW public.miembros_seguros IS 'Vista segura con enmascaramiento automático de PII';
COMMENT ON VIEW public.seccionales_seguras IS 'Vista segura con contactos enmascarados';
COMMENT ON VIEW public.delivery_orders_seguras IS 'Vista segura con direcciones ocultas hasta asignación';