-- Asignar rol de administrador al usuario Carlos Ventura
INSERT INTO public.user_roles (user_id, role) 
VALUES ('a6053ad0-91db-4bc6-b332-f6324c779f50', 'admin'::app_role)
ON CONFLICT (user_id, role) DO NOTHING;

-- Verificar que el rol se asignó correctamente
SELECT ur.*, p.nombre_completo 
FROM user_roles ur
JOIN profiles p ON p.id = ur.user_id
WHERE ur.user_id = 'a6053ad0-91db-4bc6-b332-f6324c779f50';