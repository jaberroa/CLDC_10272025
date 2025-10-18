-- Create storage policies for existing expedientes bucket to handle documents
CREATE POLICY "Users can view expedientes for their organization" 
ON storage.objects 
FOR SELECT 
USING (
  bucket_id = 'expedientes' AND 
  EXISTS (
    SELECT 1 
    FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (
      ur.role = 'admin'::app_role OR 
      has_role(auth.uid(), 'moderador'::app_role)
    )
  )
);

CREATE POLICY "Admins and moderators can upload to expedientes" 
ON storage.objects 
FOR INSERT 
WITH CHECK (
  bucket_id = 'expedientes' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can update expedientes" 
ON storage.objects 
FOR UPDATE 
USING (
  bucket_id = 'expedientes' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can delete expedientes" 
ON storage.objects 
FOR DELETE 
USING (
  bucket_id = 'expedientes' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);