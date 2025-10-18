import { QueryClient } from '@tanstack/react-query';

/**
 * Optimized React Query configuration
 * - Smart refetch strategies
 * - Automatic retry with exponential backoff
 * - Network-aware caching
 */
export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 5 * 60 * 1000,
      gcTime: 10 * 60 * 1000,
      refetchOnWindowFocus: true,
      refetchOnReconnect: true,
      retry: 3,
      retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
      networkMode: 'online',
    },
    mutations: {
      retry: 1,
      retryDelay: 1000,
      networkMode: 'online',
    },
  },
});

export function prefetchOnHover<T>(
  queryKey: string | string[],
  queryFn: () => Promise<T>
) {
  void queryClient.prefetchQuery({
    queryKey: Array.isArray(queryKey) ? queryKey : [queryKey],
    queryFn,
    staleTime: 5 * 60 * 1000,
  });
}

export function invalidateQuery(queryKey: string | string[]) {
  return queryClient.invalidateQueries({
    queryKey: Array.isArray(queryKey) ? queryKey : [queryKey],
  });
}
