<template>
  <div class="w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-100 gap-3">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $t("sidebar.myVouchers") || "Khuyến mãi của tôi" }}</h1>
        <p class="text-xs md:text-sm text-gray-500 mt-1">Quản lý và sử dụng các mã giảm giá dành riêng cho bạn tại TechMart</p>
      </div>
      <NuxtLink
        to="/voucher"
        class="inline-flex items-center gap-1.5 text-xs md:text-sm font-medium text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 px-3 py-1.5 rounded-lg transition-colors self-start sm:self-auto"
      >
        <span>Săn thêm voucher</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </NuxtLink>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto py-4 scrollbar-none border-b border-gray-100">
      <button
        v-for="tab in filterTabs"
        :key="tab.id"
        type="button"
        @click="activeFilter = tab.id"
        :class="[
          'px-3.5 py-1.5 rounded-full text-xs md:text-sm font-medium whitespace-nowrap transition-all duration-200',
          activeFilter === tab.id
            ? 'bg-sky-600 text-white shadow-sm'
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
      <div v-for="i in 4" :key="i" class="animate-pulse bg-gray-50 rounded-xl border border-gray-200 p-4 flex gap-4">
        <div class="w-24 h-24 bg-gray-200 rounded-lg shrink-0"></div>
        <div class="flex-1 space-y-2 py-1">
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
          <div class="h-8 bg-gray-200 rounded w-1/3 mt-2"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <div class="w-12 h-12 text-red-500 mx-auto mb-3">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <p class="text-sm text-gray-600 mb-3">{{ error }}</p>
      <button @click="fetchPromotions" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-xs font-medium hover:bg-sky-700">Thử lại</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredPromotions.length === 0" class="text-center py-16 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 mt-6">
      <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
      </div>
      <h3 class="text-base font-semibold text-gray-800 mb-1">Chưa có voucher nào</h3>
      <p class="text-xs md:text-sm text-gray-500 max-w-sm mx-auto mb-5">Hiện không có mã giảm giá nào trong danh mục này. Hãy khám phá thêm ưu đãi nhé!</p>
      <NuxtLink to="/shop" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs md:text-sm font-medium rounded-lg transition-colors shadow-sm">
        Mua sắm ngay
      </NuxtLink>
    </div>

    <!-- Voucher Cards Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
      <div
        v-for="promo in filteredPromotions"
        :key="promo.id"
        class="group relative bg-white rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.06)] hover:shadow-lg border border-transparent hover:border-dashed hover:border-sky-400 transition-all duration-300 flex"
      >
        <!-- Top Cutout Notch (50/50) with curved dashed border on hover -->
        <div class="absolute -top-[1px] left-28 sm:left-32 -translate-x-1/2 w-5 h-2.5 z-10 pointer-events-none overflow-visible">
          <svg class="w-5 h-2.5 overflow-visible" viewBox="0 0 20 10" fill="none">
            <path d="M -0.5 -1.5 L 20.5 -1.5 L 20.5 0 C 20.5 5.52 15.82 10 10 10 C 4.18 10 -0.5 5.52 -0.5 0 Z" fill="#ffffff" />
            <path d="M 0 0 C 0 5.52 4.48 10 10 10 C 15.52 10 20 5.52 20 0" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="3,3" class="text-transparent group-hover:text-sky-400 transition-colors duration-300" />
          </svg>
        </div>

        <!-- Bottom Cutout Notch (50/50) with curved dashed border on hover -->
        <div class="absolute -bottom-[1px] left-28 sm:left-32 -translate-x-1/2 w-5 h-2.5 z-10 pointer-events-none overflow-visible">
          <svg class="w-5 h-2.5 overflow-visible" viewBox="0 0 20 10" fill="none">
            <path d="M -0.5 11.5 L 20.5 11.5 L 20.5 10 C 20.5 4.48 15.82 0 10 0 C 4.18 0 -0.5 4.48 -0.5 10 Z" fill="#ffffff" />
            <path d="M 0 10 C 0 4.48 4.48 0 10 0 C 15.52 0 20 4.48 20 10" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="3,3" class="text-transparent group-hover:text-sky-400 transition-colors duration-300" />
          </svg>
        </div>

        <!-- Left Banner Ticket Styling -->
        <div class="w-28 sm:w-32 bg-gradient-to-br from-sky-500 to-sky-700 text-white p-2.5 sm:p-3 flex flex-col justify-center items-center text-center gap-1.5 sm:gap-2 relative shrink-0 rounded-l-xl">
          <div class="flex flex-col items-center">
            <img src="/images/icons/voucher-tag.png" alt="Voucher" class="w-6 h-6 sm:w-7 sm:h-7 object-contain mb-0.5" />
            <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-sky-100">TechMart</div>
          </div>

          <div>
            <span class="text-[10px] sm:text-[11px] font-black uppercase bg-white text-sky-700 px-2 py-0.5 rounded shadow-xs whitespace-nowrap">
              {{ getDiscountBadge(promo) }}
            </span>
          </div>
        </div>

        <!-- Right Content Details -->
        <div class="flex-1 p-2.5 sm:p-3 flex flex-col justify-between rounded-r-xl min-w-0">
          <div>
            <div class="flex items-start justify-between gap-2 mb-1">
              <h2 class="text-xs sm:text-sm font-bold text-gray-900 group-hover:text-sky-600 transition-colors line-clamp-1">
                {{ promo.name }}
              </h2>
            </div>

            <!-- Rules & Conditions -->
            <p class="text-[11px] sm:text-xs text-gray-600 flex items-center gap-1 mb-0.5">
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="truncate">{{ getConditionText(promo) }}</span>
            </p>

            <p v-if="getMaxDiscountText(promo)" class="text-[11px] sm:text-xs text-amber-600 flex items-center gap-1 mb-0.5">
              <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="truncate">{{ getMaxDiscountText(promo) }}</span>
            </p>

            <!-- Expiry Date -->
            <p class="text-[10px] sm:text-[11px] text-gray-500 flex items-center gap-1">
              <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ getExpiryText(promo) }}</span>
            </p>
          </div>

          <!-- Actions Footer -->
          <div class="mt-2 pt-2 border-t border-dashed border-gray-100 flex items-center justify-between gap-2">
            <!-- Voucher Code Pill -->
            <div
              @click="copyCode(promo.code)"
              class="cursor-pointer group/code inline-flex items-center gap-1 px-2 py-0.5 bg-gray-50 hover:bg-sky-50 border border-dashed border-gray-300 hover:border-sky-300 rounded text-[11px] font-mono font-bold text-gray-700 hover:text-sky-700 transition-colors"
              title="Nhấn để sao chép"
            >
              <span>{{ promo.code }}</span>
              <svg class="w-3 h-3 text-gray-400 group-hover/code:text-sky-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </div>

            <!-- Use Voucher Button -->
            <button
              type="button"
              @click="useVoucher(promo.code)"
              class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-xs transition-colors"
            >
              Dùng ngay
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { usePromotion } from "~/composables/promotion/usePromotion";

const {
  promotions,
  loading,
  error,
  fetchPromotions,
  copyCode,
  useVoucher,
  getDiscountBadge,
  getConditionText,
  getMaxDiscountText,
  getExpiryText,
} = usePromotion();

const activeFilter = ref("all");

const filterTabs = [
  { id: "all", label: "Tất cả" },
  { id: "fixed", label: "Giảm tiền mặt" },
  { id: "percent", label: "Giảm theo %" },
  { id: "freeship", label: "Miễn phí vận chuyển" },
];

const filteredPromotions = computed(() => {
  if (activeFilter.value === "all") return promotions.value;

  if (activeFilter.value === "freeship") {
    return promotions.value.filter(
      (p) =>
        p.code.toLowerCase().includes("ship") ||
        p.name.toLowerCase().includes("ship") ||
        p.type === "free_ship"
    );
  }

  if (activeFilter.value === "percent") {
    return promotions.value.filter((p) =>
      p.actions?.some((a) => a.actionType === "percent")
    );
  }

  if (activeFilter.value === "fixed") {
    return promotions.value.filter((p) =>
      p.actions?.some((a) => a.actionType === "fixed" || a.actionType === "fixed_amount")
    );
  }

  return promotions.value;
});

onMounted(() => {
  fetchPromotions();
});
</script>

<style scoped></style>
