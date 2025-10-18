import { z } from 'zod';

/**
 * Authentication form validation schemas
 * These schemas validate user input before submission to prevent injection attacks
 */

export const loginSchema = z.object({
  email: z
    .string()
    .trim()
    .min(1, 'Email es requerido')
    .email('Email inválido')
    .max(255, 'Email muy largo'),
  password: z
    .string()
    .min(8, 'La contraseña debe tener al menos 8 caracteres')
    .max(128, 'Contraseña muy larga'),
});

export const signUpSchema = z.object({
  email: z
    .string()
    .trim()
    .min(1, 'Email es requerido')
    .email('Email inválido')
    .max(255, 'Email muy largo'),
  password: z
    .string()
    .min(8, 'La contraseña debe tener al menos 8 caracteres')
    .max(128, 'Contraseña muy larga')
    .regex(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/,
      'La contraseña debe contener al menos una mayúscula, una minúscula y un número'
    ),
  confirmPassword: z.string(),
}).refine((data) => data.password === data.confirmPassword, {
  message: 'Las contraseñas no coinciden',
  path: ['confirmPassword'],
});

export const resetPasswordSchema = z.object({
  email: z
    .string()
    .trim()
    .min(1, 'Email es requerido')
    .email('Email inválido')
    .max(255, 'Email muy largo'),
});

export const updatePasswordSchema = z.object({
  password: z
    .string()
    .min(8, 'La contraseña debe tener al menos 8 caracteres')
    .max(128, 'Contraseña muy larga')
    .regex(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/,
      'La contraseña debe contener al menos una mayúscula, una minúscula y un número'
    ),
  confirmPassword: z.string(),
}).refine((data) => data.password === data.confirmPassword, {
  message: 'Las contraseñas no coinciden',
  path: ['confirmPassword'],
});

export type LoginInput = z.infer<typeof loginSchema>;
export type SignUpInput = z.infer<typeof signUpSchema>;
export type ResetPasswordInput = z.infer<typeof resetPasswordSchema>;
export type UpdatePasswordInput = z.infer<typeof updatePasswordSchema>;
