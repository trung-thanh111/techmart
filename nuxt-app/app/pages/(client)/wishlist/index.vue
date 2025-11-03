<template>
  <div class="main-content max-w-7xl mx-auto">
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
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("client.wishlist.description") }}</p>
      </div>

      <div v-if="!isLoading && wishlistGrouped.length > 0" class="mb-4 md:mb-6">
        <div class="flex flex-wrap items-center justify-between gap-2 md:gap-3 pb-3 border-b border-gray-200">
          <FilterWishlist :filters="filters" :catalogues="catalogues" :brands="brands" @update:price="handlePriceChange" @update:catalogues="handleCatalogueChange" @update:brands="handleBrandChange" />

          <div class="flex items-center gap-2">
            <FilterSort :sort="sortValue" :setSort="handleSort" />
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
      <div v-if="hasMore && filteredProducts.length > 5" class="w-full flex justify-center mt-4 md:mt-6">
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
import FilterWishlist from "~/components/client/features/FilterWishlist.vue";
import FilterSort from "~/components/client/features/FilterSort.vue";
import { useWishlist } from "~/composables/client/product/useWishlist";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { brandService } from "~/services/brand/brand.service";
import type { IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import type { IBrand } from "~/interfaces/brand/brand.interface";

type WishlistProduct = IWishlistGrouped;

const { wishlistGrouped, isLoading, paginate, fetchWishlist } = useWishlist();
const filteredProducts = ref<WishlistProduct[]>([]);
const currentPage = ref(1);
let filterDebounceTimer: ReturnType<typeof setTimeout> | null = null;

// Cache catalogues và brands giống shop
const catalogues = useState<Array<{ id: number; name: string }>>("wishlist-catalogues", () => []);
const brands = useState<Array<{ id: number; name: string }>>("wishlist-brands", () => []);

// Filter state - format giống shop với multiselect (in)
const filters = ref({
  price: [1000, 300000000] as [number, number],
  catalogues: [] as number[],
  brands: [] as number[],
});
const sortValue = ref("price_origin,desc");

const hasMore = computed(() => {
  const total = paginate.value?.data?.total || 0;
  return wishlistGrouped.value.length < total;
});

const loadMore = async () => {
  if (isLoading.value || !hasMore.value) return;
  currentPage.value += 1;
  await fetchWishlist(currentPage.value, 10, true);
  applyFilters();
};

// Load catalogues và brands với cache giống shop
const fetchCatalogues = async () => {
  if (catalogues.value.length) return catalogues.value;
  try {
    const res = await productCatalogueService.all();
    const data = res?.data || [];
    const items = Array.isArray(data) ? data : [data];
    catalogues.value = items.map((item: IProductCatalogue) => ({
      id: item.id,
      name: item.name || "",
    }));
    return catalogues.value;
  } catch (e) {
    console.error("[wishlist] fetchCatalogues error:", e);
    return [];
  }
};

const fetchBrands = async () => {
  if (brands.value.length) return brands.value;
  try {
    const res = await brandService.paginationPublic({
      type: "all",
      publish: 2,
      sort: "created_at,desc",
    });
    let data = res?.data?.data || res?.data || [];
    if (!Array.isArray(data)) {
      data = [data];
    }
    brands.value = data
      .filter((item: IBrand) => item && item.id) // Filter out null/undefined
      .map((item: IBrand) => ({
        id: item.id,
        name: item.name || "",
      }));
    return brands.value;
  } catch (e) {
    console.error("[wishlist] fetchBrands error:", e);
    return [];
  }
};

const loadWishlistProducts = async () => {
  await fetchWishlist(currentPage.value, 10, false);
  applyFilters();
};

const handlePriceChange = (newRange: [number, number]) => {
  filters.value.price = newRange;
  applyFilters();
};

const handleCatalogueChange = (ids: number[]) => {
  filters.value.catalogues = ids;
  applyFilters();
};

const handleBrandChange = (ids: number[]) => {
  filters.value.brands = ids;
  applyFilters();
};

const handleSort = (sort: string) => {
  sortValue.value = sort;
  applyFilters();
};

const handleProductRemoved = () => {
  applyFilters();
};

const applyFilters = () => {
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer);
  }

  filterDebounceTimer = setTimeout(() => {
    let result = [...wishlistGrouped.value];

    // Filter by price
    const [minPrice, maxPrice] = filters.value.price;
    result = result.filter((item) => {
      const price = item.product?.priceOrigin || 0;
      return price >= minPrice && price <= maxPrice;
    });

    // Filter by catalogues (multiselect - in)
    if (filters.value.catalogues.length > 0) {
      result = result.filter((item) => item.product?.productCatalogueId && filters.value.catalogues.includes(item.product.productCatalogueId));
    }

    // Filter by brands (multiselect - in)
    if (filters.value.brands.length > 0) {
      result = result.filter((item) => item.product?.brandId && filters.value.brands.includes(item.product.brandId));
    }

    // Sort - giống shop
    const [sortField, sortOrder] = sortValue.value.split(",");
    result.sort((a, b) => {
      let aVal: any;
      let bVal: any;

      if (sortField === "price" || sortField === "price_origin") {
        aVal = a.product?.priceOrigin || 0;
        bVal = b.product?.priceOrigin || 0;
      } else if (sortField === "name") {
        aVal = (a.product?.name || "").toLowerCase();
        bVal = (b.product?.name || "").toLowerCase();
      } else if (sortField === "created_at") {
        // Có thể cần thêm field created_at vào interface
        aVal = 0;
        bVal = 0;
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
  }, 150);
};

watch(
  () => wishlistGrouped.value.length,
  () => {
    applyFilters();
  }
);

onMounted(async () => {
  await Promise.all([fetchCatalogues(), fetchBrands()]);
  await loadWishlistProducts();
});

onBeforeUnmount(() => {
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
