-- Fix security vulnerability in seccional_submissions table
-- Remove public policies and implement proper role-based access control

-- Drop existing public policies
DROP POLICY IF EXISTS "Public can insert submissions for testing" ON public.seccional_submissions;
DROP POLICY IF EXISTS "Public can view submissions for testing" ON public.seccional_submissions;

-- Create secure RLS policies

-- Admins can manage all submissions
CREATE POLICY "Admins can manage all submissions"
ON public.seccional_submissions
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can view all submissions but not modify
CREATE POLICY "Moderators can view all submissions"
ON public.seccional_submissions
FOR SELECT
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role));

-- Users can view their own submissions
CREATE POLICY "Users can view own submissions"
ON public.seccional_submissions
FOR SELECT
TO authenticated
USING (auth.uid() = created_by);

-- Authenticated users can insert submissions (with their own ID)
CREATE POLICY "Users can create submissions"
ON public.seccional_submissions
FOR INSERT
TO authenticated
WITH CHECK (auth.uid() = created_by);

-- Update the created_by column to be NOT NULL since it's critical for security
ALTER TABLE public.seccional_submissions 
ALTER COLUMN created_by SET NOT NULL;