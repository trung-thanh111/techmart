<template>
  <div class="main-content relative w-full flex flex-col justify-center items-center min-h-[600px] py-12 px-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden max-w-lg w-full mx-auto">
      <!-- Header -->
      <div class="p-4 md:p-6 text-center border-b border-gray-100">
        <div class="flex justify-center mb-4">
          <div class="w-16 md:w-20 h-16 md:h-20 rounded-full flex items-center justify-center" :class="isSuccess ? 'bg-teal-100' : 'bg-red-100'">
            <svg v-if="isSuccess" class="w-8 md:w-10 h-8 md:h-10 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="w-8 md:w-10 h-8 md:h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold mb-1" :class="isSuccess ? 'text-teal-500' : 'text-red-500'">
          {{ isSuccess ? "Thanh toán thành công!" : "Thanh toán chưa hoàn tất" }}
        </h1>
        <p class="text-gray-600 text-sm md:text-base">
          {{ isSuccess ? "Giao dịch của bạn đã được ghi nhận thành công" : (message || "Có lỗi xảy ra trong quá trình thanh toán") }}
        </p>
      </div>

      <!-- Payment Details -->
      <div class="p-3 md:p-6">
        <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6">
          <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Chi tiết giao dịch
          </h3>
          <div class="space-y-3">
            <div v-if="orderId" class="flex justify-between">
              <span class="text-gray-600">Mã đơn / Request:</span>
              <span class="font-semibold text-gray-900 text-sm">{{ orderId }}</span>
            </div>
            <div v-if="amount" class="flex justify-between">
              <span class="text-gray-600">Số tiền:</span>
              <span class="font-bold text-red-500 text-base md:text-lg">{{ formatPrice(amount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Trạng thái:</span>
              <span class="font-medium" :class="isSuccess ? 'text-teal-600' : 'text-red-600'">
                {{ isSuccess ? "Thành công" : "Thất bại" }}
              </span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
          <a href="/" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-md transition-colors">Tiếp tục mua sắm</a>
          <NuxtLink :to="PROFILEORDER" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-md transition-colors">Đơn hàng của tôi</NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { formatPrice } from "~/utils/helpers";

definePageMeta({
  layout: "client",
});

const route = useRoute();
const resultCode = computed(() => (route.query.resultCode as string) || (route.query.errorCode as string) || "99");
const isSuccess = computed(() => resultCode.value === "0" || resultCode.value === "00");
const orderId = computed(() => (route.query.orderId as string) || "");
const amount = computed(() => Number(route.query.amount) || 0);
const message = computed(() => (route.query.message as string) || "");
</script>
