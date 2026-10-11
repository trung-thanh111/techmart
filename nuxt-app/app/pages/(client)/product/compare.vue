<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <div class="compare-page">
      <div class="container mx-auto">
        <DataState :loading="false" :error="false" :data="compareList" :show-empty-image="true">
          <div class="space-y-8">
            <section class="bg-white rounded-lg shadow-md p-4 md:p-6">
              <!-- Action bar: Clear All & Mobile swipe indicator -->
              <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                  <h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-800">{{ $t("utilityTool.compareProduct") }}</h2>
                  <span class="text-xs bg-sky-50 text-sky-600 font-semibold px-2 py-0.5 rounded-full">({{ compareList.length }}/3)</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                  <div class="md:hidden flex items-center gap-1 text-[11px] sm:text-xs text-sky-600 bg-sky-50 px-2.5 py-1 rounded-md">
                    <i class="pi pi-arrows-h text-[10px]"></i>
                    <span>Vuốt ngang để xem</span>
                  </div>
                  <button
                    v-if="compareList.length > 0"
                    @click="handleClearAll"
                    type="button"
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-red-600 hover:text-red-700 hover:bg-red-50 px-2.5 py-1 rounded-md transition-colors font-medium"
                  >
                    <i class="pi pi-trash text-xs"></i>
                    <span>{{ $t("action.clearAll") || "Xóa tất cả" }}</span>
                  </button>
                </div>
              </div>

              <div class="overflow-x-auto">
                <div class="grid grid-cols-4 gap-4 min-w-[800px]">
                  <div class="font-semibold text-gray-700 flex flex-col justify-center">
                    <h3 class="text-sm sm:text-base md:text-xl font-semibold text-gray-800 mb-2">{{ $t("common.product") }}</h3>
                    <p class="text-xs text-gray-400 font-normal">Tối đa 3 sản phẩm so sánh</p>
                  </div>

                  <template v-for="i in 3" :key="i">
                    <ProductCardCompare v-if="compareList[i - 1]" :item="compareList[i - 1]!" @remove="() => handleRemoveProduct(compareList[i - 1]!.variantId)" class="bg-white rounded-lg" />
                    <button v-else @click="openSearchModal" class="border-2 border-dashed border-gray-400 rounded-lg p-4 flex flex-col items-center justify-center min-h-[120px] hover:border-sky-400 hover:bg-sky-900/20 transition-colors">
                      <span class="text-4xl text-gray-400 mb-2">+</span>
                      <span class="text-sm text-gray-400">{{ $t("action.addProduct") }}</span>
                    </button>
                  </template>
                </div>
              </div>
            </section>

            <section class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-5">
              <div v-if="isLoadingSpecs" class="text-center py-8">
                <ProgressSpinner />
                <p class="text-gray-500 mt-4 text-xs sm:text-sm md:text-base">{{ $t("common.loading") }}</p>
              </div>
              <div v-else-if="specifications.length === 0" class="text-center py-8 text-gray-500 text-xs sm:text-sm md:text-base">
                <p>{{ $t("common.noSpec") }}</p>
              </div>
              <div v-else class="overflow-x-auto">
                <div class="md:hidden flex items-center gap-1 text-[11px] sm:text-xs text-sky-600 bg-sky-50 px-2.5 py-1 rounded-md w-fit mb-3">
                  <i class="pi pi-arrows-h text-[10px]"></i>
                  <span>Vuốt ngang để xem chi tiết</span>
                </div>
                <table class="w-full border-collapse min-w-[700px] md:min-w-full" style="table-layout: fixed">
                  <colgroup>
                    <col style="width: 25%" />
                    <col style="width: 25%" v-for="n in 3" :key="n" />
                  </colgroup>
                  <thead>
                    <tr class="bg-gray-50">
                      <th class="border border-gray-200 px-3 py-3 md:px-4 md:py-4 text-sm sm:text-base md:text-xl text-left font-semibold text-gray-800">{{ $t("header.specifications") }}</th>
                      <th v-for="(item, index) in compareList" :key="item.variantId" class="border border-gray-200 px-3 py-3 md:px-4 md:py-4 text-center font-semibold text-gray-700">
                        <div class="line-clamp-2 md:line-clamp-3 text-xs sm:text-sm">{{ item.fullName }}</div>
                      </th>
                      <th v-if="compareList.length < 3" v-for="n in 3 - compareList.length" :key="`empty-header-${n}`" class="border border-gray-200 px-3 py-3 md:px-4 md:py-4"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(spec, specIndex) in specifications" :key="specIndex" class="hover:bg-gray-50 transition-colors">
                      <td class="border border-gray-200 px-3 py-3 md:px-4 md:py-5 font-medium text-gray-700 bg-gray-50 text-xs sm:text-sm">
                        {{ spec.attribute }}
                      </td>
                      <td v-for="(item, itemIndex) in compareList" :key="item.variantId" class="border border-gray-200 px-3 py-3 md:px-4 md:py-5 text-gray-600 text-xs sm:text-sm">
                        {{ spec[`product${itemIndex}`] || "-" }}
                      </td>
                      <td v-if="compareList.length < 3" v-for="n in 3 - compareList.length" :key="`empty-cell-${n}`" class="border border-gray-200 px-3 py-3 md:px-4 md:py-5"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
          <template #empty>
            <div class="text-center py-12">
              <Button :label="$t('nav.shop')" icon="pi pi-arrow-left" severity="info" @click="goToShop" />
            </div>
          </template>
        </DataState>
      </div>

      <SearchProductModal v-model="showSearchModal" selection-mode="record" @added="handleProductsAdded" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useCompare } from "~/composables/product/useCompare";
import { TOSHOP, TOCOMPARE } from "~/utils/constants";
import ProductCardCompare from "~/components/features/ProductCardCompare.vue";
import SearchProductModal from "~/components/features/SearchProductModal.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import DataState from "~/components/ui/DataState.vue";
import { EMPTYBOX } from "~/utils/constants";
import { useI18n } from "vue-i18n";
import { navigateTo } from "#app";
import { definePageMeta } from "#imports";

const { t } = useI18n();
const { compareList, removeFromCompare, clearAll, loadProductDetail } = useCompare();

const showSearchModal = ref(false);
const isLoadingSpecs = ref(false);
const productDetails = ref<Map<number, any>>(new Map());

const breadcrumbs = computed(() => [
  { label: t("page.shop"), href: TOSHOP },
  { label: t("utilityTool.compareProduct"), href: TOCOMPARE },
]);

interface SpecificationRow {
  attribute: string;
  [key: string]: string;
}

const specifications = computed<SpecificationRow[]>(() => {
  if (compareList.value.length === 0) return [];

  const rows: SpecificationRow[] = [];
  const attributeMap = new Map<string, Map<number, string>>();

  compareList.value.forEach((item, index) => {
    const variant = item.variant;
    if (variant?.attributeData) {
      Object.entries(variant.attributeData).forEach(([attrName, attrValue]: [string, any]) => {
        const value = attrValue?.name || attrValue?.value || String(attrValue || "-");
        if (!attributeMap.has(attrName)) {
          attributeMap.set(attrName, new Map());
        }
        attributeMap.get(attrName)!.set(index, value);
      });
    }
  });

  // Tạo rows
  attributeMap.forEach((valueMap, attrName) => {
    const row: SpecificationRow = { attribute: attrName };
    compareList.value.forEach((_, index) => {
      row[`product${index}`] = valueMap.get(index) || "-";
    });
    rows.push(row);
  });

  return rows;
});

const handleRemoveProduct = (variantId: number) => {
  removeFromCompare(variantId);
};

const handleClearAll = () => {
  clearAll();
};

const handleProductsAdded = () => {
  showSearchModal.value = false;
  loadAllProductDetails();
};

const openSearchModal = () => {
  showSearchModal.value = true;
};

const goToShop = () => {
  navigateTo(TOSHOP);
};

const loadAllProductDetails = async () => {
  isLoadingSpecs.value = true;
  try {
    const loadPromises = compareList.value.map(async (item) => {
      if (!productDetails.value.has(item.productId)) {
        const detail = await loadProductDetail(item.productId);
        if (detail) {
          productDetails.value.set(item.productId, detail);
        }
      }
    });

    await Promise.all(loadPromises);

    // Update variants với attributeData đầy đủ từ detail
    compareList.value.forEach((item) => {
      const detail = productDetails.value.get(item.productId);
      if (detail?.productVariants) {
        const fullVariant = detail.productVariants.find((v: any) => v.id === item.variantId);
        if (fullVariant) {
          item.variant = fullVariant;
        }
      }
    });
  } catch (error) {
    console.error("Error loading product details:", error);
  } finally {
    isLoadingSpecs.value = false;
  }
};

watch(
  () => compareList.value.length,
  () => {
    if (compareList.value.length > 0) {
      loadAllProductDetails();
    }
  },
);

onMounted(() => {
  if (compareList.value.length > 0) {
    loadAllProductDetails();
  }
});

definePageMeta({
  layout: "client",
});
</script>

<style scoped>
.compare-page {
  min-height: calc(100vh - 200px);
}

/* Responsive */
@media (max-width: 1024px) {
  .min-w-\[800px\] {
    min-width: 700px;
  }
}

@media (max-width: 768px) {
  .min-w-\[800px\] {
    min-width: 600px;
  }

  .grid-cols-4 {
    grid-template-columns: repeat(4, minmax(150px, 1fr));
  }
}

@media (max-width: 640px) {
  .min-w-\[800px\] {
    min-width: 500px;
  }
}
</style>
