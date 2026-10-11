import { bannerService } from "~/services/banner/banner.service";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { productService } from "~/services/product/product.service";
import { postService } from "~/services/post/post.service";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IPaginate } from "~/interfaces/base.interface";
import type { IPost } from "~/interfaces/post/post.interface";

export const useShop = (scope = "shop") => {
  const config = useRuntimeConfig();

  const banners = useState<IBanner[]>("shop-banners", () => []);
  const catalogues = useState<IProductCatalogue[]>("shop-catalogues", () => []);
  const products = useState<IProduct[]>(`${scope}-products`, () => []);
  const summerProducts = useState<IProduct[]>("shop-summer", () => []);
  const posts = useState<IPost[]>("shop-posts", () => []);
  const bannerSeason = useState<IBanner[]>("shop-banner-season", () => []);

  const filters = useState<Record<string, any>>(`${scope}-filters`, () => ({
    sort: "created_at,desc",
    keyword: "",
    product_variants: {
      variant_attributes: {
        "attributes.id": { in: "" },
      },
    },
    price_origin: { between: "" },
    brand_id: { eq: null },
  }));

  const paginate = useState<IPaginate<IProduct> | null>(`${scope}-paginate`, () => null);
  const totalProducts = useState<number>(`${scope}-total-products`, () => 0);
  const currentPage = useState<number>(`${scope}-current-page`, () => 1);
  const hasMore = useState<boolean>(`${scope}-has-more`, () => true);
  const currentPerPage = useState<number>(`${scope}-per-page`, () => (scope === "search" ? 20 : 8));

  const isLoading = ref(false);
  const isInitialized = useState<boolean>(`${scope}-initialized`, () => false);

  const fetchBanners = async () => {
    if (banners.value.length) return banners.value;
    const { data } = await bannerService.fetch({
      type: "all",
      publish: 2,
      page_id: 4,
      position_id: 1,
      limit: 1,
    });
    (banners as any).value = Array.isArray(data) ? data : [data];
    return banners.value;
  };

  const fetchCatalogues = async () => {
    if (catalogues.value.length) return catalogues.value;
    const { data } = await productCatalogueService.all();
    catalogues.value = Array.isArray(data) ? data : [data];
    return catalogues.value;
  };

  const fetchProducts = async (page = 1, perPage = currentPerPage.value, append = false) => {
    isLoading.value = true;
    currentPerPage.value = perPage;

    try {
      const params: Record<string, any> = {
        publish: 2,
        page,
        perpage: perPage,
        sort: filters.value.sort || "created_at,desc",
        ...(filters.value.keyword ? { keyword: filters.value.keyword } : {}),
      };

      if (filters.value.product_variants?.variant_attributes?.["attributes.id"]?.in) {
        params.product_variants = JSON.stringify(filters.value.product_variants);
      }

      if (filters.value.price_origin?.between) {
        params.price_origin = JSON.stringify(filters.value.price_origin);
      }

      if (filters.value.brand_id?.eq !== null && filters.value.brand_id?.eq !== undefined) {
        params.brand_id = filters.value.brand_id.eq;
      }

      const res = await productService.pagination(params);

      const list = Array.isArray(res?.data?.data) ? res.data.data : [];
      paginate.value = res.data;
      totalProducts.value = Number(res?.data?.total || 0);

      const currentTotal = append ? products.value.length + list.length : list.length;
      hasMore.value = currentTotal < totalProducts.value;

      products.value = append ? [...products.value, ...list] : list;
      currentPage.value = page;

      return products.value;
    } catch (error) {
      console.error(`[useShop:${scope}] fetchProducts error:`, error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * @param sortValue - Giá trị sort mới (vd: "price_origin,desc")
   */
  const setSort = async (sortValue: string) => {
    if (filters.value.sort === sortValue) return;
    filters.value.sort = sortValue;
    currentPage.value = 1;
    await fetchProducts(1, currentPerPage.value, false);
  };

  /**
   * @param filterKey - Key của filter (vd: "brand_id", "price_origin")
   * @param filterValue - Giá trị filter
   */
  const setFilter = async (filterKey: string, filterValue: any) => {
    switch (filterKey) {
      case "attributes": {
        const selectedIds = Object.values(filterValue).flat().filter(Boolean);
        filters.value.product_variants.variant_attributes["attributes.id"].in = selectedIds.length ? selectedIds.join(",") : "";
        break;
      }
      case "price": {
        const [min, max] = filterValue;
        filters.value.price_origin.between = `${min},${max}`;
        break;
      }
      case "brand":
        filters.value.brand_id.eq = filterValue;
        break;
      default:
        return;
    }

    currentPage.value = 1;
    await fetchProducts(1, currentPerPage.value, false);
  };

  const resetFilters = async () => {
    filters.value = {
      sort: "created_at,desc",
      keyword: "",
      product_variants: {
        variant_attributes: {
          "attributes.id": {
            in: "",
          },
        },
      },
      price_origin: {
        between: "",
      },
      brand_id: {
        eq: null,
      },
    };
    currentPage.value = 1;
    await fetchProducts(1, currentPerPage.value, false);
  };

  const loadMore = async () => {
    if (!hasMore.value || isLoading.value) return;
    await fetchProducts(currentPage.value + 1, currentPerPage.value, true);
  };

  const fetchSummerProducts = async (limit = 3) => {
    if (summerProducts.value.length) return summerProducts.value;
    const { data } = await productService.getSummerProducts(limit);
    summerProducts.value = Array.isArray((data as any)?.data) ? (data as any).data : [];
    return summerProducts.value;
  };

  const fetchPosts = async () => {
    if (posts.value.length) return posts.value;
    const { data } = await postService.pagination({
      type: "all",
      publish: 2,
      sort: "id,desc",
      post_catalogue_id: 1,
      limit: 5,
    });
    (posts as any).value = Array.isArray(data) ? data : [data];
    return posts.value;
  };

  const fetchBannerSeason = async () => {
    if (bannerSeason.value.length) return bannerSeason.value;
    const { data } = await bannerService.fetch({
      type: "all",
      publish: 2,
      page_id: 4,
      position_id: 4,
      limit: 1,
    });
    (bannerSeason as any).value = Array.isArray(data) ? data : [data];
    return bannerSeason.value;
  };

  const initializeData = async () => {
    if (isInitialized.value) return;
    isLoading.value = true;
    try {
      await Promise.all([fetchBanners(), fetchBannerSeason(), fetchCatalogues(), fetchProducts(1, currentPerPage.value), fetchSummerProducts(), fetchPosts()]);
      isInitialized.value = true;
    } catch (err) {
      console.error(`[useShop:${scope}] Init failed:`, err);
    } finally {
      isLoading.value = false;
    }
  };

  return {
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

    fetchBanners,
    fetchCatalogues,
    fetchProducts,
    fetchSummerProducts,
    fetchPosts,
    fetchBannerSeason,
    loadMore,
    initializeData,

    setSort,
    setFilter,
    resetFilters,
  };
};

export const useSearch = () => useShop("search");
