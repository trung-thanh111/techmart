<template>
  <div class="flex gap-3 py-2">
    <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
      <NuxtImg :src="imageUrl ?? IMAGEDEFAULT" :alt="item.productName" class="w-full h-full object-contain" loading="lazy" />
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-medium text-gray-800 line-clamp-2 mb-1">{{ item.productName || "Sản phẩm" }}</p>
      <p v-if="formattedAttributes" class="text-xs text-gray-500 mb-1">{{ formattedAttributes }}</p>
      <p class="text-xs text-gray-500">x{{ item.quantity || 1 }}</p>
    </div>
    <div v-if="showPrice" class="text-right flex-shrink-0">
      <p class="text-sm font-semibold text-gray-800">{{ formatPrice(item.unitPrice ?? 0) }}</p>
      <p v-if="item.originalPrice && item.originalPrice > (item.unitPrice ?? 0)" class="text-xs text-gray-400 line-through">
        {{ formatPrice(item.originalPrice) }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { IOrderItemResponse } from "~/interfaces/order/order.interface";
import { formatPrice } from "~/utils/helpers";
import { getProductImage, formatItemAttributes } from "~/utils/order.helpers";
import { IMAGEDEFAULT } from "~/utils/constants";

const props = withDefaults(
  defineProps<{
    item: IOrderItemResponse;
    showPrice?: boolean;
  }>(),
  { showPrice: false },
);

const imageUrl = computed(() => getProductImage(props.item.productImage));
const formattedAttributes = computed(() => formatItemAttributes(props.item.productAttributes));
</script>
