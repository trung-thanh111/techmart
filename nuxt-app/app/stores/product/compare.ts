import { defineStore } from "pinia";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

export interface ICompareItem {
  productId: number;
  productName: string;
  variantId: number;
  variantName: string;
  fullName: string;
  price: number;
  image: string;
  product: IProduct;
  variant: IProductVariant;
}

interface CompareStoreState {
  items: ICompareItem[];
  expiresAt: number;
}

const STORAGE_KEY = "product_compare";
const TTL_MS = 60 * 60 * 1000; // 1 giờ

function loadFromStorage(): CompareStoreState {
  if (typeof window === "undefined") {
    return { items: [], expiresAt: Date.now() + TTL_MS };
  }

  try {
    const stored = sessionStorage.getItem(STORAGE_KEY);
    if (!stored) {
      return { items: [], expiresAt: Date.now() + TTL_MS };
    }

    const parsed = JSON.parse(stored) as CompareStoreState;
    const now = Date.now();

    // Kiểm tra hết hạn
    if (now >= parsed.expiresAt) {
      sessionStorage.removeItem(STORAGE_KEY);
      return { items: [], expiresAt: Date.now() + TTL_MS };
    }

    return parsed;
  } catch (error) {
    console.error("Error loading compare from storage:", error);
    return { items: [], expiresAt: Date.now() + TTL_MS };
  }
}

function saveToStorage(items: ICompareItem[]) {
  if (typeof window === "undefined") return;

  try {
    const state: CompareStoreState = {
      items,
      expiresAt: Date.now() + TTL_MS,
    };
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  } catch (error) {
    console.error("Error saving compare to storage:", error);
  }
}

export const useCompareStore = defineStore("compare", {
  state: (): CompareStoreState => loadFromStorage(),

  getters: {
    count: (state) => state.items.length,
    isFull: (state) => state.items.length >= 3,
    isEmpty: (state) => state.items.length === 0,
    itemsArray: (state) => state.items,
  },

  actions: {
    add(item: ICompareItem): boolean {
      if (this.items.length >= 3) {
        return false;
      }

      // Kiểm tra trùng lặp
      const exists = this.items.find((i) => i.variantId === item.variantId);
      if (exists) {
        return false;
      }

      this.items.push(item);
      saveToStorage(this.items);
      return true;
    },

    remove(variantId: number) {
      const index = this.items.findIndex((i) => i.variantId === variantId);
      if (index > -1) {
        this.items.splice(index, 1);
        saveToStorage(this.items);
      }
    },

    clear() {
      this.items = [];
      if (typeof window !== "undefined") {
        sessionStorage.removeItem(STORAGE_KEY);
      }
    },

    hasVariant(variantId: number): boolean {
      return this.items.some((i) => i.variantId === variantId);
    },
  },
});
