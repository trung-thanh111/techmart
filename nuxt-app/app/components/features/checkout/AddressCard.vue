<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div v-for="(address, index) in addresses" :key="address.id || index" class="flex flex-col justify-between border border-gray-200 rounded-md hover:border-sky-500 transition-colors" :class="{ 'border-sky-500 ring-1 ring-sky-500': modelValue === address.id }">
      <label class="flex flex-col p-4 cursor-pointer flex-1">
        <div class="flex items-start justify-between h-full">
          <div class="w-full">
            <span class="block text-sm uppercase text-gray-500 font-semibold mb-2">{{ address.label || "Nhà Riêng" }}</span>
            <span class="block text-sm font-semibold text-gray-800 mb-1">{{ address.customerName }}</span>
            <span class="block text-sm text-gray-600 mb-1 line-clamp-2" :title="address.shippingAddress || formatAddress(address)">
              {{ address.shippingAddress || formatAddress(address) }}
            </span>
            <span class="block text-sm text-gray-600">{{ address.customerPhone }}</span>
          </div>
          <RadioButton v-model="selected" :inputId="String(address.id)" :value="address.id" />
        </div>
      </label>
      <div class="flex justify-end items-center gap-4 px-4 py-2 bg-gray-50 border-t border-gray-200 rounded-b-md text-xs">
        <button type="button" class="flex items-center gap-1 text-gray-600 hover:text-sky-600 transition-colors" @click.stop="$emit('update', address.id)">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
          </svg>
          Sửa
        </button>
        <button type="button" class="flex items-center gap-1 text-gray-600 hover:text-red-600 transition-colors" @click.stop="$emit('delete', address.id)">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          Xóa
        </button>
      </div>
    </div>
    <div class="border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center min-h-[120px] cursor-pointer hover:bg-sky-50 hover:border-sky-500 transition-colors" @click="$emit('create')">
      <button type="button" class="flex items-center gap-1 text-sky-500 text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Thêm mới
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { IAddress } from "~/interfaces/cart/checkout.interface";

const props = defineProps<{
  modelValue: string | number | null;
  addresses: IAddress[];
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string | number): void;
  (e: "create"): void;
  (e: "update", id: string | number): void;
  (e: "delete", id: string | number): void;
}>();

const selected = computed({
  get: () => props.modelValue,
  set: (val) => {
    if (val !== null) emit("update:modelValue", val);
  },
});

const formatAddress = (addr: IAddress) => {
  const parts = [addr.shippingStreet, addr.shippingWard, addr.shippingCity].filter(Boolean);
  return parts.join(", ");
};
</script>
