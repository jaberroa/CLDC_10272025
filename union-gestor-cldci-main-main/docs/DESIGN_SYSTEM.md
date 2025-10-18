# Sistema de Diseño CLDC - Guía Técnica

Documentación técnica completa del sistema de diseño oficial del **Círculo de Locutores Dominicanos Colegiados, Inc. (CLDC)**.

**Versión:** 1.0.0  
**Última actualización:** 9 de Octubre de 2025  
**Basado en:** [Manual de Identidad CLDC 2025](MANUAL_IDENTIDAD_CLDC_2025.md)

## 🎨 Principios de Diseño

1. **Consistencia**: Uso de tokens y componentes reutilizables
2. **Accesibilidad**: Cumplimiento WCAG 2.1 AA
3. **Responsive**: Mobile-first approach
4. **Performance**: Optimización de assets y carga
5. **Mantenibilidad**: Código limpio y documentado

## 🎯 Tokens de Diseño

### Colores Oficiales CLDC 2025 (HSL)

Todos los colores se definen en formato HSL en `src/index.css` siguiendo la **paleta oficial CLDC**:

```css
:root {
  /* Paleta Oficial CLDC 2025 */
  
  /* Azul Institucional #003049 - Color principal */
  --primary: 207 100% 15%;
  --primary-foreground: 0 0% 100%;
  
  /* Azul Claro Digital #669BBC - Secundario */
  --primary-light: 202 38% 56%;
  
  /* Dorado Suave #F6AA1C - Acento y prestigio */
  --accent: 38 91% 54%;
  --accent-foreground: 0 0% 11%;
  
  /* Rojo Nacional #CE1126 - Acento patrio */
  --danger: 351 84% 43%;
  --danger-foreground: 0 0% 100%;
  
  /* Negro Humo #1C1C1C - Texto y dark mode */
  --secondary: 0 0% 11%;
  --secondary-foreground: 0 0% 100%;

  /* Status Colors */
  --success: 142 70% 45%;
  --warning: 38 92% 50%;
  --destructive: 351 84% 43%; /* Mismo que danger */

  /* Module Colors - Basados en paleta oficial */
  --module-dashboard: 207 100% 15%;    /* Azul Institucional */
  --module-registro: 202 38% 56%;      /* Azul Claro */
  --module-miembros: 142 70% 45%;      /* Verde éxito */
  --module-elecciones: 38 91% 54%;     /* Dorado */
  --module-asambleas: 351 84% 43%;     /* Rojo Nacional */
  --module-reportes: 207 100% 15%;     /* Azul Institucional */
  --module-estadisticas: 202 38% 56%;  /* Azul Claro */
  --module-integraciones: 0 0% 11%;    /* Negro Humo */
  
  /* Neutral Colors */
  --background: 0 0% 100%;             /* Blanco #FFFFFF */
  --foreground: 0 0% 11%;              /* Negro Humo */
  --card: 210 33% 96%;                 /* Gris Neutro #F0F4F8 */
  --card-foreground: 0 0% 11%;
  --muted: 210 33% 96%;
  --muted-foreground: 215 16% 46%;
  --border: 210 33% 88%;
  --input: 210 33% 88%;
  --ring: 207 100% 15%;
  
  /* Gradientes Oficiales */
  --gradient-primary: linear-gradient(135deg, hsl(207 100% 15%), hsl(202 38% 56%));
  --gradient-hero: linear-gradient(135deg, hsl(207 100% 15% / 0.9), hsl(202 38% 56% / 0.8));
  --gradient-accent: linear-gradient(135deg, hsl(38 91% 54%), hsl(351 84% 43%));
  
  /* Sombras CLDC */
  --shadow-card: 0 4px 6px -1px rgba(0, 48, 73, 0.1);
  --shadow-elevated: 0 10px 25px -5px rgba(0, 48, 73, 0.15);
  --shadow-accent: 0 10px 30px -10px hsl(38 91% 54% / 0.4);
}

.dark {
  /* Dark Mode - Adaptación de colores oficiales */
  --background: 0 0% 11%;              /* Negro Humo como base */
  --foreground: 0 0% 100%;
  --primary: 202 38% 56%;              /* Azul Claro para dark */
  --primary-light: 202 38% 66%;
  --accent: 38 91% 64%;                /* Dorado más brillante */
  --card: 0 0% 15%;
  /* ... otros dark mode tokens */
}
```

### Tipografía Oficial CLDC

```css
:root {
  /* Tipografías Institucionales CLDC */
  --font-headline: 'Poppins', -apple-system, sans-serif;  /* Titulares */
  --font-body: 'Inter', -apple-system, sans-serif;        /* Texto corrido */
  --font-ui: 'Rubik', -apple-system, sans-serif;          /* Subtítulos y UI */
  --font-mono: 'Fira Code', 'Courier New', monospace;     /* Código */
}
```

**Uso recomendado:**
- **Poppins Bold:** Títulos principales, encabezados H1-H2
- **Rubik Medium:** Subtítulos, navegación, botones
- **Inter Regular:** Párrafos, formularios, contenido general

### Espaciado

Basado en sistema de 4px:

- `0.5` = 2px
- `1` = 4px
- `2` = 8px
- `3` = 12px
- `4` = 16px
- `6` = 24px
- `8` = 32px
- `12` = 48px
- `16` = 64px

### Radios

```css
--radius: 0.5rem; /* 8px */
```

### Sombras

```css
--shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
--shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
--shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
--shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
--shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
```

## 🧩 Componentes Base

### Button

Variantes disponibles:

```tsx
import { Button } from '@/components/ui/button';

// Default
<Button>Click me</Button>

// Variantes
<Button variant="default">Default</Button>
<Button variant="destructive">Delete</Button>
<Button variant="outline">Outline</Button>
<Button variant="secondary">Secondary</Button>
<Button variant="ghost">Ghost</Button>
<Button variant="link">Link</Button>

// Tamaños
<Button size="default">Default</Button>
<Button size="sm">Small</Button>
<Button size="lg">Large</Button>
<Button size="icon">Icon</Button>
```

### Card

```tsx
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';

<Card>
  <CardHeader>
    <CardTitle>Card Title</CardTitle>
    <CardDescription>Card description</CardDescription>
  </CardHeader>
  <CardContent>
    Card content
  </CardContent>
  <CardFooter>
    Card footer
  </CardFooter>
</Card>
```

### Input & Form

```tsx
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';

<div>
  <Label htmlFor="email">Email</Label>
  <Input id="email" type="email" placeholder="you@example.com" />
</div>
```

### Badge

```tsx
import { Badge } from '@/components/ui/badge';

<Badge variant="default">Default</Badge>
<Badge variant="secondary">Secondary</Badge>
<Badge variant="destructive">Destructive</Badge>
<Badge variant="outline">Outline</Badge>
```

## 📱 Responsive Design

### Breakpoints

```css
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
2xl: 1536px
```

### Uso:

```tsx
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  {/* Content */}
</div>
```

## ♿ Accesibilidad

### Contraste de Colores

Todos los colores cumplen WCAG 2.1 AA:

- Texto normal: ratio ≥ 4.5:1
- Texto grande: ratio ≥ 3:1
- Elementos UI: ratio ≥ 3:1

### Navegación por Teclado

- Todos los elementos interactivos son accesibles por teclado
- Focus visible con `ring` classes
- Skip links implementados

### Screen Readers

- Uso correcto de etiquetas semánticas
- ARIA labels donde sea necesario
- Alt text en todas las imágenes

## 🎭 Animaciones

### Transiciones

```css
--transition-base: 150ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-fast: 100ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
```

### Clases de Animación

```tsx
// Fade in
<div className="animate-in fade-in duration-200">

// Slide in
<div className="animate-in slide-in-from-bottom-4">

// Zoom in
<div className="animate-in zoom-in-95">
```

## 📐 Layout Patterns

### Container

```tsx
<div className="container mx-auto px-6">
  {/* Content */}
</div>
```

### Grid System

```tsx
// 12-column grid
<div className="grid grid-cols-12 gap-4">
  <div className="col-span-12 md:col-span-6 lg:col-span-4">
    {/* Content */}
  </div>
</div>
```

### Flex Patterns

```tsx
// Center content
<div className="flex items-center justify-center">

// Space between
<div className="flex items-center justify-between">

// Stack vertically
<div className="flex flex-col gap-4">
```

## 🎨 Uso de Colores Semánticos

### ✅ CORRECTO:

```tsx
<div className="bg-primary text-primary-foreground">
<div className="text-success">
<Button variant="destructive">
```

### ❌ INCORRECTO:

```tsx
<div className="bg-blue-900 text-white">  {/* NO usar colores directos */}
<div className="text-green-600">          {/* NO usar colores de Tailwind */}
```

## 🔧 Customización

### Crear Variantes Personalizadas

```tsx
// En tu componente
import { cva } from "class-variance-authority";

const buttonVariants = cva(
  "base-classes",
  {
    variants: {
      variant: {
        primary: "bg-primary text-primary-foreground",
        custom: "bg-custom text-custom-foreground",
      },
    },
  }
);
```

### Extender Tokens

```css
/* En index.css */
:root {
  --custom-color: 200 100% 50%;
}

/* En tailwind.config.ts */
export default {
  theme: {
    extend: {
      colors: {
        custom: 'hsl(var(--custom-color))',
      },
    },
  },
};
```

## 📚 Recursos

- [Tailwind CSS Docs](https://tailwindcss.com)
- [Radix UI Docs](https://www.radix-ui.com)
- [shadcn/ui Docs](https://ui.shadcn.com)
- [WCAG Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

## 🎯 Checklist de Diseño

- [ ] Usa tokens de color semánticos
- [ ] Verifica contraste de colores
- [ ] Implementa responsive design
- [ ] Añade estados de hover/focus/active
- [ ] Incluye loading states
- [ ] Maneja estados de error
- [ ] Añade animaciones suaves
- [ ] Verifica accesibilidad
- [ ] Optimiza assets
- [ ] Documenta componentes nuevos
