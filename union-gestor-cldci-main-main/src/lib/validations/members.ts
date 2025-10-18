import { z } from 'zod';

/**
 * Member data validation schemas
 * Ensures all member data is properly validated before database operations
 */

const cedulaRegex = /^\d{3}-\d{7}-\d{1}$/;
const phoneRegex = /^\+?[\d\s-()]+$/;

export const memberSchema = z.object({
  nombre_completo: z
    .string()
    .trim()
    .min(3, 'El nombre debe tener al menos 3 caracteres')
    .max(200, 'El nombre es muy largo'),
  cedula: z
    .string()
    .trim()
    .regex(cedulaRegex, 'Formato de cédula inválido (###-#######-#)')
    .optional(),
  email: z
    .string()
    .trim()
    .email('Email inválido')
    .max(255, 'Email muy largo')
    .optional()
    .or(z.literal('')),
  telefono: z
    .string()
    .trim()
    .regex(phoneRegex, 'Formato de teléfono inválido')
    .max(20, 'Teléfono muy largo')
    .optional()
    .or(z.literal('')),
  direccion: z
    .string()
    .trim()
    .max(500, 'Dirección muy larga')
    .optional()
    .or(z.literal('')),
  profesion: z
    .string()
    .trim()
    .max(200, 'Profesión muy larga')
    .optional()
    .or(z.literal('')),
  fecha_nacimiento: z
    .string()
    .regex(/^\d{4}-\d{2}-\d{2}$/, 'Formato de fecha inválido')
    .refine((date) => {
      const birthDate = new Date(date);
      const today = new Date();
      const age = today.getFullYear() - birthDate.getFullYear();
      return age >= 18 && age <= 120;
    }, 'La edad debe estar entre 18 y 120 años')
    .optional(),
  organizacion_id: z
    .string()
    .uuid('ID de organización inválido'),
  observaciones: z
    .string()
    .trim()
    .max(1000, 'Observaciones muy largas')
    .optional()
    .or(z.literal('')),
});

export const updateMemberSchema = memberSchema.partial().extend({
  id: z.string().uuid('ID inválido'),
});

export type MemberInput = z.infer<typeof memberSchema>;
export type UpdateMemberInput = z.infer<typeof updateMemberSchema>;
