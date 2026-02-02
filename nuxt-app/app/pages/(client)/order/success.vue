<template>
  <div class="main-content fixed inset-0 z-50 flex flex-col justify-center items-center bg-white/80 px-2 overflow-hidden">
    <img src="/images/bg-auth.png" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover -z-10" />
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden max-w-2xl w-full mx-auto z-10">
      <div class="bg-white p-4 md:p-6 text-center border-b border-gray-100">
        <div class="flex justify-center mb-4">
          <div class="w-16 md:w-20 h-16 md:h-20 bg-teal-100 rounded-full flex items-center justify-center">
            <svg class="w-8 md:w-10 h-8 md:h-10 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-teal-500 mb-1">Đặt hàng thành công!</h1>
        <p class="text-gray-600 text-sm md:text-base">Cảm ơn bạn đã mua sắm tại Tech Mart</p>
      </div>
      <div class="p-4 md:p-8">
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
              <span class="font-semibold text-gray-900">#TM202412001</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Ngày đặt:</span>
              <span class="text-gray-900">25/12/2024</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Tổng tiền:</span>
              <span class="font-bold text-red-500 text-base md:text-lg">525.000đ</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Trạng thái:</span>
              <span class="text-teal-600 font-medium">Đặt hàng thành công</span>
            </div>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 md:gap-4 mb-6 md:mb-8">
          <a href="/" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-md transition-colors">Tiếp tục mua sắm</a>
          <a href="/order-detail" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-md transition-colors">Chi tiết đơn hàng</a>
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
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "client",
});

useHead({
  bodyAttrs: {
    class: "overflow-hidden",
  },
});
</script>
