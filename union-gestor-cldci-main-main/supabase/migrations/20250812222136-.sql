-- Create storage bucket for documentos
INSERT INTO storage.buckets (id, name, public) 
VALUES ('documentos', 'documentos', false);

-- Create storage policies for documentos bucket
CREATE POLICY "Users can view documents for their organization" 
ON storage.objects 
FOR SELECT 
USING (
  bucket_id = 'documentos' AND 
  EXISTS (
    SELECT 1 
    FROM user_roles ur 
    WHERE ur.user_id = auth.uid() 
    AND (
      ur.role = 'admin'::app_role OR 
      auth.uid()::text = (storage.foldername(name))[1]
    )
  )
);

CREATE POLICY "Admins and moderators can upload documents" 
ON storage.objects 
FOR INSERT 
WITH CHECK (
  bucket_id = 'documentos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can update documents" 
ON storage.objects 
FOR UPDATE 
USING (
  bucket_id = 'documentos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can delete documents" 
ON storage.objects 
FOR DELETE 
USING (
  bucket_id = 'documentos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

-- Create storage bucket for photos (directiva photos, member photos, etc.)
INSERT INTO storage.buckets (id, name, public) 
VALUES ('fotos', 'fotos', true);

-- Create storage policies for fotos bucket
CREATE POLICY "Photos are publicly viewable" 
ON storage.objects 
FOR SELECT 
USING (bucket_id = 'fotos');

CREATE POLICY "Admins and moderators can upload photos" 
ON storage.objects 
FOR INSERT 
WITH CHECK (
  bucket_id = 'fotos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can update photos" 
ON storage.objects 
FOR UPDATE 
USING (
  bucket_id = 'fotos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);

CREATE POLICY "Admins and moderators can delete photos" 
ON storage.objects 
FOR DELETE 
USING (
  bucket_id = 'fotos' AND 
  (
    has_role(auth.uid(), 'admin'::app_role) OR 
    has_role(auth.uid(), 'moderador'::app_role)
  )
);