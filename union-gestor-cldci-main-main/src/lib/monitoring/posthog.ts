/**
 * PostHog Analytics Configuration
 * Tracks user behavior, feature usage, and product analytics
 */

import posthog from 'posthog-js';

// PostHog configuration
// Get your project API key from: https://app.posthog.com/project/settings
const POSTHOG_KEY = ''; // User needs to add their PostHog key here
const POSTHOG_HOST = 'https://app.posthog.com';

let initialized = false;

/**
 * Initialize PostHog analytics
 */
export const initPostHog = () => {
  // Skip if no API key configured
  if (!POSTHOG_KEY) {
    console.log('PostHog API key not configured. Skipping PostHog initialization.');
    return;
  }
  
  // Only initialize once and in production
  if (initialized || import.meta.env.MODE !== 'production') {
    return;
  }

  posthog.init(POSTHOG_KEY, {
    api_host: POSTHOG_HOST,
    
    // Autocapture settings
    autocapture: {
      dom_event_allowlist: ['click', 'change', 'submit'], // Limit autocapture events
      url_allowlist: [window.location.origin],
    },
    
    // Capture pageviews automatically
    capture_pageview: true,
    
    // Capture performance metrics
    capture_pageleave: true,
    
    // Session recording (disable by default for privacy)
    disable_session_recording: true,
    
    // Persistence
    persistence: 'localStorage',
    
    // Privacy settings
    mask_all_text: true,
    mask_all_element_attributes: true,
    
    // Loading optimization
    loaded: (posthog) => {
      if (import.meta.env.MODE === 'development') {
        posthog.debug(); // Enable debug mode in development
      }
    },
  });

  initialized = true;
};

/**
 * Track a custom event
 */
export const trackEvent = (eventName: string, properties?: Record<string, any>) => {
  if (!initialized) return;
  
  posthog.capture(eventName, properties);
};

/**
 * Identify a user
 */
export const identifyUser = (userId: string, properties?: Record<string, any>) => {
  if (!initialized) return;
  
  posthog.identify(userId, properties);
};

/**
 * Reset user identification (on logout)
 */
export const resetUser = () => {
  if (!initialized) return;
  
  posthog.reset();
};

/**
 * Track page view manually
 */
export const trackPageView = (path?: string) => {
  if (!initialized) return;
  
  posthog.capture('$pageview', {
    $current_url: path || window.location.href,
  });
};

/**
 * Set user properties
 */
export const setUserProperties = (properties: Record<string, any>) => {
  if (!initialized) return;
  
  posthog.people.set(properties);
};

/**
 * Check if a feature flag is enabled
 */
export const isFeatureEnabled = (flagKey: string): boolean => {
  if (!initialized) return false;
  
  return posthog.isFeatureEnabled(flagKey) || false;
};

/**
 * Get feature flag value
 */
export const getFeatureFlagValue = (flagKey: string): string | boolean | undefined => {
  if (!initialized) return undefined;
  
  return posthog.getFeatureFlag(flagKey);
};

/**
 * Track a feature flag evaluation
 */
export const trackFeatureFlag = (flagKey: string, value: any) => {
  if (!initialized) return;
  
  trackEvent('feature_flag_evaluated', {
    flag_key: flagKey,
    flag_value: value,
  });
};
