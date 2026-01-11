<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <div class="compare-page">
      <div class="container mx-auto">
        <div v-if="compareList.length === 0" class="text-center py-12">
          <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500">
            <NuxtImg :src="EMPTYBOX" alt="Empty comparison" class="object-contain" loading="lazy" />
            <p class="text-sm text-center mb-3">{{ $t("client.common.emptyProduct") }}</p>
            <Button label="Cửa hàng" icon="pi pi-arrow-left" severity="info" @click="goToShop" />
          </div>
        </div>

        <div v-else class="space-y-8">
          <section class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="overflow-x-auto">
              <div class="grid grid-cols-4 gap-4 min-w-[800px]">
                <div class="font-semibold text-gray-700">
                  <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ $t("client.common.product") }}</h2>
                  <span class="md:hidden">{{ $t("client.common.product").slice(0, 2) }}</span>
                </div>

                <template v-for="i in 3" :key="i">
                  <ProductCardCompare v-if="compareList[i - 1]" :item="compareList[i - 1]!" @remove="() => handleRemoveProduct(compareList[i - 1]!.variantId)" class="bg-white rounded-lg" />
                  <button v-else @click="openSearchModal" class="border-2 border-dashed border-gray-400 rounded-lg p-4 flex flex-col items-center justify-center min-h-[120px] hover:border-sky-400 hover:bg-sky-900/20 transition-colors">
                    <span class="text-4xl text-gray-400 mb-2">+</span>
                    <span class="text-sm text-gray-400">{{ $t("client.action.addProduct") }}</span>
                  </button>
                </template>
              </div>
            </div>
          </section>

          <section class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-5">
            <div v-if="isLoadingSpecs" class="text-center py-8">
              <ProgressSpinner />
              <p class="text-gray-500 mt-4">{{ $t("client.common.loading") }}</p>
            </div>
            <div v-else-if="specifications.length === 0" class="text-center py-8 text-gray-500">
              <p>{{ $t("client.common.noSpec") }}</p>
            </div>
            <div v-else class="overflow-x-auto">
              <table class="w-full border-collapse" style="table-layout: fixed">
                <colgroup>
                  <col style="width: 25%" />
                  <col style="width: 25%" v-for="n in 3" :key="n" />
                </colgroup>
                <thead>
                  <tr class="bg-gray-50">
                    <th class="border border-gray-200 px-4 py-4 text-xl text-left font-semibold text-gray-800">Đặc tính kỹ thuật</th>
                    <th v-for="(item, index) in compareList" :key="item.variantId" class="border border-gray-200 px-4 py-4 text-center font-semibold text-gray-700">
                      <div class="line-clamp-3 text-sm">{{ item.fullName }}</div>
                    </th>
                    <th v-if="compareList.length < 3" v-for="n in 3 - compareList.length" :key="`empty-header-${n}`" class="border border-gray-200 px-4 py-4"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(spec, specIndex) in specifications" :key="specIndex" class="hover:bg-gray-50 transition-colors">
                    <td class="border border-gray-200 px-4 py-5 font-medium text-gray-700 bg-gray-50">
                      {{ spec.attribute }}
                    </td>
                    <td v-for="(item, itemIndex) in compareList" :key="item.variantId" class="border border-gray-200 px-4 py-5 text-gray-600">
                      {{ spec[`product${itemIndex}`] || "-" }}
                    </td>
                    <td v-if="compareList.length < 3" v-for="n in 3 - compareList.length" :key="`empty-cell-${n}`" class="border border-gray-200 px-4 py-5"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>

      <SearchProductModal v-model="showSearchModal" selection-mode="record" @added="handleProductsAdded" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useCompare } from "~/composables/client/product/useCompare";
import { TOSHOP, TOCOMPARE } from "~/utils/constants";
import ProductCardCompare from "~/components/client/features/ProductCardCompare.vue";
import SearchProductModal from "~/components/client/features/SearchProductModal.vue";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { EMPTYBOX } from "~/utils/constants";
import { useI18n } from "vue-i18n";
import { navigateTo } from "#app";
import { definePageMeta } from "#imports";

const { t } = useI18n();
const { compareList, removeFromCompare, loadProductDetail } = useCompare();

const showSearchModal = ref(false);
const isLoadingSpecs = ref(false);
const productDetails = ref<Map<number, any>>(new Map());

const breadcrumbs = computed(() => [
  { label: t("client.page.shop"), href: TOSHOP },
  { label: t("client.utilityTool.compareProduct"), href: TOCOMPARE },
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
  }
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
