import type { Ref } from "vue";
import { productService } from "~/services/product/product.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProduct, ProductPayloadFilter } from "~/interfaces/product/product.interface";
import type { IProductTag } from "~/interfaces/product/tag.interface";
import { productTagService } from "~/services/product/tag.service";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { buildRelationParams } from "~/utils/helpers";

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
      const response = await productService.paginationPublic({
        ...filters,
        publish: 2,
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
      list.value = response.data ?? [];
      return list.value;
    } catch (error: any) {
      console.error("Lỗi khi load list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchTags = async (): Promise<IProductTag[]> => {
    if (isTagsLoaded.value) return tagList.value; // Cache hit
    isLoading.value = true;
    try {
      const response = await productTagService.all();
      tagList.value = response.data ?? [];
      isTagsLoaded.value = true;
      return tagList.value;
    } catch (error: any) {
      console.error("Lỗi khi load tag list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProductCatalogues = async (): Promise<IProductCatalogue[]> => {
    if (isCataloguesLoaded.value) return productCatalogueList.value; // Cache hit
    isLoading.value = true;
    try {
      const response = await productCatalogueService.all();
      productCatalogueList.value = response.data ?? [];
      isCataloguesLoaded.value = true;
      return productCatalogueList.value;
    } catch (error: any) {
      console.error("Lỗi khi load product catalogue list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchHotCatalogueProducts = async (): Promise<IProduct[]> => {
    try {
      const response = await productService.getByHotCatalogue();
      hotCatalogueProducts.value = response.data ?? [];
      return hotCatalogueProducts.value;
    } catch (error: any) {
      console.error("Lỗi khi load danh sách bài viết theo danh mục nổi bật:", error);
      hotCatalogueProducts.value = [];
      return hotCatalogueProducts.value;
    }
  };

  const fetchProductsByCatalogueId = async (catalogueId: number): Promise<IProduct[]> => {
    if (!catalogueId || Number.isNaN(catalogueId)) return [];
    if (byCatalogueCache[catalogueId]?.length) return byCatalogueCache[catalogueId] ?? [];
    byCatalogueLoading[catalogueId] = true;
    byCatalogueError[catalogueId] = null;
    try {
      const response = await productService.getByCatalogueId(catalogueId);
      const data = response.data ?? [];
      byCatalogueCache[catalogueId] = data;
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
    if (isLatestLoaded.value && latestPage.value === 1) return; // Cache hit for first page
    latestLoading.value = true;
    try {
      const res = await fetchPaginate({ keyword: "", perpage: 5, publish: 2, sort: "published_at,desc" }, latestPage.value, 5);
      const pg = res?.data;
      if (pg) {
        latestItems.value = [...latestItems.value, ...pg.data];
        latestLastPage.value = pg.last_page;
        latestPage.value += 1;
        if (latestPage.value === 2) isLatestLoaded.value = true; // Mark as loaded after first page
      }
    } finally {
      latestLoading.value = false;
    }
  };

  const loadHotQuick = async () => {
    if (hotQuickLoading.value) return;
    if (isHotQuickLoaded.value && hotQuickPage.value === 1) return; // Cache hit for first page
    hotQuickLoading.value = true;
    try {
      const relation = buildRelationParams({ product_catalogues: 1 }, "is_hot");
      const res = await productService.paginationPublic({
        publish: 2,
        sort: "id,desc",
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
        if (hotQuickPage.value === 2) isHotQuickLoaded.value = true; // Mark as loaded after first page
      }
    } finally {
      hotQuickLoading.value = false;
    }
  };

  const fetchDetailBySlug = async (slug: string): Promise<IProduct | null> => {
    if (!slug) return null;
    productDetailLoading.value = true;
    productDetailError.value = null;
    try {
      const res = await productService.showBySlug(slug);
      productDetail.value = (res as any)?.data ?? null;
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
    mostLikedLoading.value = true;
    try {
      const response = await productService.getMostLiked();
      mostLikedItems.value = response.data ?? [];
      return mostLikedItems.value;
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
    fetchDetailBySlug,
    fetchProductCatalogues,
    fetchHotCatalogueProducts,
    fetchProductsByCatalogueId,
    fetchMostLiked,
    trackView,
  };
};
