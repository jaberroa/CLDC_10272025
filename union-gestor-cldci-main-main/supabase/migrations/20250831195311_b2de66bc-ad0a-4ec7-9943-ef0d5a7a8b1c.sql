-- Fix RLS policy to prevent unauthorized access to sensitive contact information

-- Drop the previous policy that I just created (it still allows access to sensitive fields)
DROP POLICY IF EXISTS "Public can view basic seccional info (no contact details)" ON public.seccionales;

-- Create a new policy that requires authentication for any direct table access
-- This forces all public access to go through the secure get_public_seccionales function
CREATE POLICY "Authenticated users can view basic seccional info"
ON public.seccionales
FOR SELECT
TO authenticated
USING (true);

-- Create a policy for anonymous users to access only through RPC functions
-- This allows the get_public_seccionales function to work for anonymous users
-- while preventing direct table access
CREATE POLICY "Anonymous access via secure functions only"
ON public.seccionales
FOR SELECT 
TO anon
USING (false);  -- This blocks direct access, forcing use of SECURITY DEFINER functions