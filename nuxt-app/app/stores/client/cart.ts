import { defineStore } from "pinia";
import type { ICart } from "~/interfaces/cart/cart.interface";
import type { ICartItem } from "~/interfaces/cart/cartItem.interface";
import { generateGuestId } from "~/utils/helpers";

const STORAGE_KEY = "cart";
const TTL_MS = 90 * 60 * 1000; // 1h30p

interface CartStoreState {
  cart: ICart | null;
  guestId: string | null;
  expiresAt: number;
  selectedItems: number[]; // IDs của cart items được chọn để thanh toán
}

function loadFromStorage(): CartStoreState {
  if (typeof window === "undefined") {
    const guestId = generateGuestId();
    return {
      cart: null,
      guestId,
      expiresAt: Date.now() + TTL_MS,
      selectedItems: [],
    };
  }

  try {
    const stored = sessionStorage.getItem(STORAGE_KEY);
    if (!stored) {
      const guestId = generateGuestId();
      return {
        cart: null,
        guestId,
        expiresAt: Date.now() + TTL_MS,
        selectedItems: [],
      };
    }

    const parsed = JSON.parse(stored) as CartStoreState;
    const now = Date.now();

    // Kiểm tra hết hạn
    if (now >= parsed.expiresAt) {
      sessionStorage.removeItem(STORAGE_KEY);
      const guestId = generateGuestId();
      return {
        cart: null,
        guestId,
        expiresAt: Date.now() + TTL_MS,
        selectedItems: [],
      };
    }

    return parsed;
  } catch (error) {
    console.error("Error loading cart from storage:", error);
    const guestId = generateGuestId();
    return {
      cart: null,
      guestId,
      expiresAt: Date.now() + TTL_MS,
      selectedItems: [],
    };
  }
}

function saveToStorage(state: CartStoreState) {
  if (typeof window === "undefined") return;

  try {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  } catch (error) {
    console.error("Error saving cart to storage:", error);
  }
}

export const useCartStore = defineStore("cart", {
  state: (): CartStoreState => loadFromStorage(),

  getters: {
    items: (state): ICartItem[] => {
      return state.cart?.cartItems || [];
    },
    count: (state): number => {
      return state.cart?.cartItems?.length || 0;
    },
    totalQuantity: (state): number => {
      return state.cart?.cartItems?.reduce((sum, item) => sum + (item.quantity || 0), 0) || 0;
    },
    totalPrice: (state): number => {
      return state.cart?.cartItems?.reduce((sum, item) => sum + (item.price || 0) * (item.quantity || 0), 0) || 0;
    },
    isEmpty: (state): boolean => {
      return !state.cart || !state.cart.cartItems || state.cart.cartItems.length === 0;
    },
    selectedCount: (state): number => {
      return state.selectedItems.length;
    },
    selectedItemsData: (state): ICartItem[] => {
      if (!state.cart?.cartItems) return [];
      return state.cart.cartItems.filter((item) => item.id && state.selectedItems.includes(item.id));
    },
    selectedTotalPrice: (state): number => {
      if (!state.cart?.cartItems) return 0;
      return state.cart.cartItems.filter((item) => item.id && state.selectedItems.includes(item.id)).reduce((sum, item) => sum + (item.price || 0) * (item.quantity || 0), 0);
    },
  },

  actions: {
    setCart(cart: ICart | null) {
      this.cart = cart;
      this.expiresAt = Date.now() + TTL_MS;
      saveToStorage(this.$state);
    },

    setGuestId(guestId: string) {
      this.guestId = guestId;
      this.expiresAt = Date.now() + TTL_MS;
      saveToStorage(this.$state);
    },

    addItem(item: ICartItem) {
      if (!this.cart) {
        this.cart = {
          cartItems: [],
        };
      }
      if (!this.cart.cartItems) {
        this.cart.cartItems = [];
      }
      this.cart.cartItems.push(item);
      this.expiresAt = Date.now() + TTL_MS;
      saveToStorage(this.$state);
    },

    updateItem(itemId: number, updates: Partial<ICartItem>) {
      if (!this.cart?.cartItems) return;
      const index = this.cart.cartItems.findIndex((item) => item.id === itemId);
      if (index > -1) {
        this.cart.cartItems[index] = { ...this.cart.cartItems[index], ...updates };
        this.expiresAt = Date.now() + TTL_MS;
        saveToStorage(this.$state);
      }
    },

    removeItem(itemId: number) {
      if (!this.cart?.cartItems) return;
      const index = this.cart.cartItems.findIndex((item) => item.id === itemId);
      if (index > -1) {
        this.cart.cartItems.splice(index, 1);
        this.selectedItems = this.selectedItems.filter((id) => id !== itemId);
        this.expiresAt = Date.now() + TTL_MS;
        saveToStorage(this.$state);
      }
    },

    clear() {
      this.cart = null;
      this.selectedItems = [];
      if (typeof window !== "undefined") {
        sessionStorage.removeItem(STORAGE_KEY);
      }
    },

    toggleSelectItem(itemId: number) {
      const index = this.selectedItems.indexOf(itemId);
      if (index > -1) {
        this.selectedItems.splice(index, 1);
      } else {
        this.selectedItems.push(itemId);
      }
      saveToStorage(this.$state);
    },

    selectAll() {
      if (!this.cart?.cartItems) return;
      this.selectedItems = this.cart.cartItems.map((item) => item.id!).filter((id): id is number => id !== undefined);
      saveToStorage(this.$state);
    },

    deselectAll() {
      this.selectedItems = [];
      saveToStorage(this.$state);
    },

    selectItems(itemIds: number[]) {
      this.selectedItems = [...itemIds];
      saveToStorage(this.$state);
    },

    hasItem(purchasableType: string, purchasableId: number): boolean {
      if (!this.cart?.cartItems) return false;
      return this.cart.cartItems.some((item) => item.purchasableType === purchasableType && item.purchasableId === purchasableId);
    },

    findItem(purchasableType: string, purchasableId: number): ICartItem | undefined {
      if (!this.cart?.cartItems) return undefined;
      return this.cart.cartItems.find((item) => item.purchasableType === purchasableType && item.purchasableId === purchasableId);
    },
  },
});
