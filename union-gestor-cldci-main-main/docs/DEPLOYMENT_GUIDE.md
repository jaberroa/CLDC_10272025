# 🚀 Guía de Deployment - CLDCI

Guía completa para desplegar el proyecto CLDCI en producción.

## 📋 Pre-requisitos

- Node.js 20+
- Cuenta de GitHub conectada a Lovable
- Cuenta en plataforma de hosting (Vercel, Netlify, etc.)
- Supabase project configurado

## 🌟 Opciones de Deployment

### 1. Lovable Hosting (Más Simple)

Lovable proporciona hosting automático:

1. Click en "Publish" en el editor de Lovable
2. Tu app se despliega automáticamente
3. URL generada: `https://[tu-proyecto].lovableproject.com`

**Ventajas:**
- Deployment instantáneo
- SSL automático
- Sin configuración adicional

### 2. GitHub + Vercel (Recomendado)

#### Paso 1: Conectar a GitHub

1. En Lovable, click "GitHub" → "Connect to GitHub"
2. Autoriza la app de Lovable
3. Crea un nuevo repositorio

#### Paso 2: Configurar Vercel

1. Ve a [vercel.com](https://vercel.com)
2. Click "New Project"
3. Importa tu repositorio de GitHub
4. Configura las variables de entorno:

```env
VITE_SUPABASE_URL=tu_supabase_url
VITE_SUPABASE_ANON_KEY=tu_supabase_anon_key
```

5. Click "Deploy"

**Ventajas:**
- Deployment automático en cada push
- Preview URLs para PRs
- Edge network global
- Analytics incluidos

### 3. GitHub + Netlify

#### Configuración en Netlify:

1. Conecta tu repositorio de GitHub
2. Build settings:
   - Build command: `npm run build`
   - Publish directory: `dist`
3. Variables de entorno (igual que Vercel)
4. Deploy

**Ventajas:**
- Functions serverless incluidas
- Forms handling
- Split testing

### 4. Self-Hosting

#### Opción A: VPS (DigitalOcean, AWS, etc.)

```bash
# 1. Clonar repositorio
git clone [tu-repo-url]
cd [proyecto]

# 2. Instalar dependencias
npm ci

# 3. Build
npm run build

# 4. Servir con nginx/caddy
# Copiar dist/ a /var/www/html
```

#### Opción B: Docker

```dockerfile
# Dockerfile
FROM node:20-alpine as build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

```bash
# Build y run
docker build -t cldci-app .
docker run -p 80:80 cldci-app
```

## 🔐 Configuración de Variables de Entorno

### Variables Requeridas:

```env
# Supabase (públicas - OK para exponer)
VITE_SUPABASE_URL=https://tu-proyecto.supabase.co
VITE_SUPABASE_ANON_KEY=tu_anon_key

# Sentry (opcional - DSN público)
SENTRY_DSN=https://...@sentry.io/...

# PostHog (opcional - clave pública)
POSTHOG_API_KEY=phc_...
```

### Variables Secretas (Solo Backend):

Configurar en Supabase Edge Functions:

```bash
# Usando Supabase CLI
supabase secrets set SENTRY_DSN=your_dsn
supabase secrets set POSTHOG_API_KEY=your_key
```

## 🏗️ Build de Producción

```bash
# Limpieza
rm -rf dist node_modules
npm ci

# Build optimizado
npm run build

# Verificar build
ls -lah dist/

# Preview local del build
npm run preview
```

## 🔍 Checklist Pre-Deployment

- [ ] Todas las variables de entorno configuradas
- [ ] Tests pasando (`npm run test`)
- [ ] Build exitoso (`npm run build`)
- [ ] Sin errores de TypeScript (`npx tsc --noEmit`)
- [ ] Lighthouse score ≥90 (`npm run lighthouse`)
- [ ] Secrets de Supabase configurados
- [ ] DNS configurado (si usas dominio custom)
- [ ] SSL/TLS habilitado
- [ ] Backups de base de datos configurados

## 🌐 Configuración de Dominio Custom

### En Vercel:

1. Proyecto → Settings → Domains
2. Añadir dominio custom
3. Configurar DNS:

```dns
# Type  Name  Value
CNAME   www   cname.vercel-dns.com
A       @     76.76.21.21
```

### En Netlify:

1. Site settings → Domain management
2. Añadir custom domain
3. Configurar DNS según instrucciones

## 🔄 Continuous Deployment

### Configuración Automática:

Una vez conectado a GitHub, los deployments son automáticos:

- **Push a `main`**: Deploy a producción
- **Push a `develop`**: Deploy a staging
- **Pull Requests**: Preview deploys

### Rollback:

**Vercel:**
```bash
vercel rollback [deployment-url]
```

**Netlify:**
```bash
netlify rollback
```

## 📊 Monitoreo Post-Deployment

### 1. Health Checks

```bash
# Verificar que la app responde
curl -I https://tu-dominio.com

# Verificar SSL
openssl s_client -connect tu-dominio.com:443
```

### 2. Performance Monitoring

- Lighthouse CI en cada deploy
- Sentry para errores
- PostHog para analytics

### 3. Uptime Monitoring

Configura servicios como:
- UptimeRobot
- Pingdom
- Datadog

## 🐛 Troubleshooting

### Build falla en producción

```bash
# Verificar localmente
npm ci
npm run build

# Revisar logs de build
# En Vercel: Deployments → [deployment] → Build Logs
# En Netlify: Deploys → [deploy] → Deploy log
```

### Variables de entorno no funcionan

- Verifica que tengan prefijo `VITE_` para frontend
- Reinicia el deployment después de cambiar vars
- No uses `process.env` en frontend (usa `import.meta.env`)

### Supabase connection fails

- Verifica que la URL y key sean correctas
- Chequea CORS en Supabase dashboard
- Verifica que RLS policies permitan acceso

### 404 en rutas

Configura redirects para SPA:

**Vercel (`vercel.json`):**
```json
{
  "rewrites": [{ "source": "/(.*)", "destination": "/index.html" }]
}
```

**Netlify (`netlify.toml`):**
```toml
[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

## 📈 Optimizaciones Post-Deploy

1. **CDN**: Activar Cloudflare o similar
2. **Compression**: Habilitar Gzip/Brotli
3. **Caching**: Configurar cache headers
4. **Preload**: Critical resources
5. **Images**: Usar CDN de imágenes (Cloudinary, imgix)

## 🔒 Security Checklist

- [ ] HTTPS habilitado
- [ ] Security headers configurados
- [ ] CORS configurado correctamente
- [ ] Rate limiting en Supabase
- [ ] Secrets nunca en frontend
- [ ] Dependencies auditadas
- [ ] Supabase RLS policies activas

## 📚 Recursos Útiles

- [Vercel Docs](https://vercel.com/docs)
- [Netlify Docs](https://docs.netlify.com)
- [Supabase Docs](https://supabase.com/docs)
- [Vite Deploy Guide](https://vitejs.dev/guide/static-deploy.html)
- [GitHub Actions](https://docs.github.com/en/actions)

## 🆘 Soporte

- **Lovable Discord**: [discord.gg/lovable](https://discord.com/channels/1119885301872070706)
- **Docs**: https://docs.lovable.dev
- **GitHub Issues**: En tu repositorio
