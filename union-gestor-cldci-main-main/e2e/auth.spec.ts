/**
 * Authentication E2E Tests
 * Tests for login, logout, and protected routes
 */

import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
  test('should redirect to login when accessing protected route', async ({ page }) => {
    // Try to access a protected route without authentication
    await page.goto('/dashboard');
    
    // Should be redirected to login or show login prompt
    // Adjust this based on your authentication flow
    await expect(page.url()).toContain('auth');
  });

  test('should show validation errors for invalid credentials', async ({ page }) => {
    await page.goto('/');
    
    // Attempt login with invalid credentials (adjust selectors based on your form)
    await page.fill('input[type="email"]', 'invalid@example.com');
    await page.fill('input[type="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    
    // Check for error message
    await expect(page.locator('text=/error|invalid|incorrect/i')).toBeVisible();
  });

  test.skip('should login successfully with valid credentials', async ({ page }) => {
    // Skip this test in CI unless you have test credentials
    await page.goto('/');
    
    // Fill in valid test credentials
    await page.fill('input[type="email"]', 'test@example.com');
    await page.fill('input[type="password"]', 'testpassword123');
    await page.click('button[type="submit"]');
    
    // Should redirect to dashboard or home
    await expect(page).toHaveURL(/.*dashboard|\/$/);
  });
});
