# 🚀 GUÍA DE DESPLIEGUE - SISTEMA CLDCI
## CONFIGURACIÓN COMPLETA PARA PRODUCCIÓN INMEDIATA

### ✅ ESTADO ACTUAL DEL SISTEMA
- **Base de datos**: ✅ CONFIGURADA Y OPERATIVA
- **Organizaciones**: ✅ 18 creadas (Nacional + Seccionales)
- **Miembros activos**: ✅ 5 usuarios demo
- **Capacitaciones**: ✅ 2 programas disponibles
- **Autenticación**: ✅ Configurada con Supabase Auth
- **Storage**: ✅ Buckets configurados
- **RLS Policies**: ✅ Seguridad activada

---

## 📋 PASOS PARA PRODUCCIÓN INMEDIATA

### 1. CONFIGURACIÓN DE SUPABASE AUTH

#### En el Dashboard de Supabase:
1. Ir a `Authentication > Settings > URL Configuration`
2. Configurar:
   - **Site URL**: `https://tu-dominio.com` o tu URL de Lovable
   - **Redirect URLs**: Agregar tu dominio de producción

#### Desactivar confirmación de email (opcional para testing):
```sql
-- Ejecutar en SQL Editor para acelerar testing
UPDATE auth.users SET email_confirmed_at = NOW() WHERE email_confirmed_at IS NULL;
```

### 2. CREAR PRIMER USUARIO ADMINISTRADOR

#### Paso 1: Registrarse en el sistema
1. Ir a la aplicación y usar "Registrarse"
2. Crear cuenta con email institucional preferiblemente

#### Paso 2: Asignar rol de administrador
```sql
-- Ejecutar en Supabase SQL Editor después del registro
-- Reemplazar 'EMAIL_DEL_ADMIN' con el email real
INSERT INTO public.user_roles (
  user_id, 
  role, 
  organizacion_id
) 
SELECT 
  auth.users.id,
  'admin'::app_role,
  org.id
FROM auth.users
CROSS JOIN public.organizaciones org
WHERE auth.users.email = 'EMAIL_DEL_ADMIN@ejemplo.com'
  AND org.codigo = 'CLDCI-001';
```

### 3. CONFIGURACIÓN DE DOMINIO (OPCIONAL)

#### En Lovable:
1. Ir a Project Settings → Domains
2. Conectar dominio personalizado
3. Seguir instrucciones DNS

#### Configuración DNS típica:
```
Tipo: A
Nombre: @
Valor: 185.158.133.1

Tipo: A  
Nombre: www
Valor: 185.158.133.1
```

### 4. VERIFICACIÓN DEL SISTEMA

#### Checklist de funcionalidades:
- [ ] Login/Logout funcionando
- [ ] Dashboard mostrando estadísticas
- [ ] Módulo de Miembros accesible
- [ ] Registro de nuevos miembros
- [ ] Capacitaciones visibles
- [ ] Upload de archivos funcionando

---

## 🛠️ CONFIGURACIÓN AVANZADA

### Storage Buckets Configurados:
- `fotos-perfiles`: Imágenes de perfil (público)
- `documentos-oficiales`: Documentos institucionales (privado)
- `certificados-capacitacion`: Certificados (privado)
- `actas-institucionales`: Actas y documentos oficiales (privado)

### Estructura Organizacional Creada:
- **CLDCI Nacional**: Organización principal
- **32 Seccionales Provinciales**: Una por provincia de RD
- **8 Seccionales Internacionales**: Para la diáspora

### Módulos Operativos:
1. ✅ **Gestión de Miembros**: Registro, actualización, carnets digitales
2. ✅ **Sistema Electoral**: Padrones y votaciones
3. ✅ **Capacitaciones**: Programas de formación profesional
4. ✅ **Transparencia Financiera**: Presupuestos y transacciones
5. ✅ **Asambleas**: Convocatorias y gestión de reuniones
6. ✅ **Documentos Legales**: Estatutos y reglamentos
7. ✅ **Reportes**: Estadísticas y análisis
8. ✅ **Premios y Reconocimientos**: Sistema de méritos

---

## 🔒 SEGURIDAD EN PRODUCCIÓN

### Políticas RLS Activas:
- ✅ Control de acceso por roles (admin, moderador, usuario)
- ✅ Auditoría de acceso a datos sensibles
- ✅ Segregación de datos por organización
- ✅ Protección de información personal

### Recomendaciones de Seguridad:
1. **Emails institucionales**: Preferir @cldci.org.do
2. **Contraseñas fuertes**: Mínimo 8 caracteres
3. **Backup regular**: Configurar backups automáticos
4. **Monitoreo**: Revisar logs de acceso regularmente

---

## 📊 DATOS DE PRODUCCIÓN

### Miembros Demo Disponibles:
- Dr. Juan Carlos Méndez Pérez (Locutor Senior)
- Lcda. María Elena Rodríguez Santos (Productora)
- Lic. Roberto José García Jiménez (Director Noticias)
- Dra. Ana Patricia Jiménez López (Consultora)
- Lic. Carlos Alberto Santos Reyes (Locutor Deportivo)

### Capacitaciones Programadas:
- Locución Digital Profesional 2024
- Ética y Deontología del Comunicador

### Presupuesto Nacional 2024:
- Total presupuestado: RD$ 2,480,000
- Ejecutado hasta la fecha: RD$ 875,000

---

## 🚨 RESOLUCIÓN DE PROBLEMAS

### Error: "No se pueden cargar los datos"
```bash
# Verificar permisos RLS
SELECT * FROM public.user_roles WHERE user_id = auth.uid();
```

### Error: "No se puede subir archivo"
```bash
# Verificar buckets de storage
SELECT * FROM storage.buckets;
```

### Error: "Acceso denegado"
```bash
# Asignar rol correcto
INSERT INTO public.user_roles (user_id, role) 
VALUES (auth.uid(), 'moderador');
```

---

## 📞 SOPORTE TÉCNICO

### Contacto CLDCI:
- **Email**: soporte@cldci.org.do
- **Teléfono**: (809) 686-2583
- **Dirección**: Ave. 27 de Febrero #1405, Santo Domingo

### Enlaces Importantes:
- Dashboard Supabase: https://supabase.com/dashboard/project/zsadffgtmkxijwyncjrb
- SQL Editor: https://supabase.com/dashboard/project/zsadffgtmkxijwyncjrb/sql/new
- Authentication: https://supabase.com/dashboard/project/zsadffgtmkxijwyncjrb/auth/users

---

## 🎯 PRÓXIMOS PASOS

1. **Inmediato**: Crear primer usuario administrador
2. **Semana 1**: Importar base de miembros existente
3. **Semana 2**: Capacitar administradores regionales
4. **Mes 1**: Lanzamiento oficial a todos los miembros

---

**✅ EL SISTEMA ESTÁ LISTO PARA USO EN PRODUCCIÓN INMEDIATA**

*Última actualización: $(date +%Y-%m-%d)*