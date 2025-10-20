import { bannerService } from "~/services/banner/banner.service";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { productCatalogueService } from "~/services/product/catalogue.service";

export const useShop = () => {
  const config = useRuntimeConfig();
  const shopBanners = ref<IBanner[]>([]);
  const productCatalogueList = ref<IProductCatalogue[]>([]);

  const isLoading = ref(false);

  const fetchBanners = async () => {
    isLoading.value = true;
    try {
      const response = await bannerService.getByType("shop");
      console.info((response as any)?.data?.data ?? []);
      const banners = (response as any)?.data?.data ?? [];
      shopBanners.value = Array.isArray(banners) ? banners : [];
    } catch (error) {
      console.error("Lỗi fetch shop banners:", error);
      shopBanners.value = [];
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProductCatalogues = async (): Promise<IProductCatalogue[]> => {
    isLoading.value = true;
    try {
      const response = await productCatalogueService.all();
      const data = (response as any)?.data ?? [];
      productCatalogueList.value = Array.isArray(data) ? data : [data];
      return productCatalogueList.value;
    } catch (error: any) {
      console.error("Lỗi khi load tag list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const getBannerImageUrl = (path: string) => {
    if (!path) return IMAGEDEFAULT;
    return path.startsWith("uploads") ? `${config.public.baseUrl}/storage/${path}` : path;
  };

  onMounted(() => {
    fetchBanners();
    fetchProductCatalogues();
  });

  return {
    shopBanners,
    isLoading,
    productCatalogueList,
    fetchBanners,
    fetchProductCatalogues,
    getBannerImageUrl,
  };
};
