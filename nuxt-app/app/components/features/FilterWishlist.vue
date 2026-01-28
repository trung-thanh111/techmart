<template>
  <div class="flex flex-wrap items-center gap-2 md:gap-3">
    <!-- Filter Button (UI only) -->
    <button class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-600 transition" :class="{ 'bg-sky-50 border-sky-500 text-sky-500': hasActiveFilters }" disabled>
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
      </svg>
      {{ $t("common.filter") }}
    </button>

    <!-- Price Filter -->
    <FilterPriceDropdown :priceRange="filters.price" @update:price="handlePriceChange" />

    <!-- Catalogue MultiSelect -->
    <div class="relative">
      <MultiSelect v-model="selectedCatalogues" :options="catalogues" optionLabel="name" optionValue="id" :placeholder="$t('common.cataloguePlaceholder')" display="chip" filter showClear class="w-[180px] text-sm" :class="{ 'filter-active': selectedCatalogues.length > 0 }" @change="handleCatalogueChange" />
    </div>

    <!-- Brand MultiSelect -->
    <div class="relative">
      <MultiSelect v-model="selectedBrands" :options="brands" optionLabel="name" optionValue="id" :placeholder="$t('common.brandPlaceholder')" display="chip" filter showClear class="w-[180px] text-sm" :class="{ 'filter-active': selectedBrands.length > 0 }" @change="handleBrandChange" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import MultiSelect from "primevue/multiselect";
import FilterPriceDropdown from "./FilterPriceDropdown.vue";

interface FilterProps {
  price: [number, number];
  catalogues: number[];
  brands: number[];
}

const props = defineProps<{
  filters: FilterProps;
  catalogues: Array<{ id: number; name: string }>;
  brands: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits<{
  "update:price": [range: [number, number]];
  "update:catalogues": [ids: number[]];
  "update:brands": [ids: number[]];
}>();

const selectedCatalogues = ref<number[]>(props.filters.catalogues || []);
const selectedBrands = ref<number[]>(props.filters.brands || []);

const hasActiveFilters = computed(() => {
  const hasPrice = props.filters.price[0] !== 1000 || props.filters.price[1] !== 300000000;
  const hasCatalogues = props.filters.catalogues.length > 0;
  const hasBrands = props.filters.brands.length > 0;
  return hasPrice || hasCatalogues || hasBrands;
});

const handlePriceChange = (range: [number, number]) => {
  emit("update:price", range);
};

const handleCatalogueChange = () => {
  emit("update:catalogues", selectedCatalogues.value);
};

const handleBrandChange = () => {
  emit("update:brands", selectedBrands.value);
};

watch(
  () => props.filters.catalogues,
  (newVal) => {
    selectedCatalogues.value = newVal || [];
  },
  { immediate: true },
);

watch(
  () => props.filters.brands,
  (newVal) => {
    selectedBrands.value = newVal || [];
  },
  { immediate: true },
);
</script>

<style scoped>
:deep(.p-multiselect.filter-active .p-multiselect-label) {
  @apply bg-sky-50 border-sky-500 text-sky-600;
}
:deep(.p-multiselect.filter-active) {
  @apply border-sky-500;
}
:deep(.p-multiselect) {
  @apply border-gray-300 rounded-md text-sm;
}
:deep(.p-multiselect:not(.p-disabled):hover) {
  @apply border-sky-500;
}
:deep(.p-multiselect-chip) {
  @apply bg-sky-100 text-sky-700 text-xs;
}
</style>
