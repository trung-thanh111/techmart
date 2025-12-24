<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- Breadcrumb  -->
    <Breadcrumb :crumbs="breadcrumbs" />
    <!-- Search Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8">
      <div class="h-1 relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-300 via-green-300 to-[rgba(43,57,144,0.91)]"></div>
      </div>
      <div class="relative w-full bg-white text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] p-6 md:px-8 overflow-hidden rounded-b-lg">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center flex-wrap">
          <div>
            <h2 class="text-2xl md:text-3xl font-semibold mb-1 text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">Kết quả tìm kiếm</h2>
            <p class="text-sm text-gray-600 md:text-md">
              Tìm thấy
              <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ totalProducts }}</span>
              kết quả
            </p>
          </div>
        </div>
        <div class="mt-5 flex flex-wrap gap-3 justify-center md:justify-start">
          <span class="text-lg text-gray-800 font-medium">
            <span class="text-gray-700 font-semibold">"{{ queryKeyword }}"</span>
          </span>
        </div>
      </div>
    </div>

    <div class="flex justify-between items-center mb-4">
      <div>
        <span class="text-sm">
          Hiển thị
          <span class="text-sky-400 underline">{{ totalProducts }} kết quả</span>
        </span>
      </div>
      <div class="relative inline-block group">
        <FilterSort :sort="filters.sort" :setSort="setSort" />
      </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 px-4">
      <ProductCard v-for="product in products" :key="product.id" :product="product" />
    </div>

    <div v-if="!products || products.length === 0" class="flex flex-col items-center justify-center py-16">
      <!-- <img src="/public/images/empty_state.png" alt="Không tìm thấy" loading="lazy" class="w-64 h-64 mb-6 object-cover" /> -->
      <h3 class="text-xl uppercase font-bold text-gray-700 mb-1">Không tìm thấy sản phẩm nào</h3>
      <p class="text-gray-500 text-sm mb-4">Hãy thử từ khoá khác hoặc kiểm tra lại bộ lọc tìm kiếm của bạn.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import ProductCard from "~/components/client/features/ProductCard.vue";
import FilterSort from "~/components/client/features/FilterSort.vue";
import { useShop } from "~/composables/client/shop/useShop";

const route = useRoute();
const queryKeyword = ref((route.query.keyword as string) || "");

const { products, totalProducts, filters, fetchProducts, setSort } = useShop();

const perpage = 20;

const load = async (page = 1) => {
  filters.value.keyword = queryKeyword.value || "";
  await fetchProducts(page, perpage, false);
};

watch(
  () => route.query.keyword,
  (val) => {
    queryKeyword.value = (val as string) || "";
    load(1);
  }
);

onMounted(async () => {
  await load(1);
});

const breadcrumbs = computed(() => [{ label: $t("client.search.breadcrumb"), href: "/search" }]);

definePageMeta({
  layout: "client",
});
</script>
