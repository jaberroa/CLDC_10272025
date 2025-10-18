-- Fix RLS policies for customers table to properly protect personal information

-- First, drop existing policies to recreate them properly
DROP POLICY IF EXISTS "Admins can manage all customers" ON public.customers;
DROP POLICY IF EXISTS "Company users can view their customers" ON public.customers;

-- Create comprehensive RLS policies for customers table

-- 1. Admins have full access to all customer data
CREATE POLICY "admins_full_access_customers" 
ON public.customers 
FOR ALL 
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role = 'admin'::app_role
  )
)
WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role = 'admin'::app_role
  )
);

-- 2. Company users can view customers from their company only
CREATE POLICY "company_users_view_own_customers" 
ON public.customers 
FOR SELECT 
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 3. Company users can create customers for their company only
CREATE POLICY "company_users_create_own_customers" 
ON public.customers 
FOR INSERT 
TO authenticated
WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 4. Company users can update customers from their company only
CREATE POLICY "company_users_update_own_customers" 
ON public.customers 
FOR UPDATE 
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
)
WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 5. Company users can delete customers from their company only
CREATE POLICY "company_users_delete_own_customers" 
ON public.customers 
FOR DELETE 
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 6. Explicitly deny all access to unauthenticated users
CREATE POLICY "deny_anonymous_access_customers" 
ON public.customers 
FOR ALL 
TO anon
USING (false);

-- 7. Explicitly deny access to authenticated users without proper company association
CREATE POLICY "deny_unauthorized_company_access_customers" 
ON public.customers 
FOR ALL 
TO authenticated
USING (
  -- Only allow if user is admin or has company association
  EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND ur.role = 'admin'::app_role
  )
  OR 
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- Ensure RLS is enabled on customers table
ALTER TABLE public.customers ENABLE ROW LEVEL SECURITY;