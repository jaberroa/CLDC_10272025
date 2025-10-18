/**
 * Custom Test Utilities
 * Provides common testing utilities and wrappers
 */

import { ReactElement } from 'react';
import { render, RenderOptions } from '@testing-library/react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { BrowserRouter } from 'react-router-dom';
import { HelmetProvider } from 'react-helmet-async';

/**
 * Create a test query client with no retries
 */
export const createTestQueryClient = () =>
  new QueryClient({
    defaultOptions: {
      queries: {
        retry: false,
        gcTime: 0,
      },
      mutations: {
        retry: false,
      },
    },
  });

/**
 * All the providers wrapper for testing
 */
interface AllProvidersProps {
  children: React.ReactNode;
}

export function AllProviders({ children }: AllProvidersProps) {
  const testQueryClient = createTestQueryClient();

  return (
    <HelmetProvider>
      <QueryClientProvider client={testQueryClient}>
        <BrowserRouter>{children}</BrowserRouter>
      </QueryClientProvider>
    </HelmetProvider>
  );
}

/**
 * Custom render function that includes all providers
 */
export function renderWithProviders(
  ui: ReactElement,
  options?: Omit<RenderOptions, 'wrapper'>
) {
  return render(ui, { wrapper: AllProviders, ...options });
}

// Re-export everything from testing-library
export * from '@testing-library/react';
export { default as userEvent } from '@testing-library/user-event';
export { renderWithProviders as render };
