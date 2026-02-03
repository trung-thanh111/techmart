<template>
  <div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <span class="text-sm font-medium text-gray-700">{{ dateLabel }}</span>
    </div>
    <div class="space-y-4">
      <OrderCard v-for="order in orders" :key="order.id || order.code" :order="order" @cancel="$emit('cancel', $event)" @review="$emit('review', $event)" @reorder="$emit('reorder', $event)" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { IOrder } from "~/interfaces/order/order.interface";
import OrderCard from "./OrderCard.vue";

defineProps<{
  dateLabel: string;
  orders: IOrder[];
}>();

defineEmits<{
  (e: "cancel", order: IOrder): void;
  (e: "review", order: IOrder): void;
  (e: "reorder", order: IOrder): void;
}>();
</script>
