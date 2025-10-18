# CI/CD Pipeline Setup

Este documento describe el pipeline de CI/CD configurado para el proyecto CLDCI.

## 🔄 Workflows Configurados

### 1. CI Pipeline (`ci.yml`)

Pipeline principal que se ejecuta en cada push y pull request:

#### Jobs:

1. **Lint & Type Check**
   - Ejecuta ESLint para validar código
   - Verifica tipos con TypeScript
   - Bloquea merge si hay errores

2. **Build**
   - Compila la aplicación
   - Verifica que no haya errores de build
   - Guarda artifacts para otros jobs

3. **Unit Tests**
   - Ejecuta tests unitarios con Vitest
   - Genera reporte de coverage
   - Sube coverage a Codecov

4. **E2E Tests**
   - Ejecuta tests end-to-end con Playwright
   - Prueba en Chrome, Firefox y Safari
   - Genera reportes visuales

5. **Lighthouse CI**
   - Audita performance, accesibilidad, SEO
   - Verifica que se cumplan los budgets
   - Bloquea si no se alcanzan los scores mínimos

6. **Security Audit**
   - Escanea vulnerabilidades con npm audit
   - Verifica dependencias en producción

7. **Deploy Preview** (solo PRs)
   - Despliega preview del PR
   - Comenta URL en el PR

### 2. Production Deployment (`deploy-production.yml`)

Pipeline de deployment a producción:

#### Jobs:

1. **Pre-Deploy Checks**
   - Type check
   - Tests
   - Build de producción

2. **Deploy**
   - Despliega a producción
   - Requiere aprobación manual (environment protection)

3. **Post-Deploy Tests**
   - Smoke tests en producción
   - Lighthouse audit de producción

## 📊 Lighthouse CI

### Budgets Configurados:

- **Performance**: ≥90
- **Accessibility**: ≥90
- **Best Practices**: ≥90
- **SEO**: ≥90

### Métricas Específicas:

- First Contentful Paint: ≤2s
- Largest Contentful Paint: ≤2.5s
- Cumulative Layout Shift: ≤0.1
- Total Blocking Time: ≤300ms

### Budgets de Recursos:

- Scripts: ≤300KB
- Stylesheets: ≤50KB
- Images: ≤500KB
- Fonts: ≤100KB

## 🔐 Configuración de Secrets

Los siguientes secrets deben configurarse en GitHub:

1. `CODECOV_TOKEN` - Para reportes de coverage
2. `DEPLOY_TOKEN` - Para deployment automático
3. Otros según tu proveedor de hosting

## 🚀 Deployment

### Preview Deployments

- Se crean automáticamente para cada PR
- URL comentada en el PR
- Se destruyen al cerrar el PR

### Production Deployment

- Requiere aprobación manual
- Solo desde rama `main`
- Se ejecuta automáticamente después de merge

## 📝 Scripts Necesarios

Añade estos scripts a `package.json`:

```json
{
  "scripts": {
    "test": "vitest",
    "test:coverage": "vitest run --coverage",
    "test:e2e": "playwright test",
    "lighthouse": "lhci autorun"
  }
}
```

## 🔧 Configuración Local

### Ejecutar Lighthouse localmente:

```bash
npm run lighthouse
```

### Ejecutar todos los checks:

```bash
npm run lint
npm run test
npm run test:e2e
npm run build
```

## 📈 Monitoreo

### Coverage Reports

- Se suben a Codecov automáticamente
- Ver en: https://codecov.io/gh/[tu-org]/[tu-repo]

### Lighthouse Reports

- Se guardan como artifacts en GitHub Actions
- Disponibles por 30 días

### Test Reports

- Playwright genera reportes visuales
- Disponibles como artifacts

## 🐛 Troubleshooting

### Build falla en CI pero funciona localmente

- Verifica versión de Node.js
- Limpia caché: `npm ci`
- Revisa variables de entorno

### Tests fallan solo en CI

- Puede ser por timeouts más estrictos
- Verifica que no haya dependencias de estado local

### Lighthouse scores bajos

- Revisa el reporte detallado en artifacts
- Optimiza recursos según recomendaciones
- Considera lazy loading de componentes

## 📚 Recursos

- [GitHub Actions Docs](https://docs.github.com/en/actions)
- [Playwright Docs](https://playwright.dev)
- [Lighthouse CI Docs](https://github.com/GoogleChrome/lighthouse-ci)
- [Vitest Docs](https://vitest.dev)
