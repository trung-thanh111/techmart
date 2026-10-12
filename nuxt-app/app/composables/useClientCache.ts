import { clientCache, type CacheDomain } from "~/utils/cache/clientCache";

/**
 * Composable useClientCache cung cấp tiện ích tương tác nhanh với cache UI
 */
export const useClientCache = () => {
  return {
    clientCache,
    get: <T>(key: string) => clientCache.get<T>(key),
    set: <T>(key: string, data: T, domain: CacheDomain, customTtlMs?: number) => clientCache.set(key, data, domain, customTtlMs),
    remember: <T>(key: string, domain: CacheDomain, fetcher: () => Promise<T>, customTtlMs?: number) =>
      clientCache.remember(key, domain, fetcher, customTtlMs),
    has: (key: string) => clientCache.has(key),
    delete: (key: string) => clientCache.delete(key),
    invalidateDomain: (domain: CacheDomain | CacheDomain[]) => clientCache.invalidateDomain(domain),
    invalidateByUrl: (url: string) => clientCache.invalidateByUrl(url),
    clearAll: () => clientCache.clearAll(),
    getStats: () => clientCache.getStats(),
  };
};
