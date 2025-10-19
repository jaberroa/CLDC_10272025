-- Crear usuarios de acceso rápido para CLDCI
-- Contraseñas: admin123, miembros123, directiva123

INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at) VALUES 
('Administrador CLDCI', 'admin@cldci.org', '$2y$12$LQv3c1yqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBw', NOW(), NOW(), NOW()),
('Gestor de Miembros', 'miembros@cldci.org', '$2y$12$LQv3c1yqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBw', NOW(), NOW(), NOW()),
('Directiva CLDCI', 'directiva@cldci.org', '$2y$12$LQv3c1yqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBwEHxqBw', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE 
name=VALUES(name), 
password=VALUES(password), 
updated_at=NOW();

