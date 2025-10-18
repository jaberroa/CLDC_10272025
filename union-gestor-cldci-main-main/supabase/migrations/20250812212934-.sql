-- CRITICAL SECURITY FIX: Secure seccional_submissions table
-- Remove dangerous public access policies and implement proper authentication

-- Drop the dangerous public testing policies
DROP POLICY IF EXISTS "Public can insert submissions for testing" ON public.seccional_submissions;
DROP POLICY IF EXISTS "Public can view submissions for testing" ON public.seccional_submissions;

-- Ensure created_by is properly set for access control
-- Update any NULL created_by values to prevent orphaned records
UPDATE public.seccional_submissions 
SET created_by = (
  SELECT id FROM auth.users 
  WHERE email = 'admin@cldci.org' 
  LIMIT 1
)
WHERE created_by IS NULL;

-- If no admin user exists, use the first available user
UPDATE public.seccional_submissions 
SET created_by = (
  SELECT id FROM auth.users LIMIT 1
)
WHERE created_by IS NULL;

-- Make created_by NOT NULL to ensure proper access control
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

-- Moderators can view all submissions but manage only their organization's
CREATE POLICY "Moderators can view all submissions"
ON public.seccional_submissions
FOR SELECT
TO authenticated
USING (has_role(auth.uid(), 'moderador'::app_role));

CREATE POLICY "Moderators can manage submissions for their organization"
ON public.seccional_submissions
FOR INSERT, UPDATE, DELETE
TO authenticated
USING (
  has_role(auth.uid(), 'moderador'::app_role) 
  AND EXISTS (
    SELECT 1 FROM organizaciones o
    WHERE o.nombre = seccional_submissions.seccional_nombre
    AND EXISTS (
      SELECT 1 FROM user_roles ur
      WHERE ur.user_id = auth.uid()
      AND ur.organizacion_id = o.id
      AND ur.role = 'moderador'::app_role
    )
  )
)
WITH CHECK (
  has_role(auth.uid(), 'moderador'::app_role) 
  AND EXISTS (
    SELECT 1 FROM organizaciones o
    WHERE o.nombre = seccional_submissions.seccional_nombre
    AND EXISTS (
      SELECT 1 FROM user_roles ur
      WHERE ur.user_id = auth.uid()
      AND ur.organizacion_id = o.id
      AND ur.role = 'moderador'::app_role
    )
  )
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