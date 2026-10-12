import test from "node:test";
import assert from "node:assert/strict";
import { ClientCacheManager, mapUrlToDomains, DOMAIN_CONFIGS } from "../../app/utils/cache/clientCache.ts";

test("ClientCacheManager - TTL and Jitter calculation", () => {
  const manager = new ClientCacheManager();
  const domain = "PRODUCT";
  const config = DOMAIN_CONFIGS[domain];

  const now = Date.now();
  const expiresAt = manager.calculateExpiresAt(domain);
  const diff = expiresAt - now;

  // TTL nằm trong khoảng baseTtl ± (jitter / 2)
  const minExpected = config.baseTtlMs - config.jitterMs / 2 - 100;
  const maxExpected = config.baseTtlMs + config.jitterMs / 2 + 100;

  assert.ok(diff >= minExpected, `diff ${diff} should be >= ${minExpected}`);
  assert.ok(diff <= maxExpected, `diff ${diff} should be <= ${maxExpected}`);
});

test("ClientCacheManager - set and get cache", () => {
  const manager = new ClientCacheManager();
  manager.set("test:product:1", { id: 1, name: "Laptop Dell" }, "PRODUCT");

  const cached = manager.get<{ id: number; name: string }>("test:product:1");
  assert.deepEqual(cached, { id: 1, name: "Laptop Dell" });
});

test("ClientCacheManager - expired key returns null", () => {
  const manager = new ClientCacheManager();
  // Set custom TTL âm để hết hạn ngay lập tức
  manager.set("test:expired", { data: "old" }, "SYSTEM", -1000);

  const cached = manager.get("test:expired");
  assert.equal(cached, null);
  assert.equal(manager.has("test:expired"), false);
});

test("ClientCacheManager - delete key", () => {
  const manager = new ClientCacheManager();
  manager.set("test:del", 123, "CATALOGUE");
  assert.equal(manager.has("test:del"), true);

  const deleted = manager.delete("test:del");
  assert.equal(deleted, true);
  assert.equal(manager.has("test:del"), false);
});

test("ClientCacheManager - remember with deduplication", async () => {
  const manager = new ClientCacheManager();
  let fetchCount = 0;

  const mockFetcher = async () => {
    fetchCount++;
    await new Promise((r) => setTimeout(r, 50));
    return { title: "Deduplicated Article" };
  };

  // Hai request đồng thời gọi cùng key
  const [res1, res2] = await Promise.all([
    manager.remember("post:1", "POST", mockFetcher),
    manager.remember("post:1", "POST", mockFetcher),
  ]);

  assert.equal(fetchCount, 1, "Fetcher chỉ được gọi 1 lần duy nhất");
  assert.deepEqual(res1, { title: "Deduplicated Article" });
  assert.deepEqual(res2, { title: "Deduplicated Article" });

  // Gọi lần 3 sau khi đã xong - nhận từ cache, không tăng fetchCount
  const res3 = await manager.remember("post:1", "POST", mockFetcher);
  assert.equal(fetchCount, 1);
  assert.deepEqual(res3, { title: "Deduplicated Article" });
});

test("ClientCacheManager - invalidateDomain", () => {
  const manager = new ClientCacheManager();
  manager.set("prod:1", "Product 1", "PRODUCT");
  manager.set("prod:2", "Product 2", "PRODUCT");
  manager.set("cat:1", "Catalogue 1", "CATALOGUE");
  manager.set("menu:main", "Menu Main", "MENU");

  const purgedCount = manager.invalidateDomain(["PRODUCT", "CATALOGUE"]);
  assert.equal(purgedCount, 3);

  assert.equal(manager.get("prod:1"), null);
  assert.equal(manager.get("prod:2"), null);
  assert.equal(manager.get("cat:1"), null);
  assert.equal(manager.get("menu:main"), "Menu Main", "Domain MENU vẫn còn nguyên");
});

test("mapUrlToDomains - correctly maps URLs to Business Domains", () => {
  assert.deepEqual(mapUrlToDomains("/api/v1/product_catalogues/1"), ["CATALOGUE", "PRODUCT"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/products"), ["PRODUCT"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/menus/1"), ["MENU"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/posts/5"), ["POST"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/promotions"), ["PROMOTION", "PRODUCT"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/agencies"), ["AGENCY"]);
  assert.deepEqual(mapUrlToDomains("/api/v1/orders"), ["USER", "PRODUCT"]);
});

test("ClientCacheManager - invalidateByUrl", () => {
  const manager = new ClientCacheManager();
  manager.set("prod:detail:laptop", { name: "Laptop" }, "PRODUCT");
  manager.set("cat:detail:laptop", { name: "Catalogue Laptop" }, "CATALOGUE");
  manager.set("post:1", { title: "Post 1" }, "POST");

  const purged = manager.invalidateByUrl("/api/v1/product_catalogues/10");
  assert.equal(purged, 2);
  assert.equal(manager.get("prod:detail:laptop"), null);
  assert.equal(manager.get("cat:detail:laptop"), null);
  assert.notEqual(manager.get("post:1"), null);
});
