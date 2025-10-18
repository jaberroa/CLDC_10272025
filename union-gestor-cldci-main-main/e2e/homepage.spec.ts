/**
 * Homepage E2E Tests
 * Tests for the main landing page
 */

import { test, expect } from '@playwright/test';

test.describe('Homepage', () => {
  test('should load successfully', async ({ page }) => {
    await page.goto('/');
    
    // Wait for page to be fully loaded
    await page.waitForLoadState('networkidle');
    
    // Check if the main heading is visible
    await expect(page.locator('h1')).toContainText('El Corazón Digital');
  });

  test('should have correct meta tags', async ({ page }) => {
    await page.goto('/');
    
    // Check title
    await expect(page).toHaveTitle(/El Corazón Digital/);
    
    // Check meta description
    const metaDescription = page.locator('meta[name="description"]');
    await expect(metaDescription).toHaveAttribute(
      'content',
      /Plataforma de gestión integral/
    );
  });

  test('should navigate to dashboard', async ({ page }) => {
    await page.goto('/');
    
    // Click on dashboard link (adjust selector based on your implementation)
    await page.click('text=Dashboard');
    
    // Verify navigation
    await expect(page).toHaveURL(/.*dashboard/);
  });

  test('should display feature modules', async ({ page }) => {
    await page.goto('/');
    
    // Check if feature modules are displayed
    await expect(page.locator('text=Censo y actualización de datos')).toBeVisible();
    await expect(page.locator('text=Votaciones electrónicas')).toBeVisible();
  });

  test('should be responsive on mobile', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/');
    
    // Check if content is visible on mobile
    await expect(page.locator('h1')).toBeVisible();
  });
});
