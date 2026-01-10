<template>
  <div class="product-card-compare relative border border-gray-300 rounded-lg p-3 bg-white hover:shadow-md transition-shadow">
    <button v-if="showRemove" @click="$emit('remove')" class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div class="flex flex-col items-center">
      <div class="w-20 h-20 mb-2 flex items-center justify-center overflow-hidden rounded">
        <NuxtImg :src="item.image" :alt="item.fullName" class="w-full h-full object-contain" loading="lazy" />
      </div>

      <div class="text-center w-full">
        <h4 class="text-xs font-medium text-gray-700 mb-1 line-clamp-2 min-h-[2.5rem]">{{ item.fullName }}</h4>
        <p class="text-sm font-bold text-red-600">{{ formatPrice(item.price) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICompareItem } from "~/stores/client/product/compare";
import { formatPrice } from "~/utils/helpers";

interface Props {
  item: ICompareItem;
  showRemove?: boolean;
}

withDefaults(defineProps<Props>(), {
  showRemove: true,
});

defineEmits<{
  remove: [];
}>();
</script>
