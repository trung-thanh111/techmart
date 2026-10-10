<template>
  <div class="main-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <Breadcrumb :crumbs="breadcrumbs" />

    <!-- Hero Banner -->
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-600 via-sky-700 to-indigo-800 text-white p-6 sm:p-10 mb-8 shadow-lg">
      <div class="relative z-10 max-w-2xl">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-sky-100 backdrop-blur-sm mb-3">
          <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Trung Tâm Ưu Đãi TechMart
        </span>
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight mb-2 sm:mb-3">
          Săn Mã Giảm Giá & Voucher Cực Hot
        </h1>
        <p class="text-xs sm:text-base text-sky-100 max-w-xl mb-6">
          Hàng ngàn mã giảm giá, voucher freeship và ưu đãi độc quyền dành riêng cho khách hàng TechMart. Thu thập ngay!
        </p>

        <!-- Search Bar -->
        <div class="flex items-center bg-white rounded-xl shadow-md p-1 max-w-md">
          <div class="pl-3 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo tên voucher hoặc mã..."
            class="w-full px-3 py-2 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            type="button"
            class="px-2 text-gray-400 hover:text-gray-600"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Background Decorative Elements -->
      <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
      <div class="absolute right-12 top-6 opacity-20 hidden md:block pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M2 10a4 4 0 014-4h12a4 4 0 014 4v1a3 3 0 000 6v1a4 4 0 01-4 4H6a4 4 0 01-4-4v-1a3 3 0 000-6v-1zm14 1a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1zm-4 0a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1zm-4 0a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1z" />
        </svg>
      </div>
    </section>

    <!-- Filter Tabs -->
    <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-3 mb-6">
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-none py-1">
        <button
          v-for="tab in filterTabs"
          :key="tab.id"
          type="button"
          @click="activeFilter = tab.id"
          :class="[
            'px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200',
            activeFilter === tab.id
              ? 'bg-sky-600 text-white shadow-sm'
              : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'
          ]"
        >
          {{ tab.label }}
        </button>
      </div>
      <div class="text-xs text-gray-500 hidden sm:block shrink-0">
        Hiển thị <span class="font-bold text-gray-900">{{ filteredPromotions.length }}</span> voucher
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="i in 6" :key="i" class="animate-pulse bg-white rounded-2xl border border-gray-200 p-4 flex gap-4">
        <div class="w-24 h-28 bg-gray-200 rounded-xl shrink-0"></div>
        <div class="flex-1 space-y-2 py-1">
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
          <div class="h-8 bg-gray-200 rounded w-1/2 mt-4"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-16 bg-white rounded-2xl border border-gray-200 shadow-sm">
      <div class="w-12 h-12 text-red-500 mx-auto mb-3">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <p class="text-sm text-gray-600 mb-4">{{ error }}</p>
      <button @click="fetchPromotions" class="px-5 py-2 bg-sky-600 text-white rounded-xl text-xs font-semibold hover:bg-sky-700">
        Thử lại
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredPromotions.length === 0" class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
      <div class="w-16 h-16 bg-sky-50 text-sky-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
      </div>
      <h3 class="text-base font-bold text-gray-900 mb-1">Không tìm thấy voucher phù hợp</h3>
      <p class="text-xs sm:text-sm text-gray-500 max-w-sm mx-auto mb-6">
        Hãy thử tìm với từ khóa khác hoặc quay lại xem các chương trình khuyến mãi khác.
      </p>
      <NuxtLink to="/shop" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition-colors">
        Xem tất cả sản phẩm
      </NuxtLink>
    </div>

    <!-- Voucher Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="promo in filteredPromotions"
        :key="promo.id"
        class="group relative bg-white rounded-2xl border border-gray-200 hover:border-sky-400 hover:shadow-lg transition-all duration-300 flex overflow-hidden flex-col justify-between"
      >
        <!-- Top Card Content -->
        <div class="flex">
          <!-- Left Ticket Notch Column -->
          <div
            :class="[
              'w-28 sm:w-32 p-3 text-white flex flex-col justify-between items-center text-center relative shrink-0',
              promo.type === 'free_ship' || promo.code.includes('SHIP')
                ? 'bg-gradient-to-br from-emerald-500 to-teal-700'
                : 'bg-gradient-to-br from-sky-600 to-indigo-700'
            ]"
          >
            <!-- Cutout Notch -->
            <div class="absolute -top-2.5 -right-2.5 w-5 h-5 bg-white rounded-full"></div>
            <div class="absolute -bottom-2.5 -right-2.5 w-5 h-5 bg-white rounded-full"></div>

            <div class="pt-1">
              <span class="inline-block p-2 bg-white/20 rounded-full mb-1">
                <svg v-if="promo.type === 'free_ship' || promo.code.includes('SHIP')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </span>
              <div class="text-[10px] uppercase font-bold tracking-wider opacity-90">
                {{ promo.type === 'free_ship' || promo.code.includes('SHIP') ? 'FREESHIP' : 'DISCOUNT' }}
              </div>
            </div>

            <div class="my-auto py-2">
              <div class="text-xs font-black uppercase bg-white text-gray-900 px-2 py-0.5 rounded shadow-sm whitespace-nowrap">
                {{ getDiscountBadge(promo) }}
              </div>
            </div>

            <div class="text-[10px] text-white/80 pb-1">
              {{ promo.remainingUsage ? `Còn ${promo.remainingUsage} lượt` : 'Số lượng có hạn' }}
            </div>
          </div>

          <!-- Right Content Details -->
          <div class="flex-1 p-4 flex flex-col justify-between">
            <div>
              <div class="flex items-start justify-between gap-1 mb-1.5">
                <h3 class="text-sm font-bold text-gray-900 group-hover:text-sky-600 transition-colors line-clamp-1">
                  {{ promo.name }}
                </h3>
              </div>

              <!-- Conditions -->
              <p class="text-xs text-gray-600 flex items-center gap-1.5 mb-1">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ getConditionText(promo) }}</span>
              </p>

              <p v-if="getMaxDiscountText(promo)" class="text-xs text-amber-600 flex items-center gap-1.5 mb-1">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ getMaxDiscountText(promo) }}</span>
              </p>

              <!-- Expiry -->
              <p class="text-[11px] text-gray-500 flex items-center gap-1.5 mt-2">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ getExpiryText(promo) }}</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Bottom Actions Row -->
        <div class="px-4 py-3 bg-gray-50/70 border-t border-dashed border-gray-200 flex items-center justify-between gap-2">
          <!-- Code Badge Click to Copy -->
          <div
            @click="copyCode(promo.code)"
            class="cursor-pointer group/code inline-flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-sky-50 border border-dashed border-gray-300 hover:border-sky-400 rounded-lg text-xs font-mono font-bold text-gray-700 hover:text-sky-700 transition-colors shadow-2xs"
            title="Nhấn để sao chép mã"
          >
            <span>{{ promo.code }}</span>
            <svg class="w-3.5 h-3.5 text-gray-400 group-hover/code:text-sky-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
          </div>

          <!-- Use button -->
          <button
            type="button"
            @click="useVoucher(promo.code)"
            class="px-3.5 py-1.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-xs font-bold rounded-lg shadow-sm transition-colors"
          >
            Dùng ngay
          </button>
        </div>
      </div>
    </div>

    <!-- Usage Guide Section -->
    <section class="mt-14 bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs">
      <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6 text-center">
        Hướng Dẫn 3 Bước Dùng Voucher Tại TechMart
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex flex-col items-center text-center p-4 rounded-xl bg-gray-50">
          <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center font-black text-lg mb-3">
            1
          </div>
          <h3 class="text-sm font-bold text-gray-900 mb-1">Chọn & Sao Chép Mã</h3>
          <p class="text-xs text-gray-500">
            Tìm kiếm mã voucher phù hợp với đơn hàng và bấm biểu tượng sao chép mã.
          </p>
        </div>

        <div class="flex flex-col items-center text-center p-4 rounded-xl bg-gray-50">
          <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center font-black text-lg mb-3">
            2
          </div>
          <h3 class="text-sm font-bold text-gray-900 mb-1">Chọn Mua Sản Phẩm</h3>
          <p class="text-xs text-gray-500">
            Thêm các sản phẩm bạn muốn mua vào giỏ hàng đảm bảo đạt giá trị tối thiểu của mã.
          </p>
        </div>

        <div class="flex flex-col items-center text-center p-4 rounded-xl bg-gray-50">
          <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center font-black text-lg mb-3">
            3
          </div>
          <h3 class="text-sm font-bold text-gray-900 mb-1">Áp Dụng & Tiết Kiệm</h3>
          <p class="text-xs text-gray-500">
            Tại trang Thanh toán, dán mã vào ô "Mã giảm giá" và bấm Áp dụng để được trừ tiền ngay!
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import { usePromotion } from "~/composables/promotion/usePromotion";
import { TOVOUCHER } from "~/utils/constants";

const breadcrumbs = [{ label: "Khuyến mãi & Voucher", href: TOVOUCHER }];

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
const searchQuery = ref("");

const filterTabs = [
  { id: "all", label: "Tất cả" },
  { id: "fixed", label: "Giảm tiền mặt" },
  { id: "percent", label: "Giảm theo %" },
  { id: "freeship", label: "Miễn phí vận chuyển" },
];

const filteredPromotions = computed(() => {
  let list = promotions.value;

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter(
      (p) =>
        p.code.toLowerCase().includes(q) ||
        p.name.toLowerCase().includes(q) ||
        (p.description && p.description.toLowerCase().includes(q))
    );
  }

  if (activeFilter.value === "freeship") {
    return list.filter(
      (p) =>
        p.code.toLowerCase().includes("ship") ||
        p.name.toLowerCase().includes("ship") ||
        p.type === "free_ship"
    );
  }

  if (activeFilter.value === "percent") {
    return list.filter((p) =>
      p.actions?.some((a) => a.actionType === "percent")
    );
  }

  if (activeFilter.value === "fixed") {
    return list.filter((p) =>
      p.actions?.some((a) => a.actionType === "fixed" || a.actionType === "fixed_amount")
    );
  }

  return list;
});

onMounted(() => {
  fetchPromotions();
});

definePageMeta({
  layout: "client",
});

useHead({
  title: "Kho Voucher & Mã Giảm Giá TechMart | Cập Nhật Mới Nhất",
  meta: [
    {
      name: "description",
      content: "Tổng hợp các mã giảm giá, voucher khuyến mãi hấp dẫn nhất tại TechMart. Săn mã freeship và giảm giá ngay hôm nay!",
    },
  ],
});
</script>

<style scoped></style>
