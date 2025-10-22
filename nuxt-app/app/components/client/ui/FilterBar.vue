<template>
  <aside class="product-filter flex-shrink-0 w-full md:w-[280px] mb-3 md:mb-0 sticky top-4">
    <div class="p-1 md:p-2 overflow-y-auto max-h-[126vh] custom-scrollbar">
      <h2 class="text-transparent text-start bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg sm:text-md font-bold pb-2">Bộ lọc sản phẩm</h2>
      <div class="filter-scroll"></div>
      <Accordion multiple>
        <AccordionPanel v-for="catalogue in attributeCatalogues" :key="catalogue.id" :value="catalogue.id.toString()">
          <AccordionHeader>{{ catalogue.name }}</AccordionHeader>
          <AccordionContent>
            <div class="flex flex-col gap-2 py-2">
              <div v-for="attr in catalogue.attributes" :key="attr.id" class="flex items-center gap-2 py-1 text-[14px]">
                <Checkbox v-model="selectedAttributes[catalogue.slug]" :inputId="`attr-${catalogue.slug}-${attr.id}`" :value="attr.id" />
                <label :for="`attr-${catalogue.slug}-${attr.id}`" class="text-gray-700">
                  {{ attr.name }}
                </label>
              </div>
            </div>
          </AccordionContent>
        </AccordionPanel>
      </Accordion>
      <Accordion multiple>
        <!-- Bộ lọc giá -->
        <AccordionPanel value="price">
          <AccordionHeader>Giá tiền</AccordionHeader>
          <AccordionContent>
            <div class="flex flex-col gap-3 py-2">
              <Slider v-model="priceRange" range :min="minPrice" :max="maxPrice" class="w-full" />
              <div class="flex items-center justify-between gap-3">
                <div class="flex flex-col w-1/2">
                  <label class="text-sm text-gray-500">Giá tối thiểu</label>
                  <InputNumber v-model="priceRange[0]" :min="minPrice" :max="priceRange[1]" mode="currency" currency="VND" locale="vi-VN" inputClass="w-full" />
                </div>
                <div class="flex flex-col w-1/2">
                  <label class="text-sm text-gray-500">Giá tối đa</label>
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
import { ref, onMounted } from "vue";
import Checkbox from "primevue/checkbox";
import Accordion from "primevue/accordion";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";

const attributeCatalogues = ref([]);
const selectedAttributes = ref({});
const minPrice = 1000;
const maxPrice = 100000000;
const priceRange = ref([1000000, 100000000]);

const fetchAttributeCatalogues = async () => {
  try {
    const res = await attributeCatalogueService.all();
    attributeCatalogues.value = res.data || [];
    res.data.forEach((item) => {
      selectedAttributes.value[item.slug] = [];
    });
  } catch (e) {
    console.error("Lỗi khi load attribute catalogues:", e);
  }
};

onMounted(() => {
  fetchAttributeCatalogues();
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
.p-accordionheader-toggle-icon{
  font-size: 10px !important;
}
</style>
