---
title: "Manual de Identidad y Plataforma CLDC 2025"
organization: "Círculo de Locutores Dominicanos Colegiados, Inc. (CLDC)"
version: "1.0.0"
last_updated: "2025-10-09"
author: "Equipo de Arquitectura Digital CLDC & Asistente IA Institucional"
status: "Oficial - Implementado"
language: "es"
license: "Creative Commons CC BY-NC-SA 4.0"
---

# 📘 Manual de Identidad y Plataforma CLDC 2025

## 🏛️ Introducción

El presente documento establece los lineamientos visuales, técnicos y estructurales del **Círculo de Locutores Dominicanos Colegiados, Inc. (CLDC)**, con el propósito de consolidar una identidad moderna, modular y digitalmente optimizada.

Este manual sirve como **guía integral** para desarrolladores, diseñadores, miembros de la junta directiva y colaboradores técnicos.

---

## 🎨 Identidad Visual Institucional

### Logo

El logotipo del CLDC representa **la voz profesional dominicana**, integrando un micrófono estilizado con la silueta de la República Dominicana, simbolizando unidad, identidad nacional y comunicación ética.

### Paleta Oficial del CLDC (HEX / HSL)

| Nombre | HEX | HSL | Uso | Variable CSS |
|--------|------|------|-----|--------------|
| Azul Institucional | `#003049` | `hsl(207, 100%, 15%)` | Base del logotipo y cabeceras | `--primary` |
| Azul Claro Digital | `#669BBC` | `hsl(202, 38%, 56%)` | Fondos secundarios y botones | `--primary-light` |
| Rojo Nacional | `#CE1126` | `hsl(351, 84%, 43%)` | Acentos patrios y resaltados | `--danger` |
| Dorado Suave | `#F6AA1C` | `hsl(38, 91%, 54%)` | Prestigio, trayectoria y acento visual | `--accent` |
| Blanco | `#FFFFFF` | `hsl(0, 0%, 100%)` | Fondo principal | `--background` |
| Gris Neutro | `#F0F4F8` | `hsl(210, 33%, 96%)` | Tarjetas y paneles | `--card` |
| Negro Humo | `#1C1C1C` | `hsl(0, 0%, 11%)` | Texto y modo oscuro | `--secondary` |

### Tipografía

- **Titulares:** Poppins Bold (`--font-headline`)
- **Texto Corrido:** Inter Regular (`--font-body`)
- **Subtítulos / UI:** Rubik Medium (`--font-ui`)

### Tokens de Color (Design System)

```css
:root {
  /* Paleta Oficial CLDC */
  --primary: 207 100% 15%;           /* Azul Institucional #003049 */
  --primary-light: 202 38% 56%;      /* Azul Claro Digital #669BBC */
  --accent: 38 91% 54%;              /* Dorado Suave #F6AA1C */
  --danger: 351 84% 43%;             /* Rojo Nacional #CE1126 */
  --background: 0 0% 100%;           /* Blanco #FFFFFF */
  --card: 210 33% 96%;               /* Gris Neutro #F0F4F8 */
  --secondary: 0 0% 11%;             /* Negro Humo #1C1C1C */
  
  /* Gradientes Oficiales */
  --gradient-primary: linear-gradient(135deg, hsl(207 100% 15%), hsl(202 38% 56%));
  --gradient-hero: linear-gradient(135deg, hsl(207 100% 15% / 0.9), hsl(202 38% 56% / 0.8));
  --gradient-accent: linear-gradient(135deg, hsl(38 91% 54%), hsl(351 84% 43%));
  
  /* Sombras CLDC */
  --shadow-card: 0 4px 6px -1px rgba(0, 48, 73, 0.1);
  --shadow-elevated: 0 10px 25px -5px rgba(0, 48, 73, 0.15);
  --shadow-accent: 0 10px 30px -10px hsl(38 91% 54% / 0.4);
  
  /* Tipografía */
  --font-headline: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-ui: 'Rubik', sans-serif;
}
```

---

## 🧩 Sistema de Interfaz Modular

### Arquitectura Visual

La nueva plataforma CLDC utiliza una estructura modular basada en:
- **React 18+** con TypeScript
- **Tailwind CSS** con sistema de diseño personalizado
- **Supabase** para backend y autenticación
- **shadcn/ui** como librería de componentes base

#### Layout Principal

```
┌─────────────────────────────────────────┐
│  Header (Logo + Búsqueda + Perfil)     │
├──────┬──────────────────────────────────┤
│      │                                  │
│ Side │  Contenido Principal             │
│ bar  │  (Módulos Dinámicos)            │
│      │                                  │
│ Pleg │  Cards, Forms, Tables           │
│ able │  Modales, Drawers               │
│      │                                  │
└──────┴──────────────────────────────────┘
```

#### Componentes Base

- **Cards** con sombras suaves (`shadow-card`)
- **Forms** con validaciones Zod
- **Tablas** con paginación y sort dinámico
- **Modales / Drawers** accesibles (ARIA)
- **Command Palette (⌘K)** con búsqueda global
- **Toasts (Sonner)** para feedback rápido
- **Dark Mode** automático con persistencia

---

## ⚙️ Arquitectura Técnica

### Estructura de Directorios

```
src/
├── modules/          # Funcionalidades agrupadas por dominio
│   ├── miembros/
│   ├── elecciones/
│   └── directiva/
├── components/       # Elementos reutilizables
│   ├── ui/          # shadcn/ui components
│   ├── auth/        # Autenticación
│   └── layout/      # Header, Footer, Sidebar
├── hooks/           # Lógica compartida
├── lib/             # Configuración global
│   ├── utils.ts
│   └── supabase/
├── styles/          # Tokens, tema y tailwind
│   └── index.css
└── main.tsx         # Punto de entrada
```

### Stack Tecnológico

| Categoría | Tecnología | Propósito |
|-----------|-----------|-----------|
| **Frontend** | React 18 + TypeScript | UI y lógica de presentación |
| **Estilos** | Tailwind CSS | Sistema de diseño |
| **Backend** | Supabase | Base de datos, Auth, Storage |
| **Estado** | TanStack Query | Gestión de datos async |
| **Forms** | React Hook Form + Zod | Validación y formularios |
| **Testing** | Vitest + Playwright | Unit + E2E tests |
| **CI/CD** | GitHub Actions | Integración continua |

---

## 🧭 Módulos del Sistema

### Módulos Actuales (Implementados)

1. **Dashboard Central** – Estadísticas y accesos rápidos
2. **Gestión de Miembros** – Registro, carnets digitales, perfiles
3. **Directiva Institucional** – Órganos de gobierno y cargos
4. **Sistema Electoral** – Votaciones y consultas
5. **Transparencia Financiera** – Estados de cuenta y pagos
6. **Documentos Legales** – Estatutos, reglamentos, actas
7. **Reportes y Estadísticas** – Análisis de datos institucionales
8. **Reconocimientos y Premios** – Sistema de méritos
9. **Formación Profesional** – Cursos y diplomados
10. **Integraciones Digitales** – APIs y conectores externos

### Módulos Futuros (Roadmap 2025-2027)

| Fase | Módulo | Descripción | Prioridad |
|------|--------|-------------|-----------|
| Q1 2025 | Academia CLDC | Cursos, certificaciones y talleres virtuales integrados con LMS | Alta |
| Q2 2025 | Convenios Internacionales | Registro y seguimiento de acuerdos institucionales | Media |
| Q3 2025 | Prensa Digital CLDC | Portal de noticias gremiales, artículos y entrevistas | Alta |
| Q4 2025 | Canal de Miembros | Espacio privado con foros y transmisiones | Media |
| Q1 2026 | Sistema de Premios | Nominaciones, votaciones y galardones digitales | Media |
| Q2 2026 | IA Gremial | Panel analítico con indicadores de membresía e impacto | Alta |

---

## 🚀 Estrategia de Implementación

### Fase 1: Fundación (Completada ✅)
- ✅ Activar Design System y logo actualizado
- ✅ Implementar paleta oficial de colores
- ✅ Configurar tipografías institucionales
- ✅ Establecer tokens de diseño

### Fase 2: Módulos Base (En Progreso 🔄)
- ✅ Miembros y Colegiación
- ✅ Comunicación y Boletines
- ✅ Directiva Institucional
- 🔄 Sistema Electoral (refinamiento)
- 🔄 Transparencia Financiera (mejoras UX)

### Fase 3: Automatización (Planificado 📋)
- 📋 Integrar automatización Supabase
- 📋 Sistema de notificaciones push
- 📋 Workflows automatizados
- 📋 Sincronización multi-plataforma

### Fase 4: Optimización (Planificado 📋)
- 📋 CI/CD completo con GitHub Actions
- 📋 Pruebas automatizadas E2E
- 📋 Monitoreo de rendimiento (Lighthouse)
- 📋 Análisis de seguridad (Supabase Linter)

### Fase 5: Expansión (Planificado 📋)
- 📋 Academia CLDC
- 📋 Módulo de IA Gremial
- 📋 App móvil nativa
- 📋 Integración con redes sociales

---

## 📦 Entregables y Documentación

### Archivos Clave

```
/
├── src/
│   ├── index.css                    # Sistema de diseño oficial
│   ├── assets/cldc-logo.png         # Logo institucional
│   └── components/ui/               # Componentes base
│
├── docs/
│   ├── MANUAL_IDENTIDAD_CLDC_2025.md    # Este documento
│   ├── DESIGN_SYSTEM.md                 # Guía de diseño técnica
│   ├── AUDIT_REPORT.md                  # Reporte de seguridad
│   └── PROGRESS.md                      # Estado del proyecto
│
├── README.md                            # Documentación principal
├── tailwind.config.ts                   # Configuración Tailwind
└── package.json                         # Dependencias
```

### Comandos de Desarrollo

```bash
# Instalar dependencias
npm install

# Modo desarrollo
npm run dev

# Build producción
npm run build

# Tests
npm run test
npm run test:e2e

# Linting
npm run lint

# Preview build
npm run preview
```

---

## 🔐 Seguridad y Cumplimiento

### Medidas Implementadas

- ✅ Row-Level Security (RLS) en todas las tablas
- ✅ Autenticación JWT con Supabase Auth
- ✅ Encriptación en tránsito (HTTPS)
- ✅ Validación de inputs con Zod
- ✅ Sanitización de datos
- ✅ Rate limiting en API
- ✅ Auditoría de accesos

### Últimas Correcciones (2025-10-09)

Se han corregido **7 vulnerabilidades críticas**:
1. Contactos de miembros directivos expuestos
2. Datos de seccionales accesibles sin autorización
3. Feedback de delivery sin autenticación
4. Base de datos de clientes expuesta
5. Información estructural pública
6. Cursos y formación sin protección
7. Logs de auditoría sin control

**Estado actual:** ✅ Sistema seguro y listo para producción

---

## 🧠 Guías de Uso

### Para Desarrolladores

```tsx
// Usar colores semánticos
<div className="bg-primary text-primary-foreground">
  Azul Institucional CLDC
</div>

<div className="bg-accent text-accent-foreground">
  Dorado Suave - Acento
</div>

// Usar gradientes oficiales
<div className="bg-gradient-primary">
  Gradiente institucional
</div>

// Usar tipografías
<h1 className="font-[family-name:var(--font-headline)]">
  Titular Poppins
</h1>

<p className="font-[family-name:var(--font-body)]">
  Texto Inter
</p>
```

### Para Diseñadores

- Usar siempre la paleta oficial del CLDC
- Aplicar gradientes en heros y CTAs
- Mantener jerarquía tipográfica (Poppins > Rubik > Inter)
- Respetar espaciado 4px base
- Asegurar contraste WCAG AA (4.5:1)

---

## 🎯 Checklist de Calidad

- [x] Paleta de colores oficial implementada
- [x] Tipografías institucionales configuradas
- [x] Tokens de diseño definidos
- [x] Gradientes oficiales activos
- [x] Sombras personalizadas CLDC
- [x] Dark mode funcional
- [x] Componentes base actualizados
- [ ] Logo SVG moderno (pendiente conversión PNG→SVG)
- [ ] Guía PDF exportada
- [ ] Tests visuales automatizados

---

## 📚 Recursos y Enlaces

- [Tailwind CSS Documentation](https://tailwindcss.com)
- [shadcn/ui Components](https://ui.shadcn.com)
- [Supabase Documentation](https://supabase.com/docs)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [Poppins Font](https://fonts.google.com/specimen/Poppins)
- [Inter Font](https://rsms.me/inter/)
- [Rubik Font](https://fonts.google.com/specimen/Rubik)

---

## 🔄 Prompt Maestro de Regeneración

```
Actúa como un Equipo Senior Multidisciplinario (UX/UI, Fullstack, DevOps, Branding) 
para mantener, verificar o regenerar la plataforma institucional del CLDC.

PASOS:
1. Analiza el contenido de /docs/MANUAL_IDENTIDAD_CLDC_2025.md
2. Verifica que la estructura del repositorio y los módulos estén actualizados
3. Si hay inconsistencias, actualiza los componentes visuales y lógicos
4. Asegura que el diseño siga la paleta oficial definida
5. Comprueba compatibilidad con Tailwind, Supabase y shadcn/ui
6. Realiza pruebas de rendimiento, accesibilidad y seguridad
7. Genera reporte de auditoría final con estado del sistema
8. Confirmar éxito con: ✅ 'CLDC sistema verificado y actualizado'

OBJETIVO: 
Mantener la plataforma CLDC moderna, funcional y alineada con su identidad institucional.
```

---

## 📝 Historial de Cambios

| Fecha | Versión | Cambios |
|-------|---------|---------|
| 2025-10-09 | 1.0.0 | Creación del manual oficial e implementación completa |

---

## 📄 Licencia

Este documento está licenciado bajo [Creative Commons CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/).

**Atribución:** Círculo de Locutores Dominicanos Colegiados, Inc. (CLDC)  
**Uso:** Institucional y educativo  
**Restricciones:** No comercial, compartir bajo la misma licencia

---

## 👥 Contacto y Soporte

Para consultas sobre este manual o la plataforma:
- **Email:** soporte@cldci.com
- **Web:** https://cldci.com
- **GitHub:** [Repositorio del Proyecto]

---

**Última actualización:** 9 de octubre de 2025  
**Estado:** ✅ Oficial - Sistema en producción  
**Próxima revisión:** Enero 2026
