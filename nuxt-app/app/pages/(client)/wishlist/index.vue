<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- Breadcrumb  -->
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="product-sell-well bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("client.wishlist.title") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="relative group flex items-center space-x-2">
            <div class="relative">
              <button class="hover:text-sky-500 transition">
                <svg width="22" height="22" viewBox="0 0 448 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <defs>
                    <linearGradient id="icon-gradient" x1="0" y1="0" x2="1" y2="1">
                      <stop offset="0%" stop-color="#2b3990" />
                      <stop offset="50%" stop-color="#0097cc" />
                      <stop offset="100%" stop-color="#8dc63f" />
                    </linearGradient>
                  </defs>

                  <path fill="url(#icon-gradient)" d="M192 104.8c0-9.2-5.8-17.3-13.2-22.8C167.2 73.3 160 61.3 160 48c0-26.5 28.7-48 64-48s64 21.5 64 48c0 13.3-7.2 25.3-18.8 34c-7.4 5.5-13.2 13.6-13.2 22.8c0 12.8 10.4 23.2 23.2 23.2l56.8 0c26.5 0 48 21.5 48 48l0 56.8c0 12.8 10.4 23.2 23.2 23.2c9.2 0 17.3-5.8 22.8-13.2c8.7-11.6 20.7-18.8 34-18.8c26.5 0 48 28.7 48 64s-21.5 64-48 64c-13.3 0-25.3-7.2-34-18.8c-5.5-7.4-13.6-13.2-22.8-13.2c-12.8 0-23.2 10.4-23.2 23.2L384 464c0 26.5-21.5 48-48 48l-56.8 0c-12.8 0-23.2-10.4-23.2-23.2c0-9.2 5.8-17.3 13.2-22.8c11.6-8.7 18.8-20.7 18.8-34c0-26.5-28.7-48-64-48s-64 21.5-64 48c0 13.3 7.2 25.3 18.8 34c7.4 5.5 13.2 13.6 13.2 22.8c0 12.8-10.4 23.2-23.2 23.2L48 512c-26.5 0-48-21.5-48-48L0 343.2C0 330.4 10.4 320 23.2 320c9.2 0 17.3 5.8 22.8 13.2C54.7 344.8 66.7 352 80 352c26.5 0 48-28.7 48-64s-21.5-64-48-64c-13.3 0-25.3 7.2-34 18.8C40.5 250.2 32.4 256 23.2 256C10.4 256 0 245.6 0 232.8L0 176c0-26.5 21.5-48 48-48l120.8 0c12.8 0 23.2-10.4 23.2-23.2z" />
                </svg>
              </button>
              <div class="absolute top-full right-0 w-full h-5 group-hover:block hidden z-0"></div>
              <div class="absolute top-full right-0 mt-1 w-72 bg-white border border-gray-200 rounded shadow-lg p-3 z-50 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200">
                <p class="text-sm font-semibold mb-2 text-gray-700">📦 Mọi người thường mua cùng:</p>
                <ul class="space-y-3 text-sm text-gray-600">
                  <li>
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg transition hover:bg-gray-50">
                      <NuxtImg src="/images/products/iphone-16-pro-max.webp" loading="lazy" alt="Ốp lưng silicon" class="w-12 h-12 object-cover rounded-md" />
                      <div class="flex-1">
                        <h4 class="font-medium text-gray-700 transition line-clamp-1 group-hover:text-sky-500">Ốp lưng silicon iPhone</h4>
                        <span class="text-xs text-gray-500 line-clamp-1">Chống sốc, chống bám vân tay</span>
                      </div>
                      <div class="text-right">
                        <span class="text-red-600 font-semibold whitespace-nowrap">+29.000 ₫</span>
                        <span class="block text-xs text-gray-400 line-through">59.000 ₫</span>
                      </div>
                    </a>
                  </li>

                  <li>
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg transition hover:bg-gray-50">
                      <NuxtImg src="/images/products/iphone-16-pro-max.webp" loading="lazy" alt="Dán cường lực" class="w-12 h-12 object-cover rounded-md" />
                      <div class="flex-1">
                        <h4 class="font-medium text-gray-700 transition line-clamp-1 group-hover:text-sky-500">Dán cường lực full viền</h4>
                        <span class="text-xs text-gray-500 line-clamp-1">Độ cứng 9H, chống xước</span>
                      </div>
                      <div class="text-right">
                        <span class="text-red-600 font-semibold whitespace-nowrap">+39.000 ₫</span>
                        <span class="block text-xs text-gray-400 line-through">79.000 ₫</span>
                      </div>
                    </a>
                  </li>

                  <li>
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg transition hover:bg-gray-50">
                      <NuxtImg src="/images/products/iphone-16-pro-max.webp" loading="lazy" alt="Cáp sạc nhanh" class="w-12 h-12 object-cover rounded-md" />
                      <div class="flex-1">
                        <h4 class="font-medium text-gray-700 transition line-clamp-1 group-hover:text-sky-500">Cáp sạc nhanh 20W</h4>
                        <span class="text-xs text-gray-500 line-clamp-1">Sạc nhanh PD, chip thông minh</span>
                      </div>
                      <div class="text-right">
                        <span class="text-red-600 font-semibold whitespace-nowrap">+89.000 ₫</span>
                        <span class="block text-xs text-gray-400 line-through">159.000 ₫</span>
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("client.wishlist.description") }}</p>
      </div>

      <!-- Filter Section - Đặt bên dưới desc -->
      <div v-if="!isLoading && wishlistGrouped.length > 0" class="mb-4 md:mb-6">
        <div class="flex flex-wrap items-center gap-2 md:gap-3 pb-3 border-b border-gray-200">
          <!-- Filter Button -->
          <button @click="showFilterModal = true" class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-600 hover:border-sky-500 hover:text-sky-500 transition" :class="{ 'bg-sky-50 border-sky-500 text-sky-500': hasActiveFilters }">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Bộ lọc
          </button>

          <!-- Catalogue Filter -->
          <select v-model="filters.productCatalogueId" @change="applyFilters" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-600 hover:border-sky-500 hover:text-sky-500 transition focus:outline-none focus:ring-2 focus:ring-sky-500" :class="{ 'bg-sky-50 border-sky-500 text-sky-500': filters.productCatalogueId }">
            <option :value="null">Tất cả danh mục</option>
            <option v-for="cat in catalogues" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>

          <!-- Sort -->
          <div class="ml-auto flex items-center gap-2">
            <span class="text-xs md:text-sm text-gray-600 font-medium">Sắp xếp theo:</span>
            <FilterSort :sort="sortValue" :setSort="handleSort" />
          </div>
        </div>
      </div>

      <!-- Filter Modal (Mobile & Desktop) -->
      <div v-if="showFilterModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showFilterModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
          <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Bộ lọc</h3>
            <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="flex-1 overflow-y-auto p-4">
            <LazyFilterBar :hideAttributes="true" @update:price="handlePriceChange" @update:brand="handleBrandChange" />
          </div>
          <div class="flex items-center gap-3 p-4 border-t border-gray-200">
            <button @click="resetFilters" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">Đóng</button>
            <button @click="applyFilters" class="flex-1 px-4 py-2 bg-sky-500 text-white rounded-md hover:bg-sky-600 transition">Xem kết quả</button>
          </div>
        </div>
      </div>

      <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
          <p class="text-gray-600">{{ $t("client.wishlist.loading") }}</p>
        </div>
      </div>
      <div v-else-if="filteredProducts.length === 0" class="flex items-center justify-center min-h-[400px]">
        <div class="text-center">
          <p class="text-gray-600">{{ $t("client.wishlist.noItems") }}</p>
        </div>
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <ProductCardWishlist v-for="(item, index) in filteredProducts" :key="item.id || index" :product="item" @removed="handleProductRemoved" />
      </div>
      <!-- xem thêm  -->
      <div v-if="hasMore && filteredProducts.length > 0" class="w-full flex justify-center mt-4 md:mt-6">
        <button @click="loadMore" type="button" class="relative flex items-center py-1.5 px-4 overflow-hidden font-medium transition-all bg-sky-500 rounded group">
          <span class="absolute inset-0 transition-all duration-500 ease-in-out delay-200 -translate-x-full bg-sky-600 rounded-md group-hover:translate-x-0"></span>
          <span class="relative w-full text-xs sm:text-sm md:text-base text-center text-white transition-colors duration-200 ease-in-out group-hover:text-white">{{ $t("client.wishlist.loadMore") }}</span>
        </button>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import ProductCardWishlist from "~/components/client/features/ProductCardWishlist.vue";
import FilterBar from "~/components/client/ui/FilterBar.vue";
import FilterSort from "~/components/client/features/FilterSort.vue";
import { useWishlist } from "~/composables/client/product/useWishlist";
import { productCatalogueService } from "~/services/product/catalogue.service";

interface WishlistProduct {
  id: number;
  name: string;
  slug: string;
  priceOrigin: number;
  album?: Array<{ path: string }>;
  productCatalogueId?: number;
  brandId?: number;
  variants: Array<{ id: number; name: string; wishlistId?: number }>;
}

const { wishlistGrouped, isLoading, paginate, fetchWishlist } = useWishlist();
const filteredProducts = ref<WishlistProduct[]>([]);
const currentPage = ref(1);
const showFilterModal = ref(false);
let filterDebounceTimer: ReturnType<typeof setTimeout> | null = null;

// Load catalogues from DB
const catalogues = ref<Array<{ id: number; name: string }>>([]);

// Filter state
const filters = ref({
  price: [1000, 300000000] as number[],
  brand: null as number | null,
  productCatalogueId: null as number | null,
});
const sortValue = ref("price_origin,desc");

const hasMore = computed(() => {
  const total = paginate.value?.data?.total || 0;
  return wishlistGrouped.value.length < total;
});

const hasActiveFilters = computed(() => {
  const hasBrand = filters.value.brand !== null;
  const hasPrice = filters.value.price[0] !== 1000 || filters.value.price[1] !== 300000000;
  const hasCatalogue = filters.value.productCatalogueId !== null;
  return hasBrand || hasPrice || hasCatalogue;
});

const loadMore = async () => {
  if (isLoading.value || !hasMore.value) return;
  currentPage.value += 1;
  await loadWishlistProducts();
};

// Load catalogues
// Cache catalogues để tránh reload nhiều lần
let cataloguesCache: Array<{ id: number; name: string }> | null = null;

const loadCatalogues = async () => {
  if (cataloguesCache) {
    catalogues.value = cataloguesCache;
    return;
  }
  try {
    const res = await productCatalogueService.all();
    const data = res?.data || [];
    cataloguesCache = Array.isArray(data) ? data : [data];
    catalogues.value = cataloguesCache;
  } catch (e) {
    console.error("Lỗi khi load catalogues:", e);
  }
};

const loadWishlistProducts = async () => {
  await fetchWishlist(currentPage.value, 10);
  // Debounce filter để tránh apply nhiều lần khi data load
  clearTimeout(filterDebounceTimer);
  filterDebounceTimer = setTimeout(() => {
    applyFilters();
  }, 100);
};

const handlePriceChange = (newRange: number[]) => {
  filters.value.price = newRange;
};

const handleBrandChange = (brandId: number | null) => {
  filters.value.brand = brandId;
};

const handleSort = (sort: string) => {
  sortValue.value = sort;
  applyFilters();
};

const handleProductRemoved = () => {
  // Re-apply filters khi product bị xóa
  applyFilters();
};

const applyFilters = () => {
  // Clear previous debounce timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer);
  }

  filterDebounceTimer = setTimeout(() => {
    let result = [...wishlistGrouped.value];

    // Filter by price (using product price)
    const [minPrice, maxPrice] = filters.value.price;
    result = result.filter((item) => {
      const price = item.priceOrigin || 0;
      return price >= minPrice && price <= maxPrice;
    });

    // Filter by brand
    if (filters.value.brand !== null) {
      result = result.filter((item) => item.brandId === filters.value.brand);
    }

    // Filter by product catalogue
    if (filters.value.productCatalogueId !== null) {
      result = result.filter((item) => item.productCatalogueId === filters.value.productCatalogueId);
    }

    // Sort
    const [sortField, sortOrder] = sortValue.value.split(",");
    result.sort((a, b) => {
      let aVal: any;
      let bVal: any;

      if (sortField === "price" || sortField === "price_origin") {
        aVal = a.priceOrigin || 0;
        bVal = b.priceOrigin || 0;
      } else if (sortField === "name") {
        aVal = (a.name || "").toLowerCase();
        bVal = (b.name || "").toLowerCase();
      } else {
        aVal = a[sortField as keyof typeof a];
        bVal = b[sortField as keyof typeof b];
      }

      if (sortOrder === "asc") {
        return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
      } else {
        return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
      }
    });

    filteredProducts.value = result;
    showFilterModal.value = false;
  }, 150);
};

const resetFilters = () => {
  filters.value = {
    price: [1000, 300000000],
    brand: null,
    productCatalogueId: null,
  };
  sortValue.value = "price_origin,desc";
  applyFilters();
};

// Watch wishlistGrouped để tự động re-apply filters khi có thay đổi
// Chỉ watch length và reference thay đổi, không cần deep watch
watch(
  () => wishlistGrouped.value.length,
  () => {
    applyFilters();
  }
);

// Watch filters và sort để apply khi thay đổi (debounce tự động trong applyFilters)
watch([() => filters.value.price[0], () => filters.value.price[1], () => filters.value.brand, () => filters.value.productCatalogueId, sortValue], () => {
  applyFilters();
});

onMounted(async () => {
  // Load wishlist trước (quan trọng nhất)
  await loadWishlistProducts();
  // Load catalogues sau (không block render)
  loadCatalogues();
});

onBeforeUnmount(() => {
  // Cleanup debounce timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer);
    filterDebounceTimer = null;
  }
});

const breadcrumbs = computed(() => [{ label: $t("client.wishlist.breadcrumb"), href: "/wishlist" }]);

definePageMeta({
  layout: "client",
  middleware: "auth-required",
});
</script>
