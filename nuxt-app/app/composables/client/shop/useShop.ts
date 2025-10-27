import { bannerService } from "~/services/banner/banner.service";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { productService } from "~/services/product/product.service";
import { postService } from "~/services/post/post.service";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IPaginate } from "~/interfaces/base.interface";
import type { IPost } from "~/interfaces/post/post.interface";

export const useShop = () => {
  const config = useRuntimeConfig();

  // Global state (persist across routes)
  const banners = useState<IBanner[]>("shop-banners", () => []);
  const catalogues = useState<IProductCatalogue[]>("shop-catalogues", () => []);
  const products = useState<IProduct[]>("shop-products", () => []);
  const summerProducts = useState<IProduct[]>("shop-summer", () => []);
  const posts = useState<IPost[]>("shop-posts", () => []);
  const bannerSeason = useState<IBanner[]>("shop-banner-season", () => []);

  const filters = useState<Record<string, any>>("shop-filters", () => ({
    sort: "created_at,desc",
    product_variants: {
      variant_attributes: {
        "attributes.id": {
          in: "",
        },
      },
    },
    price_origin: {
      between: "0,100000000",
    },
    brand_id: {
      eq: null,
    },
  }));

  // Pagination / flags
  const paginate = useState<IPaginate<IProduct> | null>("shop-paginate", () => null);
  const totalProducts = useState<number>("shop-total-products", () => 0);
  const currentPage = useState<number>("shop-current-page", () => 1);
  const hasMore = useState<boolean>("shop-has-more", () => true);

  // Loading flag (local)
  const isLoading = ref(false);
  const isInitialized = useState<boolean>("shop-initialized", () => false);
  // Request sequence id to avoid race conditions (older responses overwriting newer ones)
  const requestSeq = ref(0);

  const fetchBanners = async () => {
    if (banners.value.length) return banners.value;
    const { data } = await bannerService.fetch({
      type: "all",
      publish: 2,
      sort: "id,desc",
      banner_type: "shop",
    });
    banners.value = Array.isArray(data) ? data : [data];
    return banners.value;
  };

  const fetchCatalogues = async () => {
    if (catalogues.value.length) return catalogues.value;
    const { data } = await productCatalogueService.all();
    catalogues.value = Array.isArray(data) ? data : [data];
    return catalogues.value;
  };

  const fetchProducts = async (page = 1, perPage = 8, append = false) => {
    // Nếu không append và là trang 1 và đã có dữ liệu => không fetch lại
    if (!append && page === 1 && products.value.length && !filters.value.sort) {
      return products.value;
    }

    isLoading.value = true;
    try {
      // Chuẩn bị params gửi lên API
      const params: Record<string, any> = {
        publish: 2,
        page,
        perpage: perPage,
        sort: filters.value.sort || null,
      };

      // Gom filter
      const filterParams: Record<string, any> = {};

      if (filters.value.product_variants?.variant_attributes?.["attributes.id"]?.in?.length) {
        filterParams.product_variants = filters.value.product_variants;
      }

      if (filters.value.price_origin) {
        filterParams.price_origin = filters.value.price_origin;
      }

      if (filters.value.brand_id?.eq !== null) {
        filterParams.brand_id = filters.value.brand_id;
      }

      params.filters = JSON.stringify(filterParams);

      // Mark this request with a sequence id to avoid races
      requestSeq.value += 1;
      const thisRequest = requestSeq.value;

      // Gọi API
      const res = await productService.paginationPublic(params);

      // If a newer request started after this one, ignore this response
      if (thisRequest !== requestSeq.value) {
        // do not apply stale response
        return products.value;
      }

      const data = res?.data || {};
      const list = Array.isArray(data.data) ? data.data : [];
      const total = Number(data.total || 0);

      paginate.value = data;
      totalProducts.value = total;

      // Nếu không có sản phẩm thì reset danh sách
      if (list.length === 0) {
        if (page === 1) products.value = [];
        hasMore.value = false;
        currentPage.value = page;
        return products.value;
      }

      // Cập nhật danh sách
      products.value = append ? [...products.value, ...list] : list;

      // Tính toán có còn dữ liệu nữa không
      const loaded = products.value.length;
      hasMore.value = loaded < total;

      currentPage.value = page;

      return products.value;
    } catch (error) {
      console.error("[useShop] fetchProducts error:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Hàm set sort
   * Khi người dùng chọn sort mới, reset về trang 1 và fetch lại
   * @param sortValue - Giá trị sort mới (vd: "price_origin,desc")
   */
  const setSort = async (sortValue: string) => {
    // Nếu sort value giống với sort hiện tại => không làm gì
    if (filters.value.sort === sortValue) return;

    // Cập nhật sort value
    filters.value.sort = sortValue;

    // Reset về trang 1 và fetch lại danh sách
    currentPage.value = 1;
    await fetchProducts(1, 8, false);
  };

  /**
   * Hàm set filter (chuẩn bị cho tương lai)
   * @param filterKey - Key của filter (vd: "brand_id", "price_origin")
   * @param filterValue - Giá trị filter
   */
  const setFilter = async (filterKey: string, filterValue: any) => {
    switch (filterKey) {
      case "attributes":
        // Xử lý attributes filter
        const selectedIds = Object.values(filterValue).flat().filter(Boolean);
        if (selectedIds.length) {
          filters.value.product_variants.variant_attributes["attributes.id"].in = selectedIds.join(",");
        } else {
          filters.value.product_variants.variant_attributes["attributes.id"].in = "";
        }
        break;
      case "price":
        // Xử lý price filter
        const [min, max] = filterValue;
        filters.value.price_origin.beetwen = `${min},${max}`;
        break;
      case "brand":
        // Xử lý brand filter
        filters.value.brand_id.eq = filterValue || null;
        break;
      default:
        console.warn(`Unknown filter key: ${filterKey}`);
        return;
    }

    // Reset về trang 1 và fetch lại
    currentPage.value = 1;
    await fetchProducts(1, 8, false);
  };

  const resetFilters = async () => {
    filters.value = {
      sort: "created_at,desc",
    };
    currentPage.value = 1;
    await fetchProducts(1, 8, false);
  };

  const loadMore = async () => {
    if (!hasMore.value || isLoading.value) return;
    await fetchProducts(currentPage.value + 1, 8, true);
  };

  const fetchSummerProducts = async (limit = 3) => {
    if (summerProducts.value.length) return summerProducts.value;
    const { data } = await productService.getSummerProducts(limit);
    summerProducts.value = Array.isArray(data?.data) ? data.data : [];
    return summerProducts.value;
  };

  const fetchPosts = async () => {
    if (posts.value.length) return posts.value;
    const { data } = await postService.paginationPublic({
      type: "all",
      publish: 2,
      sort: "id,desc",
      post_catalogue_id: 1,
      limit: 5,
    });
    posts.value = Array.isArray(data) ? data : [data];
    return posts.value;
  };

  const fetchBannerSeason = async () => {
    if (bannerSeason.value.length) return bannerSeason.value;
    const { data } = await bannerService.fetch({
      banner_type: "shop",
      publish: 2,
      sort: "id,desc",
      limit: 1,
    });
    bannerSeason.value = Array.isArray(data) ? data : [data];
    return bannerSeason.value;
  };

  const getBannerImageUrl = (path: string) => (path?.startsWith("uploads") ? `${config.public.baseUrl}/storage/${path}` : path || IMAGEDEFAULT);

  const initializeData = async () => {
    if (isInitialized.value) return;
    isLoading.value = true;
    try {
      await Promise.all([fetchBanners(), fetchCatalogues(), fetchProducts(1, 8), fetchSummerProducts(), fetchPosts(), fetchBannerSeason()]);
      isInitialized.value = true;
    } catch (err) {
      console.error("[useShop] Init failed:", err);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    // state
    banners,
    catalogues,
    products,
    summerProducts,
    posts,
    bannerSeason,
    paginate,
    totalProducts,
    currentPage,
    hasMore,
    isLoading,
    filters,

    // methods
    fetchBanners,
    fetchCatalogues,
    fetchProducts,
    fetchSummerProducts,
    fetchPosts,
    fetchBannerSeason,
    loadMore,
    initializeData,
    getBannerImageUrl,

    setSort,
    setFilter,
    resetFilters,
  };
};
