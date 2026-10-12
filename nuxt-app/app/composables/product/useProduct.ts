import type { Ref } from "vue";
import { productService } from "~/services/product/product.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProduct, ProductPayloadFilter } from "~/interfaces/product/product.interface";
import type { IProductTag } from "~/interfaces/product/tag.interface";
import { productTagService } from "~/services/product/tag.service";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { buildRelationParams } from "~/utils/helpers";
import { clientCache } from "~/utils/cache/clientCache";

export const useProduct = () => {
  const isLoading: Ref<boolean> = ref(false);
  const paginate: Ref<IApiOk<IPaginate<IProduct>> | null> = ref(null);
  const list: Ref<IProduct[]> = ref([]);
  const tagList: Ref<IProductTag[]> = ref([]);
  const productCatalogueList: Ref<IProductCatalogue[]> = ref([]);
  const hotCatalogueProducts = useState<IProduct[]>("hotCatalogueProducts", () => [] as IProduct[]);
  const byCatalogueCache = reactive<Record<number, IProduct[]>>({});
  const byCatalogueLoading = reactive<Record<number, boolean>>({});
  const byCatalogueError = reactive<Record<number, string | null>>({});
  const latestItems = ref<IProduct[]>([]);
  const latestPage = ref(1);
  const latestLastPage = ref(1);
  const latestLoading = ref(false);
  const hotQuickItems = ref<IProduct[]>([]);
  const hotQuickPage = ref(1);
  const hotQuickLastPage = ref(1);
  const hotQuickLoading = ref(false);
  const productDetail = ref<IProduct | null>(null);
  const productDetailLoading = ref(false);
  const productDetailError = ref<string | null>(null);

  // Cache flags for optimization using useState
  const isTagsLoaded = useState("product-tags-loaded", () => false);
  const isCataloguesLoaded = useState("product-catalogues-loaded", () => false);
  const isLatestLoaded = useState("product-latest-loaded", () => false);
  const isHotQuickLoaded = useState("product-hot-quick-loaded", () => false);

  /**
   * Lấy bài viết theo paginate
   */
  const fetchPaginate = async (filters: ProductPayloadFilter, page = 1, perPage = 10) => {
    isLoading.value = true;
    try {
      const response = await productService.pagination({
        ...filters,
        page,
        perpage: perPage,
      });
      paginate.value = response;
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load paginate:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy danh sách bài viết (featured, latest...)
   */
  const fetchList = async (option: "featured" | "featuredLimit" | "latest"): Promise<IProduct[]> => {
    const cacheKey = `product:list:${option}`;
    const cached = clientCache.get<IProduct[]>(cacheKey);
    if (cached) {
      list.value = cached;
      return cached;
    }
    isLoading.value = true;
    try {
      let response: IApiOk<IProduct[]>;
      switch (option) {
        case "featured":
          response = await productService.getFeatured();
          break;
        case "featuredLimit":
          response = await productService.getFeaturedLimit();
          break;
        case "latest":
          response = await productService.getLatest();
          break;
        default:
          throw new Error(`Unknown type: ${option}`);
      }
      const data = response.data ?? [];
      list.value = data;
      clientCache.set(cacheKey, data, "PRODUCT");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchTags = async (): Promise<IProductTag[]> => {
    const cached = clientCache.get<IProductTag[]>("product:tags:all");
    if (cached) {
      tagList.value = cached;
      isTagsLoaded.value = true;
      return cached;
    }
    isLoading.value = true;
    try {
      const response = await productTagService.all();
      const data = response.data ?? [];
      tagList.value = data;
      isTagsLoaded.value = true;
      clientCache.set("product:tags:all", data, "PRODUCT");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load tag list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProductCatalogues = async (): Promise<IProductCatalogue[]> => {
    const cached = clientCache.get<IProductCatalogue[]>("catalogue:all");
    if (cached) {
      productCatalogueList.value = cached;
      isCataloguesLoaded.value = true;
      return cached;
    }
    isLoading.value = true;
    try {
      const response = await productCatalogueService.all();
      const data = response.data ?? [];
      productCatalogueList.value = data;
      isCataloguesLoaded.value = true;
      clientCache.set("catalogue:all", data, "CATALOGUE");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load product catalogue list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchHotCatalogueProducts = async (): Promise<IProduct[]> => {
    const cached = clientCache.get<IProduct[]>("product:hotCatalogue");
    if (cached) {
      hotCatalogueProducts.value = cached;
      return cached;
    }
    try {
      const response = await productService.getByHotCatalogue();
      const data = response.data ?? [];
      hotCatalogueProducts.value = data;
      clientCache.set("product:hotCatalogue", data, "PRODUCT");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load danh sách bài viết theo danh mục nổi bật:", error);
      hotCatalogueProducts.value = [];
      return hotCatalogueProducts.value;
    }
  };

  const fetchProductsByCatalogueId = async (catalogueId: number): Promise<IProduct[]> => {
    if (!catalogueId || Number.isNaN(catalogueId)) return [];
    const cacheKey = `product:byCatalogue:${catalogueId}`;
    const cached = clientCache.get<IProduct[]>(cacheKey) || byCatalogueCache[catalogueId];
    if (cached?.length) {
      byCatalogueCache[catalogueId] = cached;
      return cached;
    }
    byCatalogueLoading[catalogueId] = true;
    byCatalogueError[catalogueId] = null;
    try {
      const response = await productService.getByCatalogueId(catalogueId);
      const data = response.data ?? [];
      byCatalogueCache[catalogueId] = data;
      clientCache.set(cacheKey, data, "PRODUCT");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load bài theo catalogue id:", error);
      byCatalogueError[catalogueId] = error?.message ?? "Unknown error";
      return [];
    } finally {
      byCatalogueLoading[catalogueId] = false;
    }
  };

  const loadLatest = async () => {
    if (latestLoading.value) return;
    if (isLatestLoaded.value && latestPage.value === 1) return;
    latestLoading.value = true;
    try {
      const res = await fetchPaginate({ keyword: "", perpage: 5, sort: "published_at,desc" }, latestPage.value, 5);
      const pg = res?.data;
      if (pg) {
        latestItems.value = [...latestItems.value, ...pg.data];
        latestLastPage.value = pg.last_page;
        latestPage.value += 1;
        if (latestPage.value === 2) isLatestLoaded.value = true;
      }
    } finally {
      latestLoading.value = false;
    }
  };

  const loadHotQuick = async () => {
    if (hotQuickLoading.value) return;
    if (isHotQuickLoaded.value && hotQuickPage.value === 1) return;
    hotQuickLoading.value = true;
    try {
      const relation = buildRelationParams({ product_catalogues: 1 }, "is_hot");
      const res = await productService.pagination({
        ...relation,
        page: hotQuickPage.value,
        perpage: 6,
      });
      const pg: any = res?.data;
      const pageItems: IProduct[] = Array.isArray(pg?.data) ? pg.data : [];
      if (pageItems.length > 0) {
        hotQuickItems.value = [...hotQuickItems.value, ...pageItems];
        hotQuickLastPage.value = typeof pg?.last_page === "number" ? pg.last_page : hotQuickLastPage.value;
        hotQuickPage.value = hotQuickPage.value + 1;
        if (hotQuickPage.value === 2) isHotQuickLoaded.value = true;
      }
    } finally {
      hotQuickLoading.value = false;
    }
  };

  const fetchById = async (slug: string): Promise<IProduct | null> => {
    if (!slug) return null;
    const cacheKey = `product:detail:${slug}`;
    const cached = clientCache.get<IProduct>(cacheKey);
    if (cached) {
      productDetail.value = cached;
      productDetailLoading.value = false;
      return cached;
    }
    productDetailLoading.value = true;
    productDetailError.value = null;
    try {
      const res = await productService.show(slug);
      const val = (res as any)?.data ?? null;
      productDetail.value = val;
      if (val) {
        clientCache.set(cacheKey, val, "PRODUCT");
      }
      return productDetail.value;
    } catch (e: any) {
      productDetailError.value = e?.message || "Không tải được bài viết";
      productDetail.value = null;
      return null;
    } finally {
      productDetailLoading.value = false;
    }
  };

  const trackView = async (productId: number): Promise<void> => {
    try {
      const currentViewCount = productDetail.value?.viewCount || 0;
      const newViewCount = currentViewCount + 1;
      await productService.incrementViewCount(productId, newViewCount);
      if (productDetail.value) {
        productDetail.value.viewCount = newViewCount;
      }
    } catch (error) {
      console.log("Error tracking view:", error);
    }
  };

  const mostLikedItems = ref<IProduct[]>([]);
  const mostLikedLoading = ref(false);

  const fetchMostLiked = async (): Promise<IProduct[]> => {
    const cached = clientCache.get<IProduct[]>("product:mostLiked");
    if (cached) {
      mostLikedItems.value = cached;
      return cached;
    }
    mostLikedLoading.value = true;
    try {
      const response = await productService.getMostLiked();
      const data = response.data ?? [];
      mostLikedItems.value = data;
      clientCache.set("product:mostLiked", data, "PRODUCT");
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load most liked products:", error);
      throw error;
    } finally {
      mostLikedLoading.value = false;
    }
  };

  return {
    isLoading,
    paginate,
    list,
    tagList,
    productCatalogueList,
    hotCatalogueProducts,
    byCatalogueCache,
    byCatalogueLoading,
    byCatalogueError,
    latestItems,
    latestPage,
    latestLastPage,
    latestLoading,
    hotQuickItems,
    hotQuickPage,
    hotQuickLastPage,
    hotQuickLoading,
    productDetail,
    productDetailLoading,
    productDetailError,
    mostLikedItems,
    mostLikedLoading,
    fetchPaginate,
    fetchList,
    fetchTags,
    loadLatest,
    loadHotQuick,
    fetchById,
    fetchProductCatalogues,
    fetchHotCatalogueProducts,
    fetchProductsByCatalogueId,
    fetchMostLiked,
    trackView,
  };
};
