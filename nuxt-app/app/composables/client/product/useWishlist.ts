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

      // Transform data để match với interface: wrap trong product key
      // API trả về format: {id: productId, name, variants: [{id: variantId, wishlistId}]}
      const transformedItems = items.map((item: any) => {
        // Nếu item đã có product key thì giữ nguyên
        if (item.product) {
          return item;
        }
        // Nếu item là flat structure thì wrap trong product
        // Lấy wishlistId từ variant đầu tiên (hoặc có thể không có nếu là grouped)
        const firstVariant = Array.isArray(item.variants) && item.variants.length > 0 ? item.variants[0] : null;
        const wishlistId = firstVariant?.wishlistId || 0;
        const productVariantId = firstVariant?.id || 0;

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
            variants: item.variants || [],
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

      if (!itemId) {
        for (const item of wishlistGrouped.value) {
          const variants = item.product?.variants || [];
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

      // Real-time update: remove variant from wishlist immediately
      wishlistGrouped.value = wishlistGrouped.value
        .map((item) => {
          if (!item.product?.variants) return item;
          const filteredVariants = item.product.variants.filter((v: any) => v.wishlistId !== itemId && v.id !== productVariantId && v.productVariantId !== productVariantId);
          return {
            ...item,
            product: {
              ...item.product,
              variants: filteredVariants,
            },
          };
        })
        .filter((item) => item.product?.variants && item.product.variants.length > 0);

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

    // Check trong wishlistGrouped
    for (const item of wishlistGrouped.value) {
      const variants = item.product?.variants || [];
      if (
        variants.some((v: any) => {
          const variantId = v.id || v.productVariantId;
          return variantId === productVariantId && v.wishlistId;
        })
      ) {
        return true;
      }
    }

    // Check trong wishlistItems (fallback)
    return wishlistItems.value.some((item) => item.productVariantId === productVariantId);
  };

  const getWishlistVariantsByProduct = (productId: number): IWishlist[] => {
    return wishlistItems.value.filter((item) => item.productVariant?.productId === productId);
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

      // Nếu tìm thấy trong API nhưng chưa có trong local state, thì refresh wishlist
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

  const handleToggleWishlistFromProduct = async (product: IProduct): Promise<boolean> => {
    if (!product) {
      toast.error("Lỗi", { description: "Thông tin sản phẩm không hợp lệ" });
      return false;
    }

    let variantId: number | undefined;

    if (product.variant?.id) {
      variantId = product.variant.id;
    } else if (product.productVariants && product.productVariants.length > 0 && product.productVariants[0]) {
      variantId = product.productVariants[0].id;
    }

    if (!variantId) {
      toast.error("Lỗi", { description: "Sản phẩm không có phiên bản" });
      return false;
    }

    return await toggleWishlist(variantId);
  };

  const isProductVariantInWishlist = (product: IProduct): boolean => {
    if (!product) return false;

    let variantId: number | undefined;
    if (product.variant?.id) {
      variantId = product.variant.id;
    } else if (product.productVariants && product.productVariants.length > 0 && product.productVariants[0]) {
      variantId = product.productVariants[0].id;
    }

    if (!variantId) return false;
    return isVariantInWishlist(variantId);
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
    checkVariantWishlistStatus,
    handleToggleWishlistFromProduct,
  };
};
