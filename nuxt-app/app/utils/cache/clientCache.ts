/**
 * Client Cache Manager - Hệ thống quản lý Cache phía Client chuẩn nghiệp vụ
 *
 * Tính năng chính:
 * 1. Phân nhóm theo Business Domain (PRODUCT, CATALOGUE, POST, MENU, PROMOTION, AGENCY, FAQ, BANNER, SYSTEM, USER)
 * 2. TTL Jitter ngẫu nhiên để chống Cache Stampede (tránh hàng loạt key hết hạn cùng thời điểm)
 * 3. Hỗ trợ hiển thị tức thì (Cache-First / Instant Render), không gián đoạn giao diện hay nhấp nháy spinner khi chuyển trang
 * 4. Request Deduplication: tự động gộp các request giống nhau đang chạy dở
 * 5. Tự động đồng bộ và Invalidate toàn bộ domain liên quan khi có action CRUD từ BE API
 */

export type CacheDomain =
  | "PRODUCT"
  | "CATALOGUE"
  | "POST"
  | "MENU"
  | "PROMOTION"
  | "AGENCY"
  | "FAQ"
  | "BANNER"
  | "SYSTEM"
  | "USER";

export interface CacheEntry<T = any> {
  key: string;
  data: T;
  domain: CacheDomain;
  timestamp: number;
  expiresAt: number;
}

export interface DomainConfig {
  baseTtlMs: number;
  jitterMs: number;
}

export const DOMAIN_CONFIGS: Record<CacheDomain, DomainConfig> = {
  PRODUCT: { baseTtlMs: 5 * 60 * 1000, jitterMs: 60 * 1000 },
  CATALOGUE: { baseTtlMs: 10 * 60 * 1000, jitterMs: 120 * 1000 },
  POST: { baseTtlMs: 10 * 60 * 1000, jitterMs: 120 * 1000 },
  MENU: { baseTtlMs: 30 * 60 * 1000, jitterMs: 300 * 1000 },
  PROMOTION: { baseTtlMs: 5 * 60 * 1000, jitterMs: 60 * 1000 },
  AGENCY: { baseTtlMs: 30 * 60 * 1000, jitterMs: 300 * 1000 },
  FAQ: { baseTtlMs: 30 * 60 * 1000, jitterMs: 300 * 1000 },
  BANNER: { baseTtlMs: 15 * 60 * 1000, jitterMs: 180 * 1000 },
  SYSTEM: { baseTtlMs: 30 * 60 * 1000, jitterMs: 300 * 1000 },
  USER: { baseTtlMs: 2 * 60 * 1000, jitterMs: 30 * 1000 },
};

export class ClientCacheManager {
  private store = new Map<string, CacheEntry>();
  private inflight = new Map<string, Promise<any>>();
  private maxEntries = 600;
  private isBrowser = typeof window !== "undefined" && typeof window.sessionStorage !== "undefined";

  constructor() {
    this.hydrateFromSessionStorage();
  }

  /**
   * Tính thời gian hết hạn kèm random jitter để chống hiện tượng hết hạn đồng loạt
   */
  public calculateExpiresAt(domain: CacheDomain, customTtlMs?: number): number {
    const config = DOMAIN_CONFIGS[domain] || { baseTtlMs: 300000, jitterMs: 60000 };
    const base = customTtlMs ?? config.baseTtlMs;
    // Jitter phân phối ngẫu nhiên trong khoảng [-jitterMs / 2, +jitterMs / 2]
    const jitter = Math.floor((Math.random() - 0.5) * config.jitterMs);
    const ttl = customTtlMs !== undefined ? customTtlMs : Math.max(1000, base + jitter);
    return Date.now() + ttl;
  }

  /**
   * Lấy dữ liệu nếu cache còn hợp lệ
   */
  public get<T>(key: string): T | null {
    const entry = this.store.get(key);
    if (!entry) return null;

    if (Date.now() > entry.expiresAt) {
      this.store.delete(key);
      this.persistToSessionStorage(key, null);
      return null;
    }
    return entry.data as T;
  }

  /**
   * Lưu dữ liệu vào cache với domain tương ứng và TTL jitter
   */
  public set<T>(key: string, data: T, domain: CacheDomain, customTtlMs?: number): void {
    if (this.store.size >= this.maxEntries) {
      this.cleanup();
    }

    const expiresAt = this.calculateExpiresAt(domain, customTtlMs);
    const entry: CacheEntry<T> = {
      key,
      data,
      domain,
      timestamp: Date.now(),
      expiresAt,
    };

    this.store.set(key, entry);
    this.persistToSessionStorage(key, entry);
  }

  /**
   * Kiểm tra key có tồn tại và còn hạn không
   */
  public has(key: string): boolean {
    return this.get(key) !== null;
  }

  /**
   * Xóa 1 key cụ thể
   */
  public delete(key: string): boolean {
    const deleted = this.store.delete(key);
    this.persistToSessionStorage(key, null);
    return deleted;
  }

  /**
   * Remember: Cache-first wrapper kèm Request Deduplication
   * - Nếu cache hit: trả về lập tức (0ms network)
   * - Nếu cache miss: gọi fetcher, lưu cache rồi trả về
   * - Nếu đang có cùng key inflight: gộp chung request tránh gọi duplicate
   */
  public async remember<T>(
    key: string,
    domain: CacheDomain,
    fetcher: () => Promise<T>,
    customTtlMs?: number
  ): Promise<T> {
    const cached = this.get<T>(key);
    if (cached !== null) {
      return cached;
    }

    if (this.inflight.has(key)) {
      return this.inflight.get(key) as Promise<T>;
    }

    const promise = (async () => {
      try {
        const result = await fetcher();
        if (result !== undefined && result !== null) {
          this.set(key, result, domain, customTtlMs);
        }
        return result;
      } finally {
        this.inflight.delete(key);
      }
    })();

    this.inflight.set(key, promise);
    return promise;
  }

  /**
   * Invalidate toàn bộ cache của một hoặc nhiều Business Domain
   * Dùng khi có action CRUD từ BE hoặc mutation từ FE
   */
  public invalidateDomain(domain: CacheDomain | CacheDomain[]): number {
    const domains = Array.isArray(domain) ? domain : [domain];
    let count = 0;

    for (const [key, entry] of this.store.entries()) {
      if (domains.includes(entry.domain)) {
        this.store.delete(key);
        this.persistToSessionStorage(key, null);
        count++;
      }
    }
    return count;
  }

  /**
   * Tự động xóa cache theo URL mutation của API
   */
  public invalidateByUrl(url: string): number {
    const domains = mapUrlToDomains(url);
    if (domains.length > 0) {
      return this.invalidateDomain(domains);
    }
    return 0;
  }

  /**
   * Xóa toàn bộ cache
   */
  public clearAll(): void {
    this.store.clear();
    this.inflight.clear();
    if (this.isBrowser) {
      try {
        sessionStorage.removeItem("__techmart_cache__");
      } catch (e) {
        // Ignored
      }
    }
  }

  /**
   * Dọn dẹp các cache hết hạn
   */
  public cleanup(): void {
    const now = Date.now();
    for (const [key, entry] of this.store.entries()) {
      if (now > entry.expiresAt) {
        this.store.delete(key);
        this.persistToSessionStorage(key, null);
      }
    }
  }

  /**
   * Thống kê cache phục vụ theo dõi và debug
   */
  public getStats() {
    return {
      size: this.store.size,
      inflight: this.inflight.size,
      domains: Array.from(this.store.values()).reduce((acc, entry) => {
        acc[entry.domain] = (acc[entry.domain] || 0) + 1;
        return acc;
      }, {} as Record<string, number>),
    };
  }

  private hydrateFromSessionStorage(): void {
    if (!this.isBrowser) return;
    try {
      const raw = sessionStorage.getItem("__techmart_cache__");
      if (!raw) return;
      const parsed: Record<string, CacheEntry> = JSON.parse(raw);
      const now = Date.now();
      for (const [key, entry] of Object.entries(parsed)) {
        if (entry && entry.expiresAt > now) {
          this.store.set(key, entry);
        }
      }
    } catch (e) {
      // Ignored
    }
  }

  private persistToSessionStorage(key: string, entry: CacheEntry | null): void {
    if (!this.isBrowser) return;
    try {
      const raw = sessionStorage.getItem("__techmart_cache__");
      const obj: Record<string, CacheEntry> = raw ? JSON.parse(raw) : {};
      if (entry === null) {
        delete obj[key];
      } else {
        obj[key] = entry;
      }
      sessionStorage.setItem("__techmart_cache__", JSON.stringify(obj));
    } catch (e) {
      // Quota exceeded hoặc private browsing mode
    }
  }
}

/**
 * Ánh xạ URL sang Business Domain tương ứng
 */
export function mapUrlToDomains(url: string): CacheDomain[] {
  const u = (url || "").toLowerCase();
  const domains: CacheDomain[] = [];

  if (u.includes("/product_catalogues") || u.includes("/product/category")) {
    domains.push("CATALOGUE", "PRODUCT");
  } else if (u.includes("/products") || u.includes("/product-reviews") || u.includes("/wishlists")) {
    domains.push("PRODUCT");
  }

  if (u.includes("/post_catalogues") || u.includes("/posts") || u.includes("/post_comments") || u.includes("/post_likes")) {
    domains.push("POST");
  }

  if (u.includes("/menus") || u.includes("/menu")) {
    domains.push("MENU");
  }

  if (u.includes("/promotions") || u.includes("/promotion")) {
    domains.push("PROMOTION", "PRODUCT");
  }

  if (u.includes("/agencies") || u.includes("/agency")) {
    domains.push("AGENCY");
  }

  if (u.includes("/faqs") || u.includes("/faq_catalogues")) {
    domains.push("FAQ");
  }

  if (u.includes("/banners") || u.includes("/banner")) {
    domains.push("BANNER");
  }

  if (u.includes("/systems") || u.includes("/policy")) {
    domains.push("SYSTEM");
  }

  if (u.includes("/orders") || u.includes("/carts") || u.includes("/cart_items")) {
    domains.push("USER", "PRODUCT");
  }

  return Array.from(new Set(domains));
}

// Singleton xuất khẩu toàn cục
export const clientCache = new ClientCacheManager();
