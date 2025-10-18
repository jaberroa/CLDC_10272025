import { z } from 'zod';

/**
 * Elections and voting validation schemas
 * Critical security validation for electoral processes
 */

export const electionSchema = z.object({
  titulo: z
    .string()
    .trim()
    .min(5, 'El título debe tener al menos 5 caracteres')
    .max(200, 'El título es muy largo'),
  descripcion: z
    .string()
    .trim()
    .max(2000, 'La descripción es muy larga')
    .optional(),
  fecha_inicio: z
    .string()
    .regex(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/, 'Formato de fecha inválido')
    .refine((date) => new Date(date) > new Date(), 'La fecha debe ser futura'),
  fecha_fin: z
    .string()
    .regex(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/, 'Formato de fecha inválido'),
  organizacion_id: z.string().uuid('ID de organización inválido'),
  tipo_eleccion: z.enum(['directiva', 'asamblea', 'referendo'], {
    errorMap: () => ({ message: 'Tipo de elección inválido' }),
  }),
}).refine(
  (data) => new Date(data.fecha_fin) > new Date(data.fecha_inicio),
  {
    message: 'La fecha de fin debe ser posterior a la fecha de inicio',
    path: ['fecha_fin'],
  }
);

export const voteSchema = z.object({
  eleccion_id: z.string().uuid('ID de elección inválido'),
  candidato_id: z.string().uuid('ID de candidato inválido'),
  elector_id: z.string().uuid('ID de elector inválido'),
});

export const candidateSchema = z.object({
  eleccion_id: z.string().uuid('ID de elección inválido'),
  miembro_id: z.string().uuid('ID de miembro inválido'),
  propuestas: z
    .string()
    .trim()
    .max(5000, 'Las propuestas son muy largas')
    .optional(),
  foto_url: z
    .string()
    .url('URL de foto inválida')
    .optional()
    .or(z.literal('')),
});

export type ElectionInput = z.infer<typeof electionSchema>;
export type VoteInput = z.infer<typeof voteSchema>;
export type CandidateInput = z.infer<typeof candidateSchema>;
