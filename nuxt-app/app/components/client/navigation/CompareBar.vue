<template>
  <transition name="slide-up">
    <div v-if="showCompareBar && !isComparePage" class="fixed bottom-0 left-0 right-0 z-50 bg-white shadow-2xl px-4 py-2">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-start justify-between text-sm">
          <span class="font-medium text-gray-800">
            <span class="text-teal-400 font-bold">{{ compareCount }}</span>
            /3 {{ $t("client.utilityTool.productsCompared") }}
          </span>
          <button class="text-blue-400 font-normal flex items-center gap-1 hover:text-blue-300 transition-colors" @click="toggleExpand">
            {{ isExpanded ? $t("client.utilityTool.hide") : $t("client.utilityTool.show") }}
            <svg :class="['w-4 h-4 transition-transform', { 'rotate-180': isExpanded }]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
          </button>
        </div>

        <transition name="expand">
          <div v-show="isExpanded" class="grid grid-cols-4 gap-4 mt-2">
            <template v-for="i in 3" :key="i">
              <ProductCardCompare v-if="compareList[i - 1]" :item="compareList[i - 1]!" @remove="() => handleRemoveProduct(compareList[i - 1]!.variantId)" class="bg-white rounded-lg" />
              <button v-else @click="openSearchModal" class="border-2 border-dashed border-gray-400 rounded-lg p-4 flex flex-col items-center justify-center min-h-[120px] hover:border-sky-400 hover:bg-sky-900/20 transition-colors">
                <span class="text-4xl text-gray-400 mb-2">+</span>
                <span class="text-sm text-gray-400">{{ $t("client.utilityTool.addProduct") }}</span>
              </button>
            </template>
            <div class="flex flex-col gap-2 justify-center items-center">
              <Button :label="$t('client.utilityTool.compareNow')" icon="pi pi-check" severity="info" @click="goToComparePage" class="w-full" size="small" :disabled="compareCount === 0" />
              <Button :label="$t('client.utilityTool.clearAllProducts')" severity="danger" outlined @click="handleClearAll" class="w-full" size="small" :disabled="compareCount === 0" />
            </div>
          </div>
        </transition>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from "vue";
import { useCompare } from "~/composables/client/product/useCompare";
import ProductCardCompare from "~/components/client/features/ProductCardCompare.vue";
import { useRoute } from "vue-router";

const { compareList, compareCount, isFull, isEmpty, removeFromCompare, clearAll } = useCompare();
const route = useRoute();

const showCompareBar = computed(() => !isEmpty.value);
const isComparePage = computed(() => route.path === "/product/compare");
const isExpanded = ref(true);

const emit = defineEmits<{
  "open-search": [];
}>();

const handleRemoveProduct = (variantId: number) => {
  removeFromCompare(variantId);
};

const handleClearAll = () => {
  clearAll();
};

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
};

const openSearchModal = () => {
  emit("open-search");
};

const goToComparePage = () => {
  navigateTo("/product/compare");
};

onMounted(() => {
  // Auto expand khi có sản phẩm
  if (compareCount.value > 0) {
    isExpanded.value = true;
  }
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}
.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}
.expand-enter-to,
.expand-leave {
  max-height: 500px;
  opacity: 1;
}
</style>
