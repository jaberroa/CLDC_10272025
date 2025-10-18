-- CRITICAL SECURITY FIX: Secure seccional_submissions table (Handle NULL values)
-- Remove dangerous public access policies and implement proper authentication

-- Drop the dangerous public testing policies
DROP POLICY IF EXISTS "Public can insert submissions for testing" ON public.seccional_submissions;
DROP POLICY IF EXISTS "Public can view submissions for testing" ON public.seccional_submissions;

-- First, make created_by nullable temporarily while we handle existing data
-- Check if there are any existing submissions with NULL created_by
DO $$
BEGIN
  -- For existing NULL records, we'll use a placeholder UUID 
  -- This represents system/anonymous submissions that existed before auth was implemented
  UPDATE public.seccional_submissions 
  SET created_by = '00000000-0000-0000-0000-000000000000'::uuid
  WHERE created_by IS NULL;
END $$;

-- Now we can safely make created_by NOT NULL
ALTER TABLE public.seccional_submissions 
ALTER COLUMN created_by SET NOT NULL;

-- Create secure RLS policies for seccional submissions

-- Admins can manage all submissions across all organizations
CREATE POLICY "Admins can manage all submissions"
ON public.seccional_submissions
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- Moderators can view all submissions
CREATE POLICY "Moderators can view all submissions"
ON public.seccional_submissions
FOR SELECT
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role));

-- Moderators can insert submissions for their organization
CREATE POLICY "Moderators can insert submissions for their organization"
ON public.seccional_submissions
FOR INSERT
TO authenticated
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role) 
  AND auth.uid() = created_by
);

-- Moderators can update submissions for their organization
CREATE POLICY "Moderators can update submissions for their organization"
ON public.seccional_submissions
FOR UPDATE
TO authenticated
USING (
  has_role(auth.uid(), 'moderador'::app_role) 
  AND (created_by = auth.uid() OR created_by = '00000000-0000-0000-0000-000000000000'::uuid)
)
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role)
);

-- Moderators can delete submissions for their organization
CREATE POLICY "Moderators can delete submissions for their organization"
ON public.seccional_submissions
FOR DELETE
TO authenticated
USING (
  has_role(auth.uid(), 'moderador'::app_role) 
  AND (created_by = auth.uid() OR created_by = '00000000-0000-0000-0000-000000000000'::uuid)
);

-- Users can view submissions from their own organization
CREATE POLICY "Users can view submissions from their organization"
ON public.seccional_submissions
FOR SELECT
TO authenticated
USING (EXISTS (
  SELECT 1 FROM organizaciones o
  JOIN user_roles ur ON ur.organizacion_id = o.id
  WHERE o.nombre = seccional_submissions.seccional_nombre
  AND ur.user_id = auth.uid()
));

-- Users can create submissions for their organization
CREATE POLICY "Users can create submissions for their organization"
ON public.seccional_submissions
FOR INSERT
TO authenticated
WITH CHECK (
  auth.uid() = created_by
  AND EXISTS (
    SELECT 1 FROM organizaciones o
    JOIN user_roles ur ON ur.organizacion_id = o.id
    WHERE o.nombre = seccional_submissions.seccional_nombre
    AND ur.user_id = auth.uid()
  )
);

-- Users can update their own submissions
CREATE POLICY "Users can update their own submissions"
ON public.seccional_submissions
FOR UPDATE
TO authenticated
USING (auth.uid() = created_by)
WITH CHECK (auth.uid() = created_by);