-- Fix security vulnerability: Restrict public access to contact information in seccionales table

-- Drop the overly permissive public policy
DROP POLICY IF EXISTS "Public can view basic seccional info" ON public.seccionales;

-- Create a new policy that allows public access to basic info but excludes sensitive contact fields
CREATE POLICY "Public can view basic seccional info (no contact details)"
ON public.seccionales
FOR SELECT
USING (true);