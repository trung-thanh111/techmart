<template>
  <div class="container pb-1 sticky bg-white top-0 z-40 tabs">
    <div class="mb-4">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Tiến độ thanh toán</span>
        <span class="text-sm font-medium text-teal-600">{{ Math.round(progress) }}%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-2 rounded-full transition-all duration-500 ease-in-out" :style="{ width: progress + '%' }"></div>
      </div>
      <div class="flex justify-between mt-1">
        <span class="text-xs text-gray-500">Thông tin</span>
        <span class="text-xs text-gray-500">Địa chỉ</span>
        <span class="text-xs text-gray-500">Thanh toán</span>
      </div>
    </div>

    <div class="relative max-w-full overflow-x-auto scrollbar-hide hiddenScroll tabs-information-scroll">
      <div class="flex w-full items-center space-x-[2px]">
        <!-- Personal Info Tab -->
        <div :class="['flex-1 flex-shrink-0 p-2 md:p-3 text-center whitespace-normal duration-75 cursor-pointer tabs_item', activeTab === 'personal_info' ? 'text-white bg-gradient-to-r from-sky-500 to-sky-600' : 'text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white']" @click="$emit('change-tab', 'personal_info')">
          <div class="flex items-center justify-center gap-1">
            <span class="md:text-base text-sm">Thông tin</span>
            <svg v-if="isValid.personal_info" class="w-5 h-5 text-green-500 font-medium" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>

        <!-- Address Info Tab -->
        <div :class="['flex-1 flex-shrink-0 p-2 md:p-3 text-center whitespace-normal duration-75 cursor-pointer tabs_item', activeTab === 'address_info' ? 'text-white bg-gradient-to-r from-sky-500 to-sky-600' : 'text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white', !isValid.personal_info ? 'opacity-50 cursor-not-allowed' : '']" @click="$emit('change-tab', 'address_info')">
          <div class="flex items-center justify-center gap-1">
            <span class="md:text-base text-sm">Địa chỉ</span>
            <svg v-if="isValid.address_info" class="w-5 h-5 text-green-500 font-medium" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>

        <!-- Payment Method Tab -->
        <div :class="['flex-1 flex-shrink-0 p-2 md:p-3 text-center whitespace-normal duration-75 cursor-pointer tabs_item', activeTab === 'payment_method' ? 'text-white bg-gradient-to-r from-sky-500 to-sky-600' : 'text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white', !isValid.address_info ? 'opacity-50 cursor-not-allowed' : '']" @click="$emit('change-tab', 'payment_method')">
          <div class="flex items-center justify-center gap-1">
            <span class="md:text-base text-sm">Phương thức</span>
            <svg v-if="isValid.payment_method" class="w-5 h-5 text-green-500 font-medium" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICheckoutTab } from "~/interfaces/cart/checkout.interface";

defineProps<{
  activeTab: ICheckoutTab;
  progress: number;
  isValid: Record<ICheckoutTab, boolean>;
}>();

defineEmits<{
  (e: "change-tab", tab: ICheckoutTab): void;
}>();
</script>
