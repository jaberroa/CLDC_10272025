/**
 * Feature Flags System
 * Simple client-side feature toggle system with localStorage persistence
 * For production, consider using PostHog or LaunchDarkly
 */

export interface FeatureFlag {
  key: string;
  name: string;
  description: string;
  enabled: boolean;
  defaultValue: boolean;
}

// Define your feature flags here
export const FEATURE_FLAGS: Record<string, FeatureFlag> = {
  NEW_DASHBOARD: {
    key: 'new_dashboard',
    name: 'New Dashboard',
    description: 'Enable the redesigned dashboard with new features',
    enabled: false,
    defaultValue: false,
  },
  ADVANCED_REPORTING: {
    key: 'advanced_reporting',
    name: 'Advanced Reporting',
    description: 'Enable advanced reporting and analytics features',
    enabled: false,
    defaultValue: false,
  },
  REAL_TIME_NOTIFICATIONS: {
    key: 'real_time_notifications',
    name: 'Real-time Notifications',
    description: 'Enable real-time push notifications',
    enabled: true,
    defaultValue: true,
  },
  EXPERIMENTAL_FEATURES: {
    key: 'experimental_features',
    name: 'Experimental Features',
    description: 'Enable experimental and beta features',
    enabled: false,
    defaultValue: false,
  },
};

const STORAGE_KEY = 'cldci_feature_flags';

/**
 * Get all feature flags from localStorage
 */
const getStoredFlags = (): Record<string, boolean> => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored ? JSON.parse(stored) : {};
  } catch {
    return {};
  }
};

/**
 * Save feature flags to localStorage
 */
const saveFlags = (flags: Record<string, boolean>) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(flags));
  } catch (error) {
    console.error('Error saving feature flags:', error);
  }
};

/**
 * Check if a feature flag is enabled
 */
export const isFeatureFlagEnabled = (flagKey: string): boolean => {
  const storedFlags = getStoredFlags();
  const flag = FEATURE_FLAGS[flagKey];
  
  if (!flag) {
    console.warn(`Feature flag "${flagKey}" not found`);
    return false;
  }
  
  // Check localStorage first, fallback to flag's enabled state, then default value
  return storedFlags[flagKey] ?? flag.enabled ?? flag.defaultValue;
};

/**
 * Enable a feature flag
 */
export const enableFeatureFlag = (flagKey: string) => {
  const storedFlags = getStoredFlags();
  storedFlags[flagKey] = true;
  saveFlags(storedFlags);
};

/**
 * Disable a feature flag
 */
export const disableFeatureFlag = (flagKey: string) => {
  const storedFlags = getStoredFlags();
  storedFlags[flagKey] = false;
  saveFlags(storedFlags);
};

/**
 * Toggle a feature flag
 */
export const toggleFeatureFlag = (flagKey: string): boolean => {
  const currentState = isFeatureFlagEnabled(flagKey);
  const newState = !currentState;
  
  if (newState) {
    enableFeatureFlag(flagKey);
  } else {
    disableFeatureFlag(flagKey);
  }
  
  return newState;
};

/**
 * Get all feature flags with their current state
 */
export const getAllFeatureFlags = (): FeatureFlag[] => {
  const storedFlags = getStoredFlags();
  
  return Object.values(FEATURE_FLAGS).map((flag) => ({
    ...flag,
    enabled: storedFlags[flag.key] ?? flag.enabled,
  }));
};

/**
 * Reset all feature flags to default values
 */
export const resetFeatureFlags = () => {
  localStorage.removeItem(STORAGE_KEY);
};

/**
 * Export feature flags for debugging
 */
export const exportFeatureFlags = (): string => {
  const flags = getStoredFlags();
  return JSON.stringify(flags, null, 2);
};

/**
 * Import feature flags from JSON
 */
export const importFeatureFlags = (json: string) => {
  try {
    const flags = JSON.parse(json);
    saveFlags(flags);
    return true;
  } catch {
    console.error('Invalid feature flags JSON');
    return false;
  }
};
