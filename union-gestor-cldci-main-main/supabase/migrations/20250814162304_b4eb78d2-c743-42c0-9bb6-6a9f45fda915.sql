-- Fix RLS policies for customers table to properly protect personal information
-- First check and drop ALL existing policies

DROP POLICY IF EXISTS "admins_full_access_customers" ON public.customers;
DROP POLICY IF EXISTS "company_users_view_own_customers" ON public.customers;
DROP POLICY IF EXISTS "company_users_create_own_customers" ON public.customers;
DROP POLICY IF EXISTS "company_users_update_own_customers" ON public.customers;
DROP POLICY IF EXISTS "company_users_delete_own_customers" ON public.customers;
DROP POLICY IF EXISTS "deny_anonymous_access_customers" ON public.customers;
DROP POLICY IF EXISTS "deny_unauthorized_company_access_customers" ON public.customers;

-- Now create comprehensive RLS policies

-- 1. Admins have full access
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
);

-- 2. Company users can only access customers from their company
CREATE POLICY "company_users_access_own_customers" 
ON public.customers 
FOR ALL 
TO authenticated
USING (
  EXISTS (
    SELECT 1 FROM public.drivers d 
    WHERE d.company_id = customers.company_id 
    AND d.user_id = auth.uid()
  )
);

-- 3. Deny all access to unauthenticated users
CREATE POLICY "deny_anonymous_access_customers" 
ON public.customers 
FOR ALL 
TO anon
USING (false);

-- Ensure RLS is enabled
ALTER TABLE public.customers ENABLE ROW LEVEL SECURITY;