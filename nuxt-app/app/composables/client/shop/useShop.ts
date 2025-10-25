import { bannerService } from "~/services/banner/banner.service";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { productService } from "~/services/product/product.service";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IPaginate } from "~/interfaces/base.interface";
import { postService } from "~/services/post/post.service";

export const useShop = () => {
  const config = useRuntimeConfig();
  const bannerShopTops = ref<IBanner[]>([]);
  const productCatalogueList = ref<IProductCatalogue[]>([]);
  const products = ref<IProduct[]>([]);
  const summerProducts = ref<IProduct[]>([]);
  const paginate = ref<IPaginate<IProduct> | null>(null);
  const totalProducts = ref(0);
  const currentPage = ref(1);
  const hasMoreProducts = ref(true);
  const isLoading = ref(false);

  // Cache flags for optimization using useState
  const isBannersLoaded = useState("shop-banners-loaded", () => false);
  const isCataloguesLoaded = useState("shop-catalogues-loaded", () => false);
  const isProductsLoaded = useState("shop-products-loaded", () => false);
  const isSummerLoaded = useState("shop-summer-loaded", () => false);

  const fetchBanners = async () => {
    if (isBannersLoaded.value) return; // Cache hit
    try {
      const response = await bannerService.fetch({
        type: "all",
        publish: 2,
        sort: "id,desc",
        banner_type: "shop",
      });
      const data = (response?.data as any) ?? [];
      bannerShopTops.value = Array.isArray(data) ? data : [data];
      isBannersLoaded.value = true;
      return response;
    } catch (error) {
      console.error("Lỗi fetch shop banners:", error);
      bannerShopTops.value = [];
      throw error;
    }
  };

  const fetchProductCatalogues = async (): Promise<IProductCatalogue[]> => {
    if (isCataloguesLoaded.value) return productCatalogueList.value; // Cache hit
    try {
      const response = await productCatalogueService.all();
      const data = (response as any)?.data ?? [];
      productCatalogueList.value = Array.isArray(data) ? data : [data];
      isCataloguesLoaded.value = true;
      return productCatalogueList.value;
    } catch (error: any) {
      console.error("Lỗi khi load catalogues:", error);
      throw error;
    }
  };

  const fetchProducts = async (page = 1, perPage = 8, append = false) => {
    if (!append && isProductsLoaded.value && page === 1) return; // Cache hit for first page
    isLoading.value = true;
    try {
      const response = await productService.paginationPublic({
        publish: 2,
        page,
        perpage: perPage,
      });
      const data = (response as any)?.data;
      paginate.value = data ?? null;
      const newProducts = Array.isArray(data?.data) ? data.data : [];

      if (append) {
        products.value = [...products.value, ...newProducts];
      } else {
        products.value = newProducts;
        currentPage.value = 1;
        isProductsLoaded.value = true;
      }

      totalProducts.value = Number(data?.total || 0);
      hasMoreProducts.value = products.value.length < totalProducts.value;
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load products:", error);
      if (!append) {
        products.value = [];
        totalProducts.value = 0;
      }
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchPostByCategoryReviews = async (params?: Record<string, any>) => {
    isLoading.value = true;
    try {
      const defaultParams = {
        type: "all", // k paginate
        publish: 2,
        sort: "id,desc",
        post_catalogue_id: 1,
        ...params,
      };

      const response = await postService.paginationPublic(defaultParams);
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load posts by category reviews:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchBannerProductSeason = async (params?: Record<string, any>) => {
    isLoading.value = true;
    try {
      const defaultParams = {
        banner_type: "shop",
        publish: 2,
        sort: "id,desc",
        limit: 1,
        ...params,
      };

      const response = await bannerService.fetch(defaultParams);
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load banner:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const loadMoreProducts = async () => {
    if (!hasMoreProducts.value || isLoading.value) return;
    currentPage.value += 1;
    await fetchProducts(currentPage.value, 8, true);
  };

  const fetchSummerProducts = async (limit = 3) => {
    if (isSummerLoaded.value) return; // Cache hit
    try {
      const response = await productService.getSummerProducts(limit);
      const data = (response as any)?.data?.data;
      summerProducts.value = Array.isArray(data) ? data : [];
      isSummerLoaded.value = true;
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load summer products:", error);
      summerProducts.value = [];
      throw error;
    }
  };

  const getBannerImageUrl = (path: string) => {
    if (!path) return IMAGEDEFAULT;
    return path.startsWith("uploads") ? `${config.public.baseUrl}/storage/${path}` : path;
  };

  // Optimized initialization with Promise.all
  const initializeData = async () => {
    // Load critical data first (banners and products)
    await Promise.all([fetchBanners(), fetchProductCatalogues(), fetchProducts()]);

    // Load secondary data in parallel
    await Promise.all([fetchSummerProducts(3), fetchBannerProductSeason()]);
  };

  onMounted(async () => {
    await initializeData();
  });

  return {
    bannerShopTops,
    isLoading,
    productCatalogueList,
    products,
    summerProducts,
    totalProducts,
    currentPage,
    hasMoreProducts,
    fetchBanners,
    fetchProductCatalogues,
    fetchProducts,
    fetchPostByCategoryReviews,
    fetchBannerProductSeason,
    fetchSummerProducts,
    loadMoreProducts,
    getBannerImageUrl,
  };
};
