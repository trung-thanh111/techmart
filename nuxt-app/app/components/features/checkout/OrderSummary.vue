<template>
  <div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden sticky top-4">
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-3">
      <div class="flex items-center justify-between">
        <h2 class="text-white text-sm md:text-base font-bold">{{ $t("cart.order") }}</h2>
        <div class="flex items-center gap-2">
          <svg class="md:w-5 md:h-5 w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <span class="text-white/80 md:text-sm text-xs">{{ $t("cart.itemsInCart", { count: selectedCount }) }}</span>
        </div>
      </div>
    </div>

    <div class="p-2">
      <div class="space-y-3">
        <div v-if="selectedItems.length === 0" class="flex flex-col items-center justify-center py-8 text-gray-400">
          <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <p class="text-sm text-center">{{ $t("cart.selectItemsToCheckout") }}</p>
        </div>

        <div v-for="item in selectedItems" :key="item.id" class="flex items-start gap-4 bg-gray-50 border border-gray-100 hover:border-sky-500 rounded-sm">
          <div class="relative">
            <NuxtImg :src="getProductImage(item)" :alt="getProductName(item)" class="w-16 h-16 object-cover rounded-lg" loading="lazy" />
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="md:text-sm text-xs font-medium text-gray-800 line-clamp-2 mb-1">
              {{ getProductName(item) }}
            </h3>
            <p class="text-xs text-gray-500 mb-1">{{ getVariantInfo(item) }}</p>
            <p class="md:text-sm text-xs font-medium text-gray-900">
              {{ formatPrice((item.price || 0) * (item.quantity || 0)) }}
              <span class="text-red-500 ml-2">x{{ item.quantity }}</span>
            </p>
          </div>
          <button @click="$emit('unselect-item', item.id!)" type="button" class="text-gray-400 hover:text-red-500 transition-colors md:p-1 p-0.5">
            <svg class="md:w-4 md:h-4 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a2 2 0 012 2v0H7v0a2 2 0 012-2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- coupon -->
      <div class="mt-5 p-3 bg-gradient-to-r from-amber-50 to-orange-50 border border-dashed border-amber-300 rounded-sm">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <h3 class="text-sm font-semibold text-amber-800">
              {{ $t("cart.coupon") }}
            </h3>
          </div>
          <span v-if="appliedCoupon" class="text-xs text-green-700 bg-green-100 font-medium px-2 py-0.5 rounded-full">
            Đã áp dụng
          </span>
        </div>

        <!-- Khi đã áp mã -->
        <div v-if="appliedCoupon" class="flex items-center justify-between bg-white border border-green-200 rounded p-2 text-xs">
          <div class="min-w-0 flex-1">
            <p class="font-bold text-green-700 uppercase truncate">{{ appliedCoupon.code }}</p>
            <p class="text-gray-500 truncate text-[11px]">{{ appliedCoupon.name || 'Mã giảm giá' }}</p>
            <p class="text-green-600 font-semibold mt-0.5">- {{ formatPrice(discountAmount || 0) }}</p>
          </div>
          <button
            @click="$emit('remove-coupon')"
            type="button"
            class="text-gray-400 hover:text-red-500 p-1 transition-colors"
            title="Hủy áp dụng"
          >
            <i class="pi pi-times-circle text-base"></i>
          </button>
        </div>

        <!-- Khi chưa áp mã -->
        <div v-else class="flex gap-2">
          <InputText
            :model-value="couponCode"
            @update:modelValue="$emit('update:couponCode', $event || '')"
            @keyup.enter="$emit('apply-coupon')"
            :placeholder="$t('cart.couponPlaceholder')"
            :disabled="selectedCount === 0 || isApplyingCoupon"
            class="flex-1 text-sm md:px-3 px-2 md:py-2 py-1.5 !border-amber-300 focus:!ring-1 focus:!ring-amber-500 focus:!border-transparent !rounded-none !shadow-none"
          />
          <Button
            :label="isApplyingCoupon ? '' : $t('cart.applyCoupon')"
            :icon="isApplyingCoupon ? 'pi pi-spinner pi-spin' : undefined"
            @click="$emit('apply-coupon')"
            :disabled="selectedCount === 0 || isApplyingCoupon || !couponCode"
            class="border-0 md:px-4 px-3 md:py-2 py-1.5 !bg-amber-600 text-white md:text-sm text-xs font-medium !rounded-none transition-colors hover:!brightness-110 focus:!ring-1 focus:!ring-amber-500 focus:!outline-none"
          />
        </div>
      </div>

      <!-- order total -->
      <div class="mt-5 space-y-3">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">{{ $t("cart.subtotal") }}:</span>
          <span class="">{{ formatPrice(selectedTotalPrice) }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">{{ $t("cart.discount") }}:</span>
          <span :class="(discountAmount || 0) > 0 ? 'text-green-600 font-semibold' : 'text-gray-900'">
            {{ (discountAmount || 0) > 0 ? `- ${formatPrice(discountAmount)}` : formatPrice(0) }}
          </span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">{{ $t("cart.shipping") }}:</span>
          <span class="">{{ formatPrice(shippingFee) }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">{{ $t("cart.taxPercent", { percent: TAX_RATE * 100 }) }}:</span>
          <span class="">{{ formatPrice(taxAmount) }}</span>
        </div>
        <div class="border-t border-gray-200 pt-3 mt-4">
          <div class="flex items-center justify-between">
            <span class="md:text-lg text-base font-bold text-gray-900">{{ $t("cart.grandTotal") }}:</span>
            <span class="md:text-xl text-lg font-bold text-red-600">
              {{ formatPrice(selectedCount > 0 ? Math.max(0, selectedTotalPrice - (discountAmount || 0) + shippingFee + taxAmount) : 0) }}
            </span>
          </div>
          <p class="text-xs text-gray-500 mt-1">{{ $t("cart.taxIncluded") }}</p>
        </div>
      </div>

      <div class="mt-6">
        <NuxtLink v-if="to" :to="to" class="flex items-center justify-center w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white py-2 px-4 rounded-sm font-semibold text-base shadow uppercase transition-all duration-200" :class="{ 'opacity-60 cursor-not-allowed pointer-events-none': selectedCount === 0 || loading }" @click="$emit('checkout')">
          <i v-if="loading" class="pi pi-spinner pi-spin mr-2" />
          {{ buttonLabel || $t("cart.proceedToCheckout") }}
        </NuxtLink>

        <button v-else type="button" class="flex items-center justify-center w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white py-2 px-4 rounded-sm font-semibold text-base shadow uppercase transition-all duration-200" :class="{ 'opacity-60 cursor-not-allowed': selectedCount === 0 || loading }" :disabled="selectedCount === 0 || loading" @click="$emit('checkout')">
          <i v-if="loading" class="pi pi-spinner pi-spin mr-2" />
          {{ buttonLabel || $t("cart.proceedToCheckout") }}
        </button>

        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-green-600 italic">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <span>{{ $t("cart.securePayment") }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { IMAGEDEFAULT } from "~/utils/constants";
import type { ICartItem } from "~/interfaces/cart/cartItem.interface";
import { SHIPPING_METHODS, SHIPPING_FEES } from "~/utils/order.constants";

interface AppliedCoupon {
  id?: number;
  code: string;
  name?: string;
  type?: string;
}

const props = withDefaults(
  defineProps<{
    selectedItems: ICartItem[];
    selectedCount: number;
    selectedTotalPrice: number;
    shippingMethod?: string;
    couponCode: string;
    discountAmount?: number;
    appliedCoupon?: AppliedCoupon | null;
    isApplyingCoupon?: boolean;
    loading?: boolean;
    buttonLabel?: string;
    to?: string;
  }>(),
  {
    discountAmount: 0,
    appliedCoupon: null,
    isApplyingCoupon: false,
    loading: false,
  }
);

defineEmits<{
  (e: "update:couponCode", value: string): void;
  (e: "apply-coupon"): void;
  (e: "remove-coupon"): void;
  (e: "checkout"): void;
  (e: "unselect-item", id: number): void;
}>();

// Helpers moved inside or kept if simple
const getProductImage = (item: ICartItem): string => {
  if (item.variant && "products" in item.variant && (item.variant as any).products?.album) {
    const images = resolveAlbum((item.variant as any).products.album);
    return images[0] || IMAGEDEFAULT;
  }
  if (item.product?.album) {
    const images = resolveAlbum(item.product.album);
    return images[0] || IMAGEDEFAULT;
  }
  return IMAGEDEFAULT;
};

const getProductName = (item: ICartItem): string => {
  const productName = item.product?.name || "";
  const variantName = item.variant?.name || "";
  if (variantName) {
    return `${productName} - ${variantName}`;
  }
  return productName;
};

const getVariantInfo = (item: ICartItem): string => {
  if (item.variant?.attributeData) {
    return Object.values(item.variant.attributeData)
      .map((attr: any) => attr.name)
      .join(", ");
  }
  return "";
};

const shippingFee = computed(() => {
  if (props.selectedCount === 0) return 0;
  if (props.shippingMethod === SHIPPING_METHODS.EXPRESS) {
    return SHIPPING_FEES[SHIPPING_METHODS.EXPRESS];
  }
  return SHIPPING_FEES[SHIPPING_METHODS.STANDARD];
});

const taxAmount = computed(() => {
  return props.selectedCount > 0 ? Math.round(props.selectedTotalPrice * TAX_RATE) : 0;
});
</script>
