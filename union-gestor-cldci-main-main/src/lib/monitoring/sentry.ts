/**
 * Sentry Error Tracking Configuration
 * Monitors errors, performance, and provides user feedback
 */

import * as Sentry from '@sentry/react';

// Note: Sentry DSN is safe to be public
// Configure your Sentry DSN in Supabase Secrets as SENTRY_DSN
// For frontend, you can use the public DSN directly here
export const initSentry = () => {
  // Skip initialization if no DSN is configured
  const SENTRY_DSN = ''; // Configure this with your actual Sentry DSN
  
  if (!SENTRY_DSN) {
    console.log('Sentry DSN not configured. Skipping Sentry initialization.');
    return;
  }
  
  // Only initialize in production
  if (import.meta.env.MODE === 'production') {
    Sentry.init({
      dsn: SENTRY_DSN,
      integrations: [
        Sentry.browserTracingIntegration(),
        Sentry.replayIntegration({
          maskAllText: true,
          blockAllMedia: true,
        }),
      ],
      
      // Performance Monitoring
      tracesSampleRate: 0.1, // 10% of transactions for performance monitoring
      tracePropagationTargets: ['localhost', /^https:\/\/.*\.supabase\.co/],
      
      // Session Replay
      replaysSessionSampleRate: 0.1, // 10% of sessions
      replaysOnErrorSampleRate: 1.0, // 100% of sessions with errors
      
      // Environment
      environment: import.meta.env.MODE,
      
      // Release tracking
      // release: import.meta.env.VITE_APP_VERSION,
      
      // Before send hook to filter sensitive data
      beforeSend(event, hint) {
        // Don't send errors in development
        if (import.meta.env.MODE === 'development') {
          console.error('Sentry Error:', hint.originalException || hint.syntheticException);
          return null;
        }
        
        // Filter out sensitive information
        if (event.request) {
          delete event.request.cookies;
          delete event.request.headers;
        }
        
        return event;
      },
    });
  }
};

/**
 * Capture an exception manually
 */
export const captureException = (error: Error, context?: Record<string, any>) => {
  if (context) {
    Sentry.setContext('additional_context', context);
  }
  Sentry.captureException(error);
};

/**
 * Capture a message manually
 */
export const captureMessage = (message: string, level: Sentry.SeverityLevel = 'info') => {
  Sentry.captureMessage(message, level);
};

/**
 * Set user context for error tracking
 */
export const setUser = (user: { id: string; email?: string; username?: string } | null) => {
  Sentry.setUser(user);
};

/**
 * Add breadcrumb for tracking user actions
 */
export const addBreadcrumb = (breadcrumb: Sentry.Breadcrumb) => {
  Sentry.addBreadcrumb(breadcrumb);
};

/**
 * Start a performance transaction (span)
 */
export const startSpan = (name: string, op: string, callback: () => void) => {
  return Sentry.startSpan({ name, op }, callback);
};
