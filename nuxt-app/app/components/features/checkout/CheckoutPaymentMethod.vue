<template>
  <div class="text-gray-600 text-base">
    <div class="max-w-full w-full mx-auto p-4">
      <div>
        <h5 class="mb-1 font-medium text-lg">Phương thức thanh toán</h5>
        <p class="text-sm mb-6 text-gray-500">Vui lòng chọn và nhập thông tin thanh toán của bạn.</p>
      </div>
      <div class="mt-6 max-w-4xl mx-auto">
        <!-- Payment Method Tabs -->
        <div class="mb-6">
          <div class="flex w-full items-center space-x-[2px] bg-gray-100 p-1 rounded-lg">
            <div v-for="method in paymentMethods" :key="method.id" @click="activePaymentMethod = method.id" :class="['flex-1 flex-shrink-0 p-3 font-semibold text-center whitespace-normal duration-75 cursor-pointer rounded-md', activePaymentMethod === method.id ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-600 hover:text-teal-600']">
              <div class="flex items-center justify-center gap-2">
                <img :src="method.icon" :alt="method.name" class="w-6 h-6 object-cover" />
                <span class="text-sm font-medium">{{ method.name }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
          <div v-if="activePaymentMethod === 'vnpay'" class="space-y-4">
            <div class="flex items-center gap-3 mb-4">
              <img src="/images/logo/vnpay.webp" alt="VNPay" class="w-8 h-8" />
              <h6 class="text-lg font-semibold">Thanh toán qua VNPay</h6>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <p class="text-sm text-blue-800 mb-2">Bạn sẽ được chuyển đến trang thanh toán VNPay để hoàn tất giao dịch.</p>
              <ul class="text-xs text-blue-700 space-y-1">
                <li>• Hỗ trợ thẻ ATM, thẻ tín dụng/ghi nợ</li>
                <li>• Thanh toán an toàn với chuẩn 3D Secure</li>
                <li>• Xử lý giao dịch nhanh chóng</li>
              </ul>
            </div>
          </div>
          <div v-if="activePaymentMethod === 'momo'" class="space-y-4">
            <div class="flex items-center gap-3 mb-4">
              <img src="/images/logo/momo.svg" alt="MoMo" class="w-8 h-8" />
              <h6 class="text-lg font-semibold">Thanh toán qua MoMo</h6>
            </div>
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
              <p class="text-sm text-pink-800 mb-2">Quét mã QR hoặc chuyển khoản qua ứng dụng MoMo.</p>
              <ul class="text-xs text-pink-700 space-y-1">
                <li>• Thanh toán nhanh chóng qua ví MoMo</li>
                <li>• Không cần thẻ ngân hàng</li>
                <li>• Bảo mật thông tin tuyệt đối</li>
              </ul>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">
              <div class="text-center">
                <div class="bg-gray-100 w-32 h-32 mx-auto mb-2 flex items-center justify-center rounded-lg">
                  <span class="text-gray-500 text-xs">QR Code</span>
                </div>
                <p class="text-xs text-gray-600">Quét mã QR</p>
              </div>
              <div class="text-center">
                <div class="bg-gray-100 w-32 h-32 mx-auto mb-2 flex items-center justify-center rounded-lg">
                  <span class="text-gray-500 text-xs">Chuyển khoản</span>
                </div>
                <p class="text-xs text-gray-600">Chuyển khoản trực tiếp</p>
              </div>
            </div>
          </div>
          <div v-if="activePaymentMethod === 'cod'" class="space-y-4">
            <div class="flex items-center gap-3 mb-4">
              <img src="/images/cash-on-delivery.webp" alt="COD" class="w-8 h-8" />
              <h6 class="text-lg font-semibold">Thanh toán khi nhận hàng</h6>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <p class="text-sm text-green-800 mb-2">Thanh toán bằng tiền mặt khi nhận hàng.</p>
              <ul class="text-xs text-green-700 space-y-1">
                <li>• Kiểm tra hàng trước khi thanh toán</li>
                <li>• Thanh toán bằng tiền mặt hoặc thẻ</li>
                <li>• Phí thu hộ: 0đ</li>
              </ul>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <h6 class="font-medium text-gray-800 mb-2">Lưu ý:</h6>
              <ul class="text-xs text-gray-600 space-y-1">
                <li>• Vui lòng chuẩn bị đủ tiền khi nhận hàng</li>
                <li>• Có thể thanh toán bằng thẻ tại nhà</li>
                <li>• Thời gian giao hàng có thể lâu hơn</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="mt-2 italic text-gray-500 flex items-center text-sm gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
          Giao dịch của bạn được bảo mật bằng SSL
        </div>
      </div>
      <BackButton @click="$emit('back')" />
      <NextButton @click="$emit('next')" :disabled="!canProceed">Hoàn tất</NextButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { IPaymentInfo, IPaymentMethod } from "~/interfaces/cart/checkout.interface";
import NextButton from "~/components/features/checkout/NextButton.vue";
import BackButton from "~/components/features/checkout/BackButton.vue";

const props = defineProps<{
  modelValue: IPaymentInfo;
  paymentMethods: IPaymentMethod[];
  canProceed: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: IPaymentInfo): void;
  (e: "next"): void;
  (e: "back"): void;
}>();

const activePaymentMethod = computed({
  get: () => props.modelValue.selectedMethod,
  set: (val) => emit("update:modelValue", { ...props.modelValue, selectedMethod: val }),
});
</script>
