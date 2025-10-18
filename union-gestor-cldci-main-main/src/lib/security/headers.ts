/**
 * Security headers configuration
 * These headers help protect against common web vulnerabilities
 */

export const securityHeaders = {
  'Content-Security-Policy': [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "font-src 'self' https://fonts.gstatic.com",
    "img-src 'self' data: https: blob:",
    "connect-src 'self' https://*.supabase.co wss://*.supabase.co",
    "frame-ancestors 'none'",
  ].join('; '),
  'X-Content-Type-Options': 'nosniff',
  'X-Frame-Options': 'DENY',
  'X-XSS-Protection': '1; mode=block',
  'Referrer-Policy': 'strict-origin-when-cross-origin',
  'Permissions-Policy': 'camera=(), microphone=(), geolocation=()',
};

/**
 * Apply security headers to the document
 * Note: In a production environment, these should be set at the server level
 */
export function applySecurityHeaders() {
  // Add meta tags for security headers that can be set client-side
  const meta = document.createElement('meta');
  meta.httpEquiv = 'Content-Security-Policy';
  meta.content = securityHeaders['Content-Security-Policy'];
  document.head.appendChild(meta);
}
