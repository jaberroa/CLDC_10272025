-- Add a missing RLS policy to completely prevent unauthorized access to miembros table
-- This will block the direct queries happening in Index.tsx and Reportes.tsx

-- Create a policy that blocks regular users from seeing ANY member data other than their own
CREATE POLICY "Block unauthorized member access"
ON public.miembros
FOR SELECT
USING (
  -- Only allow if user is admin, moderator, or viewing their own record
  has_role(auth.uid(), 'admin'::app_role)
  OR has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  OR user_id = auth.uid()
);

-- Create secure functions for the dashboard statistics that don't expose sensitive data
CREATE OR REPLACE FUNCTION public.get_dashboard_stats()
RETURNS TABLE (
  total_miembros_activos bigint,
  total_organizaciones bigint
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
  SELECT 
    (SELECT COUNT(*) FROM public.miembros WHERE estado_membresia = 'activa') as total_miembros_activos,
    (SELECT COUNT(*) FROM public.organizaciones) as total_organizaciones;
$$;

-- Create secure function for report data that respects user permissions
CREATE OR REPLACE FUNCTION public.get_member_stats_by_province(requesting_user_id uuid)
RETURNS TABLE (
  provincia text,
  member_count bigint,
  active_count bigint
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
  SELECT 
    o.provincia,
    COUNT(m.id) as member_count,
    COUNT(CASE WHEN m.estado_membresia = 'activa' THEN 1 END) as active_count
  FROM public.miembros m
  JOIN public.organizaciones o ON m.organizacion_id = o.id
  WHERE 
    -- Only include data if user has proper access
    (
      -- Admin can see all
      EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = requesting_user_id AND ur.role = 'admin'::app_role)
      OR
      -- Moderator can only see their organization's data  
      EXISTS (
        SELECT 1 FROM user_roles ur 
        WHERE ur.user_id = requesting_user_id 
        AND ur.role = 'moderador'::app_role 
        AND ur.organizacion_id = m.organizacion_id
      )
      OR
      -- Regular users can only see aggregated stats for their own organization
      (
        EXISTS (
          SELECT 1 FROM user_roles ur 
          WHERE ur.user_id = requesting_user_id 
          AND ur.organizacion_id = m.organizacion_id
          AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
        )
        AND o.provincia IS NOT NULL  -- Only show aggregated data, no individual records
      )
    )
  GROUP BY o.provincia
  HAVING COUNT(m.id) >= 5;  -- Only show provinces with 5+ members for privacy
$$;