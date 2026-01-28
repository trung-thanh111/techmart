<template>
  <aside class="product-filter flex-shrink-0 w-full md:w-[280px] mb-3 md:mb-0 sticky top-4">
    <div class="p-1 md:p-2 overflow-y-auto max-h-[126vh] custom-scrollbar">
      <div class="flex justify-between items-center gap-2">
        <h2 class="text-transparent text-start bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg sm:text-md font-bold pb-2">Bộ lọc</h2>
        <button v-if="hasActiveFilters" class="flex items-center gap-1 text-xs text-[#0097cc] cursor-pointer hover:text-[#007c8b] transition-colors" @click="handleResetFilters">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ $t("common.resetFilters") }}
        </button>
      </div>
      <div class="filter-scroll"></div>
      <Accordion multiple>
        <AccordionPanel v-for="catalogue in attributeCatalogues.filter((cat) => cat.attributes && cat.attributes.length > 0)" :key="catalogue.id" :value="catalogue.id.toString()">
          <LazyAccordionHeader>
            {{ catalogue.name }}
          </LazyAccordionHeader>
          <AccordionContent>
            <div class="flex flex-col gap-2 py-2">
              <div v-for="attr in catalogue.attributes" :key="attr.id" class="flex items-center gap-2 rounded-md px-2 py-1 transition-colors text-[14px]">
                <Checkbox v-model="selectedAttributes[catalogue.slug]" :inputId="`attr-${catalogue.slug}-${attr.id}`" :value="attr.id" />
                <label :for="`attr-${catalogue.slug}-${attr.id}`" class="text-gray-700 cursor-pointer">
                  {{ attr.name }}
                </label>
              </div>
            </div>
          </AccordionContent>
        </AccordionPanel>
      </Accordion>
      <Accordion multiple>
        <AccordionPanel value="brand">
          <AccordionHeader input-class="text-sm font-semibold text-gray-800">Thương hiệu</AccordionHeader>
          <AccordionContent>
            <div class="flex flex-col gap-2 py-2">
              <div v-for="brand in brands" :key="brand.id" class="flex items-center gap-2 rounded-md px-2 py-1 transition-colors text-[14px]">
                <RadioButton v-model="selectedBrand" :inputId="`brand-${brand.id}`" :value="brand.id" name="brand" />
                <label :for="`brand-${brand.id}`" class="text-gray-700 text-sm cursor-pointer select-none">
                  {{ brand.name }}
                </label>
              </div>
            </div>
          </AccordionContent>
        </AccordionPanel>
      </Accordion>

      <Accordion multiple>
        <AccordionPanel value="price">
          <AccordionHeader>{{ $t("common.price") }}</AccordionHeader>
          <AccordionContent>
            <div class="flex flex-col gap-3 py-2">
              <Slider v-model="priceRange" range :min="minPrice" :max="maxPrice" class="w-full" />
              <div class="flex items-center justify-between gap-3">
                <div class="flex flex-col w-1/2">
                  <label class="text-sm text-gray-500">{{ $t("common.minimumPrice") }}</label>
                  <InputNumber v-model="priceRange[0]" :min="minPrice" :max="priceRange[1]" mode="currency" currency="VND" locale="vi-VN" inputClass="w-full" />
                </div>
                <div class="flex flex-col w-1/2">
                  <label class="text-sm text-gray-500">{{ $t("common.maximumPrice") }}</label>
                  <InputNumber v-model="priceRange[1]" :min="priceRange[0]" :max="maxPrice" mode="currency" currency="VND" locale="vi-VN" inputClass="w-full" />
                </div>
              </div>
            </div>
          </AccordionContent>
        </AccordionPanel>
      </Accordion>
    </div>
  </aside>
</template>
<script setup>
import { ref, onMounted, watch, nextTick } from "vue";
import Checkbox from "primevue/checkbox";
import Accordion from "primevue/accordion";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";
import { brandService } from "~/services/brand/brand.service";
import { useShop } from "~/composables/shop/useShop";

const { resetFilters } = useShop();

const attributeCatalogues = ref([]);
const brands = ref([]);
const emit = defineEmits(["update:attributes", "update:price", "update:brand"]);

const selectedAttributes = ref({});
const selectedBrand = ref(null);
const minPrice = 1000;
const maxPrice = 300000000;

const defaultPriceRange = [minPrice, maxPrice];
const priceRange = ref([...defaultPriceRange]);

let isInitialized = false;
let debounceTimer = null;

watch(selectedAttributes, (newValue) => isInitialized && emit("update:attributes", newValue), { deep: true });

watch(
  priceRange,
  (newValue) => {
    if (!isInitialized) return;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => emit("update:price", newValue), 500);
  },
  { deep: true },
);

watch(selectedBrand, (newValue) => isInitialized && emit("update:brand", newValue));

const fetchAttributeCatalogues = async () => {
  try {
    const res = await attributeCatalogueService.all();
    attributeCatalogues.value = res.data || [];
    res.data.forEach((item) => {
      selectedAttributes.value[item.slug] = [];
    });
  } catch (e) {
    console.error($t("message.errorLoading"), e);
  }
};

const fetchBrands = async () => {
  try {
    const res = await brandService.pagination({
      type: "all",
      publish: 2,
    });
    brands.value = res.data || [];
  } catch (e) {
    console.error($t("message.errorLoading"), e);
  }
};

const hasActiveFilters = computed(() => {
  const hasAttr = Object.values(selectedAttributes.value).some((arr) => Array.isArray(arr) && arr.length > 0);
  const hasBrand = selectedBrand.value !== null && selectedBrand.value !== undefined;
  const hasPrice = priceRange.value[0] !== defaultPriceRange[0] || priceRange.value[1] !== defaultPriceRange[1];
  return hasAttr || hasBrand || hasPrice;
});

const handleResetFilters = async () => {
  Object.keys(selectedAttributes.value).forEach((key) => {
    selectedAttributes.value[key] = [];
  });
  selectedBrand.value = null;
  priceRange.value = [minPrice, maxPrice];
  await resetFilters();
};

onMounted(async () => {
  await Promise.all([fetchAttributeCatalogues(), fetchBrands()]);
  nextTick(() => (isInitialized = true));
});
</script>
<style scoped>
:deep(.p-accordionheader) {
  padding: 10px 0 !important;
  background: transparent !important;
  border: none !important;
  min-height: auto !important;
  font-size: 16px !important;
}

:deep(.p-accordion),
:deep(.p-accordionheader),
:deep(.p-accordionpanel),
:deep(.p-accordion-content) {
  border: none !important;
  box-shadow: none !important;
}

:deep(.p-accordioncontent-content) {
  padding: 0 !important;
}

.p-accordionheader-toggle-icon {
  font-size: 10px !important;
}
</style>
