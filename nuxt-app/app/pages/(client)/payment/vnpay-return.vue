<template>
  <div class="main-content relative w-full flex flex-col justify-center items-center min-h-[600px] py-12 px-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden max-w-lg w-full mx-auto">
      <!-- Header -->
      <div class="p-4 md:p-6 text-center border-b border-gray-100">
        <div class="flex justify-center mb-4">
          <div class="w-16 md:w-20 h-16 md:h-20 rounded-full flex items-center justify-center" :class="isSuccess ? 'bg-teal-100' : 'bg-red-100'">
            <!-- Check icon -->
            <svg v-if="isSuccess" class="w-8 md:w-10 h-8 md:h-10 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <!-- X icon -->
            <svg v-else class="w-8 md:w-10 h-8 md:h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold mb-1" :class="isSuccess ? 'text-teal-500' : 'text-red-500'">
          {{ isSuccess ? "Thanh toán thành công!" : "Thanh toán thất bại" }}
        </h1>
        <p class="text-gray-600 text-sm md:text-base">
          {{ isSuccess ? "Giao dịch của bạn đã được xử lý thành công" : statusMessage }}
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
            <div v-if="txnRef" class="flex justify-between">
              <span class="text-gray-600">Mã giao dịch:</span>
              <span class="font-semibold text-gray-900 text-sm">{{ txnRef }}</span>
            </div>
            <div v-if="amount" class="flex justify-between">
              <span class="text-gray-600">Số tiền:</span>
              <span class="font-bold text-red-500 text-base md:text-lg">{{ formatPrice(amount) }}</span>
            </div>
            <div v-if="bankCode" class="flex justify-between">
              <span class="text-gray-600">Ngân hàng:</span>
              <span class="text-gray-900">{{ bankCode }}</span>
            </div>
            <div v-if="transactionNo" class="flex justify-between">
              <span class="text-gray-600">Mã tra cứu VNPay:</span>
              <span class="text-gray-900">{{ transactionNo }}</span>
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

// Parse VNPay query params
const responseCode = computed(() => (route.query.vnp_ResponseCode as string) || "99");
const isSuccess = computed(() => responseCode.value === "00");
const txnRef = computed(() => (route.query.vnp_TxnRef as string) || "");
const amount = computed(() => {
  const raw = Number(route.query.vnp_Amount) || 0;
  return raw / 100; // VNPay gửi amount × 100
});
const bankCode = computed(() => (route.query.vnp_BankCode as string) || "");
const transactionNo = computed(() => (route.query.vnp_TransactionNo as string) || "");

const STATUS_MESSAGES: Record<string, string> = {
  "00": "Giao dịch thành công",
  "07": "Trừ tiền thành công. Giao dịch bị nghi ngờ",
  "09": "Thẻ/Tài khoản chưa đăng ký InternetBanking",
  "10": "Xác thực thất bại quá 3 lần",
  "11": "Hết hạn chờ thanh toán",
  "12": "Tài khoản bị khóa",
  "13": "Nhập sai mật khẩu xác thực (OTP)",
  "24": "Khách hàng hủy giao dịch",
  "51": "Tài khoản không đủ số dư",
  "65": "Vượt quá hạn mức giao dịch trong ngày",
  "75": "Ngân hàng đang bảo trì",
  "79": "Nhập sai mật khẩu thanh toán quá số lần quy định",
  "99": "Lỗi không xác định",
};

const statusMessage = computed(() => STATUS_MESSAGES[responseCode.value] || "Lỗi không xác định");
</script>
