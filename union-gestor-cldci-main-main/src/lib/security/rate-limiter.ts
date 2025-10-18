/**
 * Client-side rate limiting utility
 * Helps prevent abuse by limiting rapid successive requests
 * Note: This is client-side only - server-side rate limiting should be implemented in edge functions
 */

interface RateLimitConfig {
  maxRequests: number;
  windowMs: number;
}

class RateLimiter {
  private requests: Map<string, number[]> = new Map();

  /**
   * Check if a request should be allowed based on rate limits
   * @param key - Unique identifier for the rate limit (e.g., 'login', 'api-call')
   * @param config - Rate limit configuration
   * @returns true if request is allowed, false if rate limit exceeded
   */
  checkLimit(key: string, config: RateLimitConfig): boolean {
    const now = Date.now();
    const timestamps = this.requests.get(key) || [];

    // Remove timestamps outside the current window
    const validTimestamps = timestamps.filter(
      (timestamp) => now - timestamp < config.windowMs
    );

    // Check if we're at the limit
    if (validTimestamps.length >= config.maxRequests) {
      return false;
    }

    // Add current timestamp
    validTimestamps.push(now);
    this.requests.set(key, validTimestamps);

    return true;
  }

  /**
   * Reset rate limit for a specific key
   */
  reset(key: string): void {
    this.requests.delete(key);
  }

  /**
   * Clear all rate limits
   */
  clearAll(): void {
    this.requests.clear();
  }
}

// Singleton instance
export const rateLimiter = new RateLimiter();

// Common rate limit configurations
export const rateLimitConfigs = {
  login: { maxRequests: 5, windowMs: 15 * 60 * 1000 }, // 5 attempts per 15 minutes
  api: { maxRequests: 60, windowMs: 60 * 1000 }, // 60 requests per minute
  vote: { maxRequests: 1, windowMs: 60 * 1000 }, // 1 vote per minute
  registration: { maxRequests: 3, windowMs: 60 * 60 * 1000 }, // 3 registrations per hour
};
