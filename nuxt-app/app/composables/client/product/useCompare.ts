import { computed, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useCompareStore, type ICompareItem } from "~/stores/client/product/compare";
import { resolveAlbum, formatPrice } from "~/utils/helpers";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductVariant } from "~/interfaces/product/variant.interface";
import { productService } from "~/services/product/product.service";

export const useCompare = () => {
  const store = useCompareStore();
  const toast = useToast();
  const isLoading = ref(false);

  const compareList = computed(() => store.itemsArray);
  const compareCount = computed(() => store.count);
  const isFull = computed(() => store.isFull);
  const isEmpty = computed(() => store.isEmpty);

  /**
   * Chuyển đổi product + variant thành ICompareItem
   */
  const createCompareItem = (product: IProduct, variant: IProductVariant): ICompareItem | null => {
    if (!product?.id || !variant?.id) return null;

    const images = resolveAlbum(product.album || []);
    const image = images[0] || "";

    const fullName = `${product.name || ""} ${variant.name || ""}`.trim();

    return {
      productId: product.id,
      productName: product.name || "",
      variantId: variant.id,
      variantName: variant.name || "",
      fullName,
      price: variant.price || 0,
      image,
      product,
      variant,
    };
  };

  const handleAddToCompareFromProduct = async (product: IProduct): Promise<boolean> => {
    if (!product?.id) {
      toast.add({
        severity: "error",
        summary: "Lỗi",
        detail: "Thông tin sản phẩm không hợp lệ",
        life: 3000,
      });
      return false;
    }

    try {
      let productToAdd = product;
      let variant: IProductVariant | null = null;

      // Ưu tiên: product.variant (từ API list với simple format)
      if (product.variant?.id) {
        variant = product.variant;
      } else if (product.productVariants && product.productVariants.length > 0 && product.productVariants[0]) {
        variant = product.productVariants[0];
      } else {
        const loadedProduct = await loadProductDetail(product.id);
        if (loadedProduct) {
          productToAdd = loadedProduct;
          variant = loadedProduct.productVariants?.[0] || loadedProduct.variant || null;
        }
      }

      if (!variant?.id) {
        toast.add({
          severity: "error",
          summary: "Lỗi",
          detail: "Sản phẩm không có phiên bản",
          life: 3000,
        });
        return false;
      }

      return addToCompare(productToAdd, variant);
    } catch (error) {
      console.error("[useCompare] handleAddToCompareFromProduct error:", error);
      toast.add({
        severity: "error",
        summary: "Lỗi",
        detail: "Không thể thêm sản phẩm vào danh sách so sánh",
        life: 3000,
      });
      return false;
    }
  };

  const addToCompare = (product: IProduct, variant: IProductVariant): boolean => {
    if (store.isFull) {
      toast.add({
        severity: "warn",
        summary: "Thông báo",
        detail: "Chỉ được so sánh tối đa 3 sản phẩm",
        life: 3000,
      });
      return false;
    }

    if (store.hasVariant(variant.id!)) {
      toast.add({
        severity: "info",
        summary: "Thông báo",
        detail: "Sản phẩm đã có trong danh sách so sánh",
        life: 3000,
      });
      return false;
    }

    const item = createCompareItem(product, variant);
    if (!item) {
      toast.add({
        severity: "error",
        summary: "Lỗi",
        detail: "Không thể thêm sản phẩm vào danh sách so sánh",
        life: 3000,
      });
      return false;
    }

    const success = store.add(item);
    if (success) {
      toast.add({
        severity: "success",
        summary: "Thành công",
        detail: "Đã thêm sản phẩm vào danh sách so sánh",
        life: 3000,
      });
    }

    return success;
  };

  /**
   * Xóa sản phẩm khỏi danh sách so sánh
   */
  const removeFromCompare = (variantId: number) => {
    store.remove(variantId);
    toast.add({
      severity: "success",
      summary: "Thành công",
      detail: "Đã xóa sản phẩm khỏi danh sách so sánh",
      life: 2000,
    });
  };

  /**
   * Xóa tất cả sản phẩm
   */
  const clearAll = () => {
    store.clear();
    toast.add({
      severity: "success",
      summary: "Thành công",
      detail: "Đã xóa tất cả sản phẩm khỏi danh sách so sánh",
      life: 2000,
    });
  };

  /**
   * Kiểm tra variant có trong danh sách so sánh không
   */
  const isInCompare = (variantId: number): boolean => {
    return store.hasVariant(variantId);
  };

  /**
   * Tìm kiếm sản phẩm với keyword
   */
  const searchProducts = async (keyword: string = "", limit: number = 10): Promise<IProduct[]> => {
    isLoading.value = true;
    try {
      const response = await productService.paginationPublic({
        publish: 2,
        perpage: limit,
        ...(keyword ? { keyword } : {}),
      });
      return response.data?.data || [];
    } catch (error) {
      console.error("Error searching products:", error);
      return [];
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy sản phẩm nổi bật (isHot = true)
   */
  const getFeaturedProducts = async (limit: number = 10): Promise<IProduct[]> => {
    isLoading.value = true;
    try {
      const response = await productService.paginationPublic({
        publish: 2,
        isHot: true,
        perpage: limit,
      });
      return response.data?.data || [];
    } catch (error) {
      console.error("Error fetching featured products:", error);
      return [];
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Load chi tiết product với variant (để lấy attributeData)
   */
  const loadProductDetail = async (productId: number): Promise<IProduct | null> => {
    isLoading.value = true;
    try {
      const response = await productService.show(productId);
      return response.data || null;
    } catch (error) {
      console.error("Error loading product detail:", error);
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    compareList,
    compareCount,
    isFull,
    isEmpty,
    isLoading,
    addToCompare,
    removeFromCompare,
    clearAll,
    isInCompare,
    searchProducts,
    getFeaturedProducts,
    loadProductDetail,
    createCompareItem,
    handleAddToCompareFromProduct,
  };
};
