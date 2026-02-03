<template>
  <div class="main-content relative w-full flex flex-col justify-center items-center min-h-[600px] py-12 px-4">
    <DataState :loading="loading" :data="order" class="max-w-2xl w-full mx-auto z-10">
      <template #empty>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden max-w-md w-full mx-auto p-8 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <div class="text-gray-400 font-bold text-xl">?</div>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Không tìm thấy thông tin</h3>
          <p class="text-gray-600 mb-6">Xin lỗi, chúng tôi không thể tìm thấy thông tin giao dịch.</p>
          <a href="/" class="inline-block px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-md transition-colors">Về trang chủ</a>
        </div>
      </template>

      <div v-if="order" class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-white p-4 md:p-6 text-center border-b border-gray-100">
          <div class="flex justify-center mb-4">
            <div class="w-16 md:w-20 h-16 md:h-20 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-8 md:w-10 h-8 md:h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </div>
          </div>
          <h1 class="text-2xl md:text-3xl font-bold text-red-500 mb-1">Thanh toán thất bại!</h1>
          <p class="text-gray-600 text-sm md:text-base">Có lỗi xảy ra trong quá trình thanh toán</p>
        </div>
        <div class="p-3 md:p-6">
          <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6 md:mb-8">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Thông tin đơn hàng
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Mã đơn hàng:</span>
                <span class="font-semibold text-gray-900">#{{ order.code }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Ngày đặt:</span>
                <span class="text-gray-900">{{ formatDate(new Date(order.createdAt as any)) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Tổng tiền:</span>
                <span class="font-bold text-red-500 text-base md:text-lg">{{ formatPrice(order.totalAmount as any) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Trạng thái:</span>
                <span class="text-red-600 font-medium uppercase">{{ ORDER_STATUS_LABELS[order.status as any] || order.status }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Phương thức thanh toán:</span>
                <span class="text-gray-900">{{ PAYMENT_METHOD_LABELS[order.paymentMethod as any] || order.paymentMethod }}</span>
              </div>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 md:gap-4 mb-6 md:mb-8">
            <a href="/" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-md transition-colors">Về trang chủ</a>
            <NuxtLink :to="`/cart/checkout`" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-md transition-colors">Thanh toán lại</NuxtLink>
          </div>
          <div class="text-center">
            <p class="text-xs md:text-sm text-gray-600 mb-2">Cần hỗ trợ? Liên hệ chúng tôi:</p>
            <div class="flex items-center justify-center gap-4 text-xs md:text-sm">
              <a href="tel:1900-xxxx" class="flex items-center gap-1 text-sky-600 hover:text-sky-700 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                1900-xxxx
              </a>
              <a href="mailto:support@techmart.com" class="flex items-center gap-1 text-sky-600 hover:text-sky-700 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                support@techmart.com
              </a>
            </div>
          </div>
        </div>
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
import { useOrderDetail } from "~/composables/order/useOrderDetail";
import { formatPrice, formatDate } from "~/utils/helpers";
import { ORDER_STATUS_LABELS, PAYMENT_METHOD_LABELS } from "~/utils/order.constants";
import DataState from "~/components/ui/DataState.vue";
import { TOORDERSUCCESS } from "~/utils/constants";

definePageMeta({
  layout: "client",
});

const { order, loading } = useOrderDetail();

watch(order, (newOrder) => {
  if (newOrder) {
    const isFailed = newOrder.status === "failed" || newOrder.status === "cancelled" || newOrder.paymentStatus === "failed";
    if (!isFailed) {
      return navigateTo(`${TOORDERSUCCESS}?code=${newOrder.code}`);
    }
  }
});
</script>
