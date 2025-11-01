import type { Ref } from "vue";
import { wishlistService } from "~/services/product/wishlist.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IWishlist, IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";

export const useWishlist = () => {
  const isLoading: Ref<boolean> = ref(false);
  const wishlistItems = ref<IWishlist[]>([]);
  const wishlistGrouped = ref<IWishlistGrouped[]>([]);
  const paginate = ref<IApiOk<IPaginate<IWishlistGrouped>> | null>(null);

  const authStore = useAuthStore();
  const toast = useAppToast();

  const fetchWishlist = async (page = 1, perPage = 10, params?: Record<string, any>) => {
    if (!authStore.isAuthenticated) {
      wishlistItems.value = [];
      wishlistGrouped.value = [];
      return [];
    }

    isLoading.value = true;
    try {
      const response = await wishlistService.pagination({
        page,
        perpage: perPage,
        ...params,
      });

      paginate.value = response;
      const items = Array.isArray(response?.data?.data) ? response.data.data : [];
      wishlistGrouped.value = page === 1 ? items : [...wishlistGrouped.value, ...items];

      return wishlistGrouped.value;
    } catch (error) {
      console.error("[useWishlist] fetchWishlist error:", error);
      toast.error("Lỗi", { description: "Không thể tải danh sách yêu thích" });
      return [];
    } finally {
      isLoading.value = false;
    }
  };

  const toggleWishlist = async (productVariantId: number): Promise<boolean> => {
    if (!authStore.isAuthenticated) {
      toast.warning("Yêu cầu đăng nhập", { description: "Vui lòng đăng nhập để thêm vào yêu thích" });
      return false;
    }

    const isInWishlist = isVariantInWishlist(productVariantId);

    if (isInWishlist) {
      return await removeWishlist(productVariantId);
    }

    try {
      const response = await wishlistService.store({ productVariantId });
      if (!response?.data) return false;

      await fetchWishlist(1, paginate.value?.data?.per_page || 10);

      toast.success("Thành công", { description: "Đã thêm vào yêu thích" });
      return true;
    } catch (error: any) {
      console.error("[useWishlist] toggleWishlist error:", error);
      toast.error("Lỗi", { description: error?.response?.data?.message || "Không thể thêm vào yêu thích" });
      return false;
    }
  };

  const removeWishlist = async (productVariantId: number, wishlistId?: number): Promise<boolean> => {
    if (!authStore.isAuthenticated) return false;

    try {
      let itemId = wishlistId;

      if (!itemId) {
        for (const product of wishlistGrouped.value) {
          const variants = (product as any).variants || (product as any).product?.variants || [];
          const variant = variants.find((v: any) => v.id === productVariantId || v.productVariantId === productVariantId);
          if (variant?.wishlistId) {
            itemId = variant.wishlistId;
            break;
          }
        }
      }

      if (!itemId) {
        toast.warning("Không tìm thấy", { description: "Không tìm thấy mục yêu thích này" });
        return false;
      }

      await wishlistService.delete(itemId);

      wishlistGrouped.value = wishlistGrouped.value
        .map((product: any) => ({
          ...product,
          variants: (product.variants || []).filter((v: any) => v.wishlistId !== itemId && v.id !== productVariantId),
        }))
        .filter((product: any) => product.variants.length > 0);

      if (paginate.value?.data) {
        const currentPage = paginate.value.data.current_page || 1;
        const perPage = paginate.value.data.per_page || 10;
        await fetchWishlist(currentPage, perPage);
      }

      toast.success("Thành công", { description: "Đã xóa khỏi yêu thích" });
      return true;
    } catch (error: any) {
      console.error("[useWishlist] removeWishlist error:", error);
      toast.error("Lỗi", { description: error?.response?.data?.message || "Không thể xóa khỏi yêu thích" });
      return false;
    }
  };

  const isVariantInWishlist = (productVariantId: number): boolean => {
    for (const product of wishlistGrouped.value) {
      const variants = (product as any).variants || (product as any).product?.variants || [];
      if (variants.some((v: any) => (v.id === productVariantId || v.productVariantId === productVariantId) && v.wishlistId)) {
        return true;
      }
    }
    return wishlistItems.value.some((item) => item.productVariantId === productVariantId);
  };

  const getWishlistVariantsByProduct = (productId: number): IWishlist[] => {
    return wishlistItems.value.filter((item) => item.productVariant?.productId === productId);
  };

  const checkVariantWishlistStatus = async (productVariantId: number): Promise<boolean> => {
    if (!authStore.isAuthenticated) return false;

    try {
      const response = await wishlistService.pagination({
        product_variant_id: productVariantId,
        perpage: 1,
      });
      const hasVariant = (response?.data?.data?.length ?? 0) > 0;

      if (hasVariant) await fetchWishlist(1, paginate.value?.data?.per_page || 10);
      return hasVariant;
    } catch (error) {
      console.error("[useWishlist] checkVariantWishlistStatus error:", error);
      return false;
    }
  };

  return {
    isLoading,
    wishlistItems,
    wishlistGrouped,
    paginate,

    fetchWishlist,
    toggleWishlist,
    removeWishlist,

    isVariantInWishlist,
    getWishlistVariantsByProduct,
    checkVariantWishlistStatus,
  };
};
