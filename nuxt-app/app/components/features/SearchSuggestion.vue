<template>
  <div class="relative">
    <form @submit.prevent="onSubmit" class="flex rounded overflow-hidden bg-white shadow-sm">
      <input v-model="keyword" type="text" name="keyword" :placeholder="$t('nav.search')" class="flex-1 px-4 py-2 text-gray-600 focus:outline-none" @focus="open = true" @blur="handleBlur" />
      <button type="submit" class="bg-[#00b88c] px-3 hover:bg-[#00b88d] transition-colors text-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-5 h-5">
          <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
        </svg>
      </button>
    </form>
    <div v-if="open" class="absolute w-full bg-white shadow-lg mt-1 rounded z-40">
      <div class="px-4 py-2">
        <div v-if="!keyword.trim()" class="space-y-1">
          <p v-if="recent.length > 0" class="text-sm font-semibold text-gray-500 mb-2">{{ $t("search.recentSearches") }}</p>
          <div v-for="(item, idx) in recent" :key="item" class="flex items-center justify-between group p-2 rounded hover:bg-gray-100 cursor-pointer">
            <div @click="useRecent(item)" class="flex items-center gap-2 w-full text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-5 h-5 text-gray-400">
                <path d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9L0 168c0 13.3 10.7 24 24 24l110.1 0c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24l0 104c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65 0-94.1c0-13.3-10.7-24-24-24z" />
              </svg>
              <p class="text-sm font-normal truncate">{{ item }}</p>
            </div>
            <button @click.stop="removeRecent(idx)" class="text-gray-400 hover:text-red-500">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>
        </div>
        <div v-else class="space-y-1 max-h-[200px] overflow-y-auto">
          <div v-if="isSearching" class="space-y-2 p-1">
            <div v-for="i in 3" :key="i" class="flex items-center gap-2 p-1.5 animate-pulse">
              <div class="w-10 h-8 bg-gray-200 rounded flex-shrink-0"></div>
              <div class="flex-1 space-y-1.5">
                <div class="h-3.5 bg-gray-200 rounded w-3/4"></div>
                <div class="h-2.5 bg-gray-200 rounded w-1/3"></div>
              </div>
            </div>
          </div>
          <div v-else-if="suggestions.length === 0" class="text-sm text-gray-500 py-2">{{ $t("search.noResults") }}</div>
          <div v-else class="text-sm text-gray-500 py-2">{{ $t("search.suggestions") }}</div>
          <template v-if="!isSearching">
            <div v-for="product in suggestions" :key="product.id" class="flex items-center gap-2 p-2 rounded hover:bg-gray-50">
              <NuxtLink :to="`/product/${product.slug || ''}`" class="flex items-center gap-2 w-full text-gray-700 no-underline">
                <img :src="getProductImage(product)" loading="lazy" :alt="getProductDisplayName(product)" class="w-10 h-8 object-cover rounded flex-shrink-0" @error="handleImgError" />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-normal text-gray-800 truncate">{{ getProductDisplayName(product) }}</p>
                  <p class="text-xs text-red-600 mt-1">{{ getProductPrice(product) ? formatPrice(getProductPrice(product)) : $t("search.updating") }}</p>
                </div>
              </NuxtLink>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-gray-400">
                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
              </svg>
            </div>
          </template>
        </div>
      </div>
      <div class="w-full max-w-7xl mx-auto">
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
      </div>
      <div v-if="visibleList.length > 0" class="px-4 py-2">
        <p class="text-sm font-semibold text-gray-500 mb-2">{{ $t("common.featuredProducts") }}</p>
        <div class="space-y-1 max-h-[120px] overflow-y-auto">
          <div v-for="product in visibleList" :key="product.id" class="flex items-center justify-between group p-2 rounded hover:bg-gray-100 cursor-pointer">
            <NuxtLink :to="`/product/${product.slug || ''}`" class="flex items-center gap-2 max-w-[90%] text-gray-400 no-underline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 flex-shrink-0"><polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2" /></svg>
              <img :src="getProductImage(product)" loading="lazy" :alt="getProductDisplayName(product)" class="w-10 h-8 object-cover rounded flex-shrink-0" @error="handleImgError" />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-normal text-gray-800 truncate">{{ getProductDisplayName(product) }}</p>
                <p class="text-xs text-red-600 mt-1">{{ getProductPrice(product) ? formatPrice(getProductPrice(product)) : $t("search.updating") }}</p>
              </div>
            </NuxtLink>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-gray-400">
              <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { useDebounceFn } from "@vueuse/core";
import { useCompare } from "~/composables/product/useCompare";
import { useCart } from "~/composables/cart/useCart";
import { useWishlist } from "~/composables/product/useWishlist";
import { resolveAlbum, formatPrice } from "~/utils/helpers";
import { IMAGEDEFAULT } from "~/utils/constants";
import type { IProduct } from "~/interfaces/product/product.interface";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const keyword = ref("");
const open = ref(false);
const suggestions = ref<IProduct[]>([]);
const recent = ref<string[]>([]);

const { searchProducts, getFeaturedProducts } = useCompare();
const { addItem } = useCart();
const { handleToggleWishlistFromProduct, isProductVariantInWishlist } = useWishlist();

const loadRecent = () => {
  try {
    const raw = localStorage.getItem("recent_searches");
    recent.value = raw ? JSON.parse(raw) : [];
  } catch {
    recent.value = [];
  }
};

const saveRecent = (value: string) => {
  const list = [value, ...recent.value.filter((i) => i !== value)].slice(0, 10);
  recent.value = list;
  try {
    localStorage.setItem("recent_searches", JSON.stringify(list));
  } catch {}
};

const removeRecent = (index: number) => {
  recent.value.splice(index, 1);
  try {
    localStorage.setItem("recent_searches", JSON.stringify(recent.value));
  } catch {}
};

const isSearching = ref(false);

const debounced = useDebounceFn(async (val: string) => {
  if (!val.trim()) {
    suggestions.value = [];
    isSearching.value = false;
    return;
  }
  isSearching.value = true;
  try {
    suggestions.value = await searchProducts(val, 10);
  } finally {
    isSearching.value = false;
  }
}, 250);

watch(keyword, (v) => {
  debounced(v);
});

watch(open, (v) => {
  if (v) loadRecent();
});

const onSubmit = async () => {
  const q = (keyword.value || "").trim();
  if (!q) return;
  saveRecent(q);
  open.value = false;
  // Use Nuxt router to navigate without page reload
  navigateTo({
    path: "/search",
    query: { keyword: q },
  });
};

const useRecent = (val: string) => {
  keyword.value = val;
  saveRecent(val);
  open.value = false;
  navigateTo({
    path: "/search",
    query: { keyword: val },
  });
};

//out blur khởi in ơut thì open = false sau 150ms
const handleBlur = () => {
  setTimeout(() => {
    open.value = false;
  }, 150);
};

const getProductImage = (product: IProduct) => {
  const images = resolveAlbum(product.album || []);
  return images[0] || IMAGEDEFAULT;
};
const handleImgError = (e: Event) => {
  const target = e.target as HTMLImageElement;
  if (target && target.src !== IMAGEDEFAULT) {
    target.src = IMAGEDEFAULT;
  }
};
const getProductDisplayName = (product: IProduct) => {
  const v = product.productVariants?.[0] || product.variant;
  return v?.name ? `${product.name} ${v.name}`.trim() : product.name || "";
};
const getProductPrice = (product: IProduct) => {
  const v = product.productVariants?.[0] || product.variant;
  return v?.price || product.priceOrigin || 0;
};

// const isInWishlist = (product: IProduct) => {
//   const variant = product.variant || product.productVariants?.[0];
//   if (!variant) return false;
//   return isProductVariantInWishlist(variant as any);
// };

// const handleToggleWishlist = async (product: IProduct) => {
//   await handleToggleWishlistFromProduct(product);
// };

// const handleAddToCart = async (product: IProduct) => {
//   let variant = product.variant || product.productVariants?.[0] || null;
//   await addItem(product, variant, 1, false);
// };

const visibleList = computed(() => {
  if (keyword.value && suggestions.value.length) return suggestions.value;
  // featured products when no keyword
  return featured.value;
});

const featured = ref<IProduct[]>([]);
watch(open, async (v) => {
  if (v) {
    featured.value = await getFeaturedProducts(10);
  }
});
</script>

<style scoped>
.boxSuggestion {
  z-index: 60;
}
</style>
