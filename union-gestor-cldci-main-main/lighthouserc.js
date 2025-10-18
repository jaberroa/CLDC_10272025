/**
 * Lighthouse CI Configuration
 * Performance budgets and assertions for automated testing
 */

module.exports = {
  ci: {
    collect: {
      startServerCommand: 'npm run dev',
      url: [
        'http://localhost:8080/',
        'http://localhost:8080/dashboard',
        'http://localhost:8080/miembros',
      ],
      numberOfRuns: 3,
      settings: {
        preset: 'desktop',
      },
    },
    assert: {
      assertions: {
        // Performance budgets
        'categories:performance': ['error', { minScore: 0.9 }],
        'categories:accessibility': ['error', { minScore: 0.9 }],
        'categories:best-practices': ['error', { minScore: 0.9 }],
        'categories:seo': ['error', { minScore: 0.9 }],
        
        // Specific metrics
        'first-contentful-paint': ['error', { maxNumericValue: 2000 }],
        'largest-contentful-paint': ['error', { maxNumericValue: 2500 }],
        'cumulative-layout-shift': ['error', { maxNumericValue: 0.1 }],
        'total-blocking-time': ['error', { maxNumericValue: 300 }],
        
        // Resource budgets
        'resource-summary:script:size': ['error', { maxNumericValue: 300000 }],
        'resource-summary:stylesheet:size': ['error', { maxNumericValue: 50000 }],
        'resource-summary:document:size': ['error', { maxNumericValue: 50000 }],
        'resource-summary:image:size': ['error', { maxNumericValue: 500000 }],
        'resource-summary:font:size': ['error', { maxNumericValue: 100000 }],
        
        // Accessibility
        'color-contrast': 'error',
        'image-alt': 'error',
        'label': 'error',
        'link-name': 'error',
        
        // SEO
        'meta-description': 'error',
        'document-title': 'error',
        'viewport': 'error',
      },
    },
    upload: {
      target: 'temporary-public-storage',
    },
  },
};
