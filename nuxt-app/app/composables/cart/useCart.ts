import { computed, ref, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import { useCartStore } from "~/stores/cart";
import { cartService } from "~/services/cart/cart.service";
import { cartItemService } from "~/services/cart/cartItem.service";
import { useAuthStore } from "~/stores/auth/auth";
import { generateGuestId } from "~/utils/helpers";
import { useAppToast } from "~/composables/useAppToast";
import type { ICart, CartPayload } from "~/interfaces/cart/cart.interface";
import type { ICartItem, CartItemPayload } from "~/interfaces/cart/cartItem.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

export const useCart = () => {
  const { t } = useI18n();
  const store = useCartStore();
  const authStore = useAuthStore();
  const toast = useAppToast();
  const isLoading = ref(false);
  const isAddingToCart = ref(false);
  const isBuyingNow = ref(false);
  const isSyncing = ref(false);

  const cart = computed(() => store.cart);
  const items = computed(() => store.items);
  const count = computed(() => store.count);
  const totalQuantity = computed(() => store.totalQuantity);
  const totalPrice = computed(() => store.totalPrice);
  const isEmpty = computed(() => store.isEmpty);
  const selectedItems = computed(() => store.selectedItemsData);
  const selectedCount = computed(() => store.selectedCount);
  const selectedTotalPrice = computed(() => store.selectedTotalPrice);
  const isAuthenticated = computed(() => authStore.isAuthenticated);
  const guestId = computed(() => store.guestId);

  const fetchCart = async (): Promise<ICart | null> => {
    if (!isAuthenticated.value) return null;
    isLoading.value = true;
    try {
      const response = await cartService.pagination({
        type: "all",
        customer_id: authStore.user?.id,
        limit: 1,
        perpage: 1,
      });

      let cartArray: ICart[] = [];

      // Handle different response structures
      if (Array.isArray(response.data)) {
        cartArray = response.data;
      } else if (response.data && typeof response.data === "object" && "data" in response.data) {
        const nestedData = (response.data as any).data;
        if (Array.isArray(nestedData)) {
          cartArray = nestedData;
        } else if (nestedData && typeof nestedData === "object" && "data" in nestedData && Array.isArray((nestedData as any).data)) {
          cartArray = (nestedData as any).data;
        }
      }

      if (cartArray.length > 0) {
        const userCart = cartArray[0];
        if (userCart) {
          // Đảm bảo cartItems được load đầy đủ
          store.setCart(userCart);
          return userCart;
        }
      }

      return null;
    } catch (error: any) {
      console.error("Error fetching cart:", error);
      // Không hiển thị toast khi không có cart (có thể là lần đầu)
      if (error?.statusCode !== 404 && error?.code !== 404) {
        toast.error(t("cart.errorLoad"));
      }
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy cart cho guest
   */
  const fetchGuestCart = async (): Promise<ICart | null> => {
    // Đảm bảo guestId luôn có
    if (!guestId.value) {
      const newGuestId = generateGuestId();
      store.setGuestId(newGuestId);
    }

    isLoading.value = true;
    try {
      const response = await cartService.showByGuestId(guestId.value!);
      if (response.data) {
        store.setCart(response.data);
        return response.data;
      }
      return null;
    } catch (error: any) {
      if (error?.statusCode === 404 || error?.code === 404) {
        return null;
      }
      console.error("Error fetching guest cart:", error);
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Tạo cart mới (user hoặc guest)
   */
  const createCart = async (): Promise<ICart | null> => {
    isLoading.value = true;
    try {
      // Đảm bảo guestId luôn có nếu là guest
      if (!isAuthenticated.value && !guestId.value) {
        const newGuestId = generateGuestId();
        store.setGuestId(newGuestId);
      }

      const payload = {
        publish: 2,
        ...(isAuthenticated.value ? { customerId: authStore.user?.id } : { guestId: guestId.value || store.guestId }),
      };

      const response = isAuthenticated.value ? await cartService.store(payload) : await cartService.storeGuest(payload);

      if (response.data) {
        store.setCart(response.data);
        return response.data;
      }
      return null;
    } catch (error: any) {
      console.error("Error creating cart:", error);
      toast.error(error?.data?.message || t("cart.errorCreate"));
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy hoặc tạo cart
   */
  const getOrCreateCart = async (): Promise<ICart | null> => {
    if (isAuthenticated.value) {
      const existingCart = await fetchCart();
      if (existingCart) return existingCart;
      return await createCart();
    } else {
      const existingCart = await fetchGuestCart();
      if (existingCart) return existingCart;
      return await createCart();
    }
  };

  /**
   * Thêm item vào cart
   */
  const addItem = async (product: IProduct, variant: IProductVariant | null, quantity: number = 1, isBuyNow: boolean = false): Promise<boolean> => {
    if (isBuyNow) {
      isBuyingNow.value = true;
    } else {
      isAddingToCart.value = true;
    }
    try {
      // Xác định purchasable
      const purchasableType = variant ? "App\\Models\\ProductVariant" : "App\\Models\\Product";
      const purchasableId = variant ? variant.id! : product.id!;
      const price = variant?.price || product.priceOrigin || 0;

      // Lấy hoặc tạo cart - đảm bảo cart luôn được fetch từ DB trước
      let currentCart = cart.value;
      if (!currentCart || !currentCart.id) {
        // Fetch cart từ DB trước
        if (isAuthenticated.value) {
          currentCart = await fetchCart();
        } else {
          currentCart = await fetchGuestCart();
        }

        // Nếu vẫn không có, tạo mới
        if (!currentCart || !currentCart.id) {
          currentCart = await getOrCreateCart();
          if (!currentCart || !currentCart.id) {
            toast.error(t("cart.errorCreate"));
            return false;
          }
        }
      }

      // Kiểm tra trùng sản phẩm
      const existingItem = store.findItem(purchasableType, purchasableId);
      if (existingItem && existingItem.id) {
        const newQuantity = (existingItem.quantity || 0) + quantity;
        await updateItemQuantity(existingItem.id, newQuantity);
      } else {
        const payload: Partial<CartItemPayload> = {
          cartId: currentCart.id!,
          purchasableType,
          purchasableId,
          quantity,
          price,
          publish: 2,
        };

        const response = isAuthenticated.value ? await cartItemService.store(payload) : await cartItemService.storeGuest(payload);

        if (response.data) {
          store.addItem(response.data);
          if (isBuyNow) {
            store.deselectAll();
            if (response.data.id) {
              store.toggleSelectItem(response.data.id);
            }
          }
        }
      }

      // Reload cart để đồng bộ - luôn reload từ DB
      if (isAuthenticated.value) {
        await fetchCart();
      } else {
        await fetchGuestCart();
      }

      // Hiển thị toast sau khi reload xong
      await nextTick();
      toast.success(isBuyNow ? t("cart.addSuccessBuyNow") : t("cart.addSuccess"));

      return true;
    } catch (error: any) {
      console.error("Error adding item to cart:", error);
      toast.error(error?.data?.message || t("cart.errorAdd"));
      return false;
    } finally {
      if (isBuyNow) {
        isBuyingNow.value = false;
      } else {
        isAddingToCart.value = false;
      }
    }
  };

  /**
   * Cập nhật quantity của item
   */
  const updateItemQuantity = async (itemId: number, quantity: number): Promise<boolean> => {
    if (quantity < 1) {
      return await removeItem(itemId);
    }

    isLoading.value = true;
    try {
      const item = items.value.find((i) => i.id === itemId);
      if (!item) {
        toast.error(t("cart.productNotFound"));
        return false;
      }

      const payload: Partial<CartItemPayload> = {
        cartId: item.cartId!,
        purchasableType: item.purchasableType!,
        purchasableId: item.purchasableId!,
        quantity,
        price: item.price!,
        publish: item.publish || 2,
      };

      const response = isAuthenticated.value ? await cartItemService.update(itemId, payload) : await cartItemService.updateGuest(itemId, payload);

      if (response.data) {
        store.updateItem(itemId, response.data);
        // Reload cart để đồng bộ từ DB
        if (isAuthenticated.value) {
          await fetchCart();
        } else {
          await fetchGuestCart();
        }
      }

      return true;
    } catch (error: any) {
      console.error("Error updating item quantity:", error);
      toast.error(t("cart.errorUpdate"));
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Xóa item khỏi cart
   */
  const removeItem = async (itemId: number): Promise<boolean> => {
    isLoading.value = true;
    try {
      const response = isAuthenticated.value ? await cartItemService.delete(itemId) : await cartItemService.deleteGuest(itemId);

      store.removeItem(itemId);

      if (isAuthenticated.value) {
        await fetchCart();
      } else {
        await fetchGuestCart();
      }

      toast.success(t("cart.removeSuccess"));

      return true;
    } catch (error: any) {
      console.error("Error removing item from cart:", error);
      toast.error(t("cart.errorRemove"));
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const clearCart = async (): Promise<boolean> => {
    if (isEmpty.value) return true;

    isLoading.value = true;
    try {
      const itemIds = items.value.map((item) => item.id!).filter((id): id is number => id !== undefined);
      for (const itemId of itemIds) {
        try {
          if (isAuthenticated.value) {
            await cartItemService.delete(itemId);
          } else {
            await cartItemService.deleteGuest(itemId);
          }
        } catch (error: any) {
          console.error(`Error deleting item ${itemId}:`, error);
        }
      }

      // Clear store
      store.clear();

      if (isAuthenticated.value) {
        await fetchCart();
      } else {
        await fetchGuestCart();
      }

      toast.success(t("cart.clearSuccess"));

      return true;
    } catch (error: any) {
      console.error("Error clearing cart:", error);
      toast.error(t("cart.errorClear"));
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const syncCartAfterLogin = async (): Promise<void> => {
    if (!isAuthenticated.value) return;

    isSyncing.value = true;
    try {
      // Lấy guest cart từ store (session storage) - đây là source of truth cho guest cart
      const guestCart = store.cart;
      const guestItems = guestCart?.cartItems || [];

      // Nếu không có guest items, không cần sync
      if (guestItems.length === 0) {
        // Vẫn cần fetch user cart để load vào store
        await fetchCart();
        return;
      }
      // Lấy user cart hiện tại từ API
      const userCart = await fetchCart();

      // Nếu không có user cart, tạo mới và chuyển guest items sang
      if (!userCart || !userCart.id) {
        const newCart = await createCart();
        if (!newCart || !newCart.id) {
          console.error("Failed to create user cart");
          return;
        }

        // Chuyển tất cả guest items sang user cart
        for (const item of guestItems) {
          if (!item.purchasableType || !item.purchasableId) continue;

          try {
            const payload: Partial<CartItemPayload> = {
              cartId: newCart.id,
              purchasableType: item.purchasableType,
              purchasableId: item.purchasableId,
              quantity: item.quantity || 1,
              price: item.price || 0,
              publish: item.publish || 2,
            };
            await cartItemService.store(payload);
          } catch (error: any) {
            console.error("Error adding item to user cart:", error);
            // Continue with next item
          }
        }

        // Xóa guest cart và reload user cart
        store.clear();
        await fetchCart();
        return;
      }

      // Nếu có user cart, merge guest items vào user cart
      const userItems = userCart.cartItems || [];
      for (const item of guestItems) {
        if (!item.purchasableType || !item.purchasableId) continue;

        // Tìm item tương ứng trong user cart
        const existingUserItem = userItems.find((i) => i.purchasableType === item.purchasableType && i.purchasableId === item.purchasableId);

        if (existingUserItem && existingUserItem.id) {
          // Merge quantity
          const newQuantity = (existingUserItem.quantity || 0) + (item.quantity || 0);
          try {
            await updateItemQuantity(existingUserItem.id, newQuantity);
          } catch (error: any) {
            console.error("Error updating item quantity:", error);
            // Continue with next item
          }
        } else {
          // Thêm mới vào user cart
          try {
            const payload: Partial<CartItemPayload> = {
              cartId: userCart.id!,
              purchasableType: item.purchasableType,
              purchasableId: item.purchasableId,
              quantity: item.quantity || 1,
              price: item.price || 0,
              publish: item.publish || 2,
            };
            await cartItemService.store(payload);
          } catch (error: any) {
            console.error("Error adding item to user cart:", error);
            // Continue with next item
          }
        }
      }

      // Xóa guest cart và reload user cart
      store.clear();
      await fetchCart();
    } catch (error: any) {
      console.error("Error syncing cart after login:", error);
      toast.error(t("cart.errorSync"));
    } finally {
      isSyncing.value = false;
    }
  };

  /**
   * Load cart khi component mount - ưu tiên session cho guest, DB cho user
   */
  const loadCart = async (): Promise<void> => {
    try {
      if (isAuthenticated.value) {
        // Đăng nhập: luôn fetch từ DB
        const dbCart = await fetchCart();
        if (dbCart && dbCart.cartItems && dbCart.cartItems.length > 0) {
          return;
        }
        // Nếu không có cart trong DB, có thể có cart trong session (sau khi đăng nhập)
        const sessionCart = cart.value;
        if (sessionCart && sessionCart.cartItems && sessionCart.cartItems.length > 0) {
          // Sync guest cart với user cart
          await syncCartAfterLogin();
          return;
        }
      } else {
        // Guest: ưu tiên session storage trước (đã được load từ store khi khởi tạo)
        const sessionCart = cart.value;
        if (sessionCart && sessionCart.cartItems && sessionCart.cartItems.length > 0) {
          // Cart đã có trong session, kiểm tra xem có trong DB không để đồng bộ
          // Nếu có trong DB, fetch để đảm bảo data mới nhất
          // Nếu không có trong DB (404), giữ nguyên session cart
          try {
            const dbCart = await fetchGuestCart();
            // Nếu có cart trong DB, đã được set vào store trong fetchGuestCart()
            // Nếu không có (404), session cart vẫn được giữ nguyên
          } catch (error: any) {
            // Lỗi 404 hoặc lỗi khác - giữ nguyên session cart
            if (error?.statusCode !== 404 && error?.code !== 404) {
              console.error("Error fetching guest cart:", error);
            }
          }
          return;
        }

        // Nếu không có session cart, thử fetch từ DB
        const guestCart = await fetchGuestCart();
        if (!guestCart || !guestCart.cartItems || guestCart.cartItems.length === 0) {
          // Không có cart trong DB và session, không cần làm gì
          return;
        }
      }
    } catch (error: any) {
      console.error("Error loading cart:", error);
    }
  };

  return {
    // State
    isLoading: computed(() => isLoading.value),
    isAddingToCart: computed(() => isAddingToCart.value),
    isBuyingNow: computed(() => isBuyingNow.value),
    isSyncing: computed(() => isSyncing.value),
    cart,
    items,
    count,
    totalQuantity,
    totalPrice,
    isEmpty,
    selectedItems,
    selectedCount,
    selectedTotalPrice,
    guestId,

    // Actions
    fetchCart,
    fetchGuestCart,
    createCart,
    getOrCreateCart,
    addItem,
    updateItemQuantity,
    removeItem,
    clearCart,
    syncCartAfterLogin,
    loadCart,

    // Store actions
    toggleSelectItem: (itemId: number) => store.toggleSelectItem(itemId),
    selectAll: () => store.selectAll(),
    deselectAll: () => store.deselectAll(),
    selectItems: (itemIds: number[]) => store.selectItems(itemIds),
    isItemSelected: (itemId: number) => store.selectedItems.includes(itemId),
  };
};
