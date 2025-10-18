import { z } from 'zod';

/**
 * Environment variables validation schema
 * All environment variables are validated at runtime to prevent configuration errors
 */
const envSchema = z.object({
  // Supabase Configuration
  SUPABASE_URL: z.string().url('SUPABASE_URL must be a valid URL'),
  SUPABASE_ANON_KEY: z.string().min(1, 'SUPABASE_ANON_KEY is required'),
  SUPABASE_PROJECT_ID: z.string().min(1, 'SUPABASE_PROJECT_ID is required'),
  
  // App Configuration
  MODE: z.enum(['development', 'production', 'test']).default('development'),
  DEV: z.boolean().default(false),
  PROD: z.boolean().default(false),
  
  // Optional: Analytics & Monitoring (to be added in later phases)
  SENTRY_DSN: z.string().url().optional(),
  POSTHOG_KEY: z.string().optional(),
  POSTHOG_HOST: z.string().url().optional(),
});

/**
 * Parse and validate environment variables
 * Throws an error if validation fails
 */
function validateEnv() {
  const env = {
    SUPABASE_URL: import.meta.env.VITE_SUPABASE_URL,
    SUPABASE_ANON_KEY: import.meta.env.VITE_SUPABASE_PUBLISHABLE_KEY,
    SUPABASE_PROJECT_ID: import.meta.env.VITE_SUPABASE_PROJECT_ID,
    MODE: import.meta.env.MODE,
    DEV: import.meta.env.DEV,
    PROD: import.meta.env.PROD,
    SENTRY_DSN: import.meta.env.VITE_SENTRY_DSN,
    POSTHOG_KEY: import.meta.env.VITE_POSTHOG_KEY,
    POSTHOG_HOST: import.meta.env.VITE_POSTHOG_HOST,
  };

  try {
    return envSchema.parse(env);
  } catch (error) {
    if (error instanceof z.ZodError) {
      const missingVars = error.errors.map((err) => {
        return `${err.path.join('.')}: ${err.message}`;
      });
      
      throw new Error(
        `❌ Invalid environment variables:\n${missingVars.join('\n')}\n\nPlease check your .env file.`
      );
    }
    throw error;
  }
}

/**
 * Validated and type-safe environment variables
 * Use this instead of accessing import.meta.env directly
 */
export const env = validateEnv();

/**
 * Type-safe environment configuration
 */
export type Env = z.infer<typeof envSchema>;

/**
 * Check if app is running in development mode
 */
export const isDevelopment = env.MODE === 'development';

/**
 * Check if app is running in production mode
 */
export const isProduction = env.MODE === 'production';

/**
 * Check if app is running in test mode
 */
export const isTest = env.MODE === 'test';
