import type { Ref } from "vue";
import { wishlistService } from "~/services/product/wishlist.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IWishlist, IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";

export const useWishlist = () => {
  const wishlistItems = useState<IWishlist[]>("wishlist-items", () => []);
  const wishlistGrouped = useState<IWishlistGrouped[]>("wishlist-grouped", () => []);
  const paginate = useState<IApiOk<IPaginate<IWishlistGrouped>> | null>("wishlist-paginate", () => null);
  const isLoading = ref(false);
  const isInitialized = useState<boolean>("wishlist-initialized", () => false);

  const authStore = useAuthStore();
  const toast = useAppToast();

  const fetchWishlist = async (page = 1, perPage = 10, append = false, params?: Record<string, any>) => {
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

      const transformedItems = items.map((item: any) => {
        if (item.product) {
          let variantData = item.product.variant || (item.product as any).variants;
          const variantArray = Array.isArray(variantData) ? variantData : variantData ? [variantData] : [];
          const variant = variantArray.length > 0 ? variantArray[0] : null;

          return {
            ...item,
            product: {
              ...item.product,
              variant: variant,
              variants: variantArray,
            },
            productVariantId: variant?.id || item.productVariantId || 0,
          };
        }
        const variantData = item.variant || item.variants;
        const variantArray = Array.isArray(variantData) ? variantData : variantData ? [variantData] : [];
        const variant = variantArray.length > 0 ? variantArray[0] : null;
        const wishlistId = variant?.wishlistId || 0;
        const productVariantId = variant?.id || 0;

        return {
          id: wishlistId, // wishlist id từ variant
          userId: item.userId || 0,
          productVariantId: productVariantId,
          product: {
            id: item.id, // product id
            name: item.name,
            slug: item.slug,
            priceOrigin: item.priceOrigin,
            album: item.album,
            productCatalogueId: item.productCatalogueId || 0,
            brandId: item.brandId || 0,
            variant: variant,
            variants: variantArray,
          },
        };
      });

      wishlistGrouped.value = append ? [...wishlistGrouped.value, ...transformedItems] : transformedItems;

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

      const currentPage = paginate.value?.data?.current_page || 1;
      const perPage = paginate.value?.data?.per_page || 10;
      await fetchWishlist(currentPage, perPage, false);

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
      let targetItem: IWishlistGrouped | null = null;
      let targetVariantIndex = -1;

      // Tìm item và variant cần xóa
      if (!itemId) {
        for (const item of wishlistGrouped.value) {
          const variants = (item.product as any)?.variants || [];
          const variantArray = Array.isArray(variants) ? variants : variants ? [variants] : [];

          for (let i = 0; i < variantArray.length; i++) {
            const variant = variantArray[i];
            if (variant && (variant.id === productVariantId || variant.productVariantId === productVariantId) && variant.wishlistId) {
              itemId = variant.wishlistId;
              targetItem = item;
              targetVariantIndex = i;
              break;
            }
          }
          if (itemId) break;
        }
      } else {
        for (const item of wishlistGrouped.value) {
          const variants = (item.product as any)?.variants || [];
          const variantArray = Array.isArray(variants) ? variants : variants ? [variants] : [];

          for (let i = 0; i < variantArray.length; i++) {
            const variant = variantArray[i];
            if (variant && variant.wishlistId === itemId) {
              targetItem = item;
              targetVariantIndex = i;
              break;
            }
          }
          if (targetItem) break;
        }
      }

      if (!itemId || !targetItem || targetVariantIndex === -1) {
        toast.warning("Không tìm thấy", { description: "Không tìm thấy mục yêu thích này" });
        return false;
      }

      await wishlistService.delete(itemId);

      const variants = (targetItem.product as any)?.variants || [];
      const variantArray = Array.isArray(variants) ? [...variants] : variants ? [variants] : [];

      variantArray.splice(targetVariantIndex, 1);

      if (variantArray.length > 0) {
        const updatedProduct = {
          ...targetItem.product,
          variants: variantArray,
          variant: variantArray[0],
        };

        wishlistGrouped.value = wishlistGrouped.value.map((item) => {
          if (item.product?.id === targetItem?.product?.id) {
            return {
              ...item,
              product: updatedProduct,
              productVariantId: variantArray[0].id || variantArray[0].productVariantId || 0,
            };
          }
          return item;
        });
      } else {
        wishlistGrouped.value = wishlistGrouped.value.filter((item) => item.product?.id !== targetItem?.product?.id);
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
    if (!productVariantId) return false;

    for (const item of wishlistGrouped.value) {
      const variants = (item.product as any)?.variants || [];
      const variantArray = Array.isArray(variants) ? variants : variants ? [variants] : [];

      for (const variant of variantArray) {
        if (variant) {
          const variantId = variant.id || variant.productVariantId;
          if (variantId === productVariantId && variant.wishlistId) {
            return true;
          }
        }
      }
    }

    return wishlistItems.value.some((item) => item.productVariantId === productVariantId);
  };

  const getWishlistVariantsByProduct = (productId: number): IWishlist[] => {
    return wishlistItems.value.filter((item) => item.productVariant?.productId === productId);
  };

  /**
   * Lấy tất cả variant IDs có trong wishlist cho một product
   */
  const getWishlistVariantIdsByProductId = (productId: number): number[] => {
    if (!productId) return [];

    const wishlistItem = wishlistGrouped.value.find((item) => item.product?.id === productId);
    if (!wishlistItem) return [];

    const variants = (wishlistItem.product as any).variants || [];
    if (Array.isArray(variants) && variants.length > 0) {
      return variants.map((v: any) => v.id || v.productVariantId).filter((id: any) => id);
    }

    // Fallback: check single variant
    const variant = (wishlistItem.product as any).variant;
    if (variant?.id) {
      return [variant.id];
    }

    return [];
  };

  const checkVariantWishlistStatus = async (productVariantId: number): Promise<boolean> => {
    if (!authStore.isAuthenticated || !productVariantId) return false;

    try {
      // Check local state trước
      if (isVariantInWishlist(productVariantId)) {
        return true;
      }

      const response = await wishlistService.pagination({
        product_variant_id: productVariantId,
        perpage: 1,
      });
      const items = Array.isArray(response?.data?.data) ? response.data.data : [];
      const hasVariant = items.length > 0;

      if (hasVariant) {
        const currentPage = paginate.value?.data?.current_page || 1;
        const perPage = paginate.value?.data?.per_page || 10;
        await fetchWishlist(currentPage, perPage, false);
      }

      return hasVariant;
    } catch (error) {
      console.error("[useWishlist] checkVariantWishlistStatus error:", error);
      return false;
    }
  };

  const isProductVariantInWishlist = (product: IProduct): boolean => {
    if (!product) return false;
    const variantId = product.variant?.id;
    if (!variantId) return false;
    return isVariantInWishlist(variantId);
  };

  const handleToggleWishlistFromProduct = async (product: IProduct): Promise<boolean> => {
    if (!product) return false;
    const variantId = product.variant?.id;
    if (!variantId) {
      toast.warning("Cảnh báo", { description: "Sản phẩm chưa có biến thể" });
      return false;
    }
    return await toggleWishlist(variantId);
  };

  return {
    isLoading,
    isInitialized,
    wishlistItems,
    wishlistGrouped,
    paginate,

    fetchWishlist,
    toggleWishlist,
    removeWishlist,

    isVariantInWishlist,
    isProductVariantInWishlist,
    getWishlistVariantsByProduct,
    getWishlistVariantIdsByProductId,
    checkVariantWishlistStatus,
    handleToggleWishlistFromProduct,
  };
};
