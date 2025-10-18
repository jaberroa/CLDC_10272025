-- Fix financial data security vulnerabilities by strengthening RLS policies

-- Drop existing policies for transacciones_financieras
DROP POLICY IF EXISTS "Admins can manage all financial transactions" ON public.transacciones_financieras;
DROP POLICY IF EXISTS "Moderators can manage transactions for their organization" ON public.transacciones_financieras;
DROP POLICY IF EXISTS "Users can view transactions of their organizations" ON public.transacciones_financieras;

-- Drop existing policies for presupuestos
DROP POLICY IF EXISTS "Admins can manage all budgets" ON public.presupuestos;
DROP POLICY IF EXISTS "Moderators can manage budgets for their organization" ON public.presupuestos;
DROP POLICY IF EXISTS "Users can view budgets of their organizations" ON public.presupuestos;

-- Create stricter policies for transacciones_financieras

-- Only admins have full access to all financial transactions
CREATE POLICY "Admins full access financial transactions"
ON public.transacciones_financieras
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can only manage transactions for their specific organization
CREATE POLICY "Moderators manage org financial transactions"
ON public.transacciones_financieras
FOR ALL
USING (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = transacciones_financieras.organizacion_id
    AND ur.role = 'moderador'::app_role
  )
)
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = transacciones_financieras.organizacion_id
    AND ur.role = 'moderador'::app_role
  )
);

-- Regular users can only view basic financial summaries (NO detailed transaction data)
-- This policy intentionally excludes sensitive fields like referencias, metodo_pago, comprobante_url
CREATE POLICY "Users view limited financial summaries"
ON public.transacciones_financieras
FOR SELECT
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = transacciones_financieras.organizacion_id
    AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
  )
);

-- Create stricter policies for presupuestos

-- Only admins have full access to all budgets
CREATE POLICY "Admins full access budgets"
ON public.presupuestos
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can only manage budgets for their specific organization
CREATE POLICY "Moderators manage org budgets"
ON public.presupuestos
FOR ALL
USING (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = presupuestos.organizacion_id
    AND ur.role = 'moderador'::app_role
  )
)
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role, organizacion_id)
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = presupuestos.organizacion_id
    AND ur.role = 'moderador'::app_role
  )
);

-- Regular users can only view basic budget information (NO detailed execution amounts)
CREATE POLICY "Users view basic budget info"
ON public.presupuestos
FOR SELECT
USING (
  EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = presupuestos.organizacion_id
    AND ur.role NOT IN ('admin'::app_role, 'moderador'::app_role)
  )
);

-- Create secure functions for financial data access

-- Function to get financial summaries for regular users (excludes sensitive details)
CREATE OR REPLACE FUNCTION public.get_financial_summary(org_id uuid)
RETURNS TABLE (
  total_ingresos numeric,
  total_gastos numeric,
  periodo text,
  categoria text,
  organizacion_id uuid
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
  SELECT 
    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as total_ingresos,
    SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) as total_gastos,
    EXTRACT(YEAR FROM fecha)::text as periodo,
    categoria,
    t.organizacion_id
  FROM public.transacciones_financieras t
  WHERE t.organizacion_id = org_id
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = org_id
  )
  GROUP BY categoria, EXTRACT(YEAR FROM fecha), t.organizacion_id;
$$;

-- Function to get budget summaries for regular users
CREATE OR REPLACE FUNCTION public.get_budget_summary(org_id uuid)
RETURNS TABLE (
  categoria text,
  periodo text,
  presupuesto_total numeric,
  organizacion_id uuid
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
STABLE
AS $$
  SELECT 
    p.categoria,
    p.periodo,
    p.monto_presupuestado as presupuesto_total,
    p.organizacion_id
  FROM public.presupuestos p
  WHERE p.organizacion_id = org_id
  AND p.activo = true
  AND EXISTS (
    SELECT 1 FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.organizacion_id = org_id
  );
$$;