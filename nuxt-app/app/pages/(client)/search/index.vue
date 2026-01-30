<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8">
      <div class="h-1 relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-300 via-green-300 to-[rgba(43,57,144,0.91)]"></div>
      </div>
      <div class="relative w-full bg-white text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] p-6 md:px-8 overflow-hidden rounded-b-lg">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center flex-wrap">
          <div>
            <h2 class="text-2xl md:text-3xl font-semibold mb-1 text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ $t("common.searchResults") }}</h2>
            <p class="text-sm text-gray-600 md:text-md">
              {{ $t("common.found") }}
              <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ totalProducts }}</span>
              {{ $t("common.results") }}
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
          {{ $t("common.showing") }}
          <span class="text-sky-400 underline">{{ totalProducts }} {{ $t("common.results") }}</span>
        </span>
      </div>
      <div class="relative inline-block group">
        <FilterSort :sort="filters.sort" :setSort="setSort" />
      </div>
    </div>

    <DataState :loading="status === 'pending'" :error="false" :data="products" :show-empty-image="true">
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 px-4 mb-5">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, computed } from "vue";
import { useRoute } from "vue-router";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import ProductCard from "~/components//features/ProductCard.vue";
import FilterSort from "~/components//features/FilterSort.vue";
import DataState from "~/components/ui/DataState.vue";
import { useShop } from "~/composables/shop/useShop";
import { EMPTYBOX, TOSEARCH } from "~/utils/constants";
import { definePageMeta } from "#imports";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const route = useRoute();
const queryKeyword = ref((route.query.keyword as string) || "");

const { products, totalProducts, filters, fetchProducts, setSort } = useShop();

const perpage = 20;

const { status } = await useAsyncData(
  "search-products",
  async () => {
    const q = (route.query.keyword as string) || "";
    queryKeyword.value = q;
    filters.value.keyword = q;
    return await fetchProducts(1, perpage, false);
  },
  {
    watch: [() => route.query.keyword],
  },
);

const breadcrumbs = computed(() => [{ label: t("search.breadcrumb"), href: TOSEARCH }]);

definePageMeta({
  layout: "client",
});
</script>
