<template>
  <div class="utility-tools">
    <!-- back to top -->
    <div class="back-to-top group fixed bottom-20 right-6 z-50 flex justify-center items-center text-zinc-600 text-sm font-bold">
      <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" class="shadow-lg flex items-center group-hover:gap-2 hover:scale-95 bg-gradient-to-r from-[#007c8b] to-[#00b88c] p-3 rounded-full cursor-pointer duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
      </button>
    </div>

    <!-- /chat  -->
    <component
      :is="chatUrl ? 'a' : 'button'"
      :href="chatUrl || undefined"
      :target="chatUrl ? '_blank' : undefined"
      rel="noopener noreferrer"
      class="group fixed bottom-36 right-6 z-50 w-[45px] h-[45px] flex items-center justify-center rounded-full cursor-pointer pt-[3px] bg-white shadow-lg transition-all duration-500"
    >
      <svg viewBox="0 0 1000 1000" class="w-6 h-6">
        <defs>
          <linearGradient id="chatGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#007c8b" />
            <stop offset="100%" stop-color="#00b88c" />
          </linearGradient>
        </defs>
        <path fill="url(#chatGradient)" d="M881.1,720.5H434.7L173.3,941V720.5h-54.4C58.8,720.5,10,671.1,10,610.2v-441C10,108.4,58.8,59,118.9,59h762.2C941.2,59,990,108.4,990,169.3v441C990,671.1,941.2,720.5,881.1,720.5L881.1,720.5z M935.6,169.3c0-30.4-24.4-55.2-54.5-55.2H118.9c-30.1,0-54.5,24.7-54.5,55.2v441c0,30.4,24.4,55.1,54.5,55.1h54.4h54.4v110.3l163.3-110.2H500h381.1c30.1,0,54.5-24.7,54.5-55.1V169.3L935.6,169.3z M717.8,444.8c-30.1,0-54.4-24.7-54.4-55.1c0-30.4,24.3-55.2,54.4-55.2c30.1,0,54.5,24.7,54.5,55.2C772.2,420.2,747.8,444.8,717.8,444.8L717.8,444.8z M500,444.8c-30.1,0-54.4-24.7-54.4-55.1c0-30.4,24.3-55.2,54.4-55.2c30.1,0,54.4,24.7,54.4,55.2C554.4,420.2,530.1,444.8,500,444.8L500,444.8z M282.2,444.8c-30.1,0-54.5-24.7-54.5-55.1c0-30.4,24.4-55.2,54.5-55.2c30.1,0,54.4,24.7,54.4,55.2C336.7,420.2,312.3,444.8,282.2,444.8L282.2,444.8z" />
      </svg>
      <span class="absolute -top-10 px-3 py-1 rounded-md text-white text-sm bg-orange-400 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none font-medium">{{ $t("utilityTool.chat") }}</span>
    </component>

    <!-- Modal Voucher -->
    <button @click="openVoucherModal" class="fixed bottom-36 left-6 z-50 bg-white rounded-full shadow-md hover:scale-105 transition-all">
      <NuxtImg src="/images/icon-voucher.png" :alt="$t('utilityTool.offers')" class="w-12 h-12 object-contain" format="webp" loading="lazy" />
    </button>
    <div v-if="showVoucherModal" class="modal fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeVoucherModal">
      <!-- Modal Container -->
      <div class="bg-white w-full max-w-xl rounded-xl overflow-hidden shadow-2xl relative animate-slide-up">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-green-400 text-white text-center py-4 text-xl md:text-2xl font-bold relative">
          {{ $t("utilityTool.offers") }}
          <button @click="closeVoucherModal" class="absolute top-2.5 right-4 text-gray-600 bg-white/80 hover:bg-white w-9 h-9 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-200">✕</button>
        </div>

        <div class="px-4 py-4 space-y-4 max-h-[70vh] overflow-y-auto scrollbar-hide bg-gray-50">
          <div v-if="loadingPromotions" class="flex justify-center py-8">
            <span class="text-sm text-gray-500">{{ $t("common.loading") }}</span>
          </div>
          <div v-else-if="promotions.length === 0" class="text-center py-8 text-gray-500 text-sm">
            {{ $t("utilityTool.noVouchers") }}
          </div>
          <!-- Voucher Item (loop) -->
          <div v-for="(voucher, index) in promotions" :key="voucher.id || index" class="voucher-item flex flex-col bg-sky-50 border border-sky-100 p-3 rounded-lg shadow-sm">
            <div class="flex items-center gap-3">
              <div class="voucher-img w-20 h-20 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden bg-white p-1 border">
                <NuxtImg :src="voucher.image || '/images/icon-voucher.png'" alt="Voucher" class="object-contain w-full h-full" loading="lazy" />
              </div>
              <div class="w-[1px] h-16 border-l border-dashed border-gray-300"></div>
              <div class="flex-1 min-w-0">
                <h3 class="voucher-title font-semibold text-gray-800 text-sm md:text-base line-clamp-1 mb-1">{{ voucher.name }}</h3>
                <div class="flex items-center gap-2 mb-2">
                  <div class="flex-1 flex items-center bg-white px-2 py-1 rounded border border-gray-200 min-w-0">
                    <span class="text-sky-600 mr-1 text-xs">📋</span>
                    <span class="voucher-code font-mono text-xs md:text-sm text-gray-700 font-bold truncate">{{ voucher.code || 'TECHMART' }}</span>
                  </div>
                  <button @click="copyVoucherCode(voucher.code)" class="bg-gradient-to-r from-sky-600 to-cyan-500 hover:from-sky-700 hover:to-cyan-600 text-white py-1 px-3 rounded text-xs font-medium whitespace-nowrap transition-all shadow-sm">
                    {{ copiedCode === voucher.code ? $t("utilityTool.copied") : $t("utilityTool.copy") }}
                  </button>
                </div>

                <div class="flex justify-between items-center text-xs">
                  <div class="text-gray-500 truncate mr-2">{{ formatExpiry(voucher.endDate) }}</div>
                  <button @click="toggleCondition(index)" class="text-sky-600 hover:text-sky-700 font-medium underline cursor-pointer whitespace-nowrap">
                    {{ showConditions[index] ? $t("utilityTool.hideConditions") : $t("utilityTool.conditions") }}
                  </button>
                </div>
              </div>
            </div>
            <div v-show="showConditions[index]" class="voucher-condition border border-sky-200 bg-white rounded-md text-xs text-gray-600 mt-2 p-2 [&>p]:mb-1 [&>p:last-child]:mb-0" v-html="voucher.description || $t('utilityTool.voucherCondition')"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- phone number  -->
    <a
      v-if="hotline"
      :href="`tel:${cleanPhone(hotline)}`"
      class="group fixed bottom-20 left-6 z-50 flex items-center justify-start w-[45px] h-[45px] bg-[#00d757] rounded-full cursor-pointer overflow-hidden shadow-lg transition-all duration-300 active:translate-x-[2px] active:translate-y-[2px] hover:w-[150px] hover:rounded-[40px]"
    >
      <div class="flex justify-center items-center w-full group-hover:w-[30%] transition-all duration-300 pl-0 group-hover:pl-2">
        <svg class="w-[25px] h-[25px]" viewBox="0 0 16 16" fill="white">
          <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
        </svg>
      </div>
      <span class="block absolute right-0 w-0 opacity-0 text-white text-base font-semibold group-hover:w-[70%] group-hover:opacity-100 transition-all duration-300 pr-3">{{ hotline }}</span>
    </a>

    <!-- Drawer -->
    <transition name="slide-up">
      <div v-if="showDrawer" class="fixed z-50 w-full max-full left-0 right-0 bottom-0 overflow-y-auto scrollbar-hide bg-white border-t border-gray-200 rounded-t-lg shadow-lg transition-transform" :style="{ maxHeight: '100vh' }" @touchstart="onTouchStart" @touchmove="onTouchMove" @touchend="onTouchEnd">
        <!-- Handle -->
        <div class="px-4 py-2 cursor-pointer hover:bg-gray-50 relative">
          <span class="absolute w-8 h-1 -translate-x-1/2 bg-gray-300 rounded-lg top-3 left-1/2"></span>
          <h5 class="inline-flex items-center text-base text-gray-600 font-medium">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 18 18">
              <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10ZM17 13h-2v-2a1 1 0 0 0-2 0v2h-2a1 1 0 0 0 0 2h2v2a1 1 0 0 0 2 0v-2h2a1 1 0 0 0 0-2Z" />
            </svg>
            {{ $t("utilityTool.compareProducts") }}
          </h5>
          <button @click="closeDrawer" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Nội dung tiện ích -->
        <div class="grid grid-cols-3 gap-4 p-4 lg:grid-cols-4">
          <div class="p-4 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="flex justify-center items-center p-2 mx-auto mb-2 bg-gray-200 rounded-full w-12 h-12">
              <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 22 21">
                <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
              </svg>
            </div>
            <div class="text-center text-sm text-gray-600 font-medium">{{ $t("utilityTool.chart") }}</div>
          </div>

          <div class="p-4 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="flex justify-center items-center p-2 mx-auto mb-2 bg-gray-200 rounded-full w-12 h-12">
              <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 14">
                <path d="M18 0H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM9 6v2H2V6h7Zm2 0h7v2h-7V6Zm-9 4h7v2H2v-2Zm9 2v-2h7v2h-7Z" />
              </svg>
            </div>
            <div class="text-center text-sm text-gray-600 font-medium">{{ $t("utilityTool.table") }}</div>
          </div>

          <div class="p-4 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="flex justify-center items-center p-2 mx-auto mb-2 bg-gray-200 rounded-full w-12 h-12">
              <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 14 20">
                <path d="M13.383.076a1 1 0 0 0-1.09.217L11 1.586 9.707.293a1 1 0 0 0-1.414 0L7 1.586 5.707.293a1 1 0 0 0-1.414 0L3 1.586 1.707.293A1 1 0 0 0 0 1v18a1 1 0 0 0 1.707.707L3 18.414l1.293 1.293a1 1 0 0 0 1.414 0L7 18.414l1.293 1.293a1 1 0 0 0 1.414 0L11 18.414l1.293 1.293A1 1 0 0 0 14 19V1a1 1 0 0 0-.617-.924ZM10 15H4a1 1 0 1 1 0-2h6a1 1 0 0 1 0 2Z" />
              </svg>
            </div>
            <div class="text-center text-sm text-gray-600 font-medium">{{ $t("utilityTool.ticket") }}</div>
          </div>

          <!-- Thêm thêm các tiện ích khác nếu cần -->
          <div class="p-4 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="flex justify-center items-center p-2 mx-auto mb-2 bg-gray-200 rounded-full w-12 h-12">
              <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="text-center text-sm text-gray-600 font-medium">{{ $t("utilityTool.view") }}</div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Compare Components -->
    <CompareBar @open-search="openSearchModal" />
    <SearchProductModal v-model="showSearchModal" :selection-mode="searchSelectionMode" @added="handleProductsAdded" @close="closeSearchModal" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import CompareBar from "~/components/navigation/CompareBar.vue";
import SearchProductModal from "~/components/features/SearchProductModal.vue";
import { promotionService } from "~/services/promotion/promotion.service";
import type { IPromotion } from "~/interfaces/promotion/promotion.interface";
import { useAppToast } from "~/composables/useAppToast";
import { useSystemConfig } from "~/composables/system/useSystemConfig";

const { t } = useI18n();
const toast = useAppToast();
const { hotline, messenger, zalo } = useSystemConfig();

const cleanPhone = (phone: string) => {
  return phone ? phone.replace(/[^\d+]/g, "") : "";
};

const chatUrl = computed(() => {
  return messenger.value || zalo.value || "";
});

const showVoucherModal = ref(false);
const showConditions = ref<boolean[]>([]);
const promotions = ref<IPromotion[]>([]);
const loadingPromotions = ref(false);
const copiedCode = ref<string | null>(null);

const showDrawer = ref(false);
const showSearchModal = ref(false);
const searchSelectionMode = ref<"record" | "checkbox">("record");

let touchStartY = 0;
let touchEndY = 0;

const fetchPromotions = async () => {
  if (promotions.value.length > 0) return;
  loadingPromotions.value = true;
  try {
    const res = await promotionService.pagination({ publish: 2, perpage: 10 });
    promotions.value = res?.data?.data || [];
    showConditions.value = new Array(promotions.value.length).fill(false);
  } catch (error) {
    console.error("Error loading promotions in UtilityTool:", error);
  } finally {
    loadingPromotions.value = false;
  }
};

const openVoucherModal = () => {
  showVoucherModal.value = true;
  fetchPromotions();
};

const closeVoucherModal = () => {
  showVoucherModal.value = false;
};

const toggleCondition = (index: number) => {
  showConditions.value[index] = !showConditions.value[index];
};

const copyVoucherCode = async (code?: string | null) => {
  if (!code) return;
  try {
    await navigator.clipboard.writeText(code);
    copiedCode.value = code;
    toast.success(t("utilityTool.copySuccess"));
    setTimeout(() => {
      if (copiedCode.value === code) {
        copiedCode.value = null;
      }
    }, 3000);
  } catch (err) {
    console.error("Copy to clipboard failed:", err);
  }
};

const formatExpiry = (dateStr?: string | null) => {
  if (!dateStr) return "";
  try {
    const d = new Date(dateStr);
    return `${t("utilityTool.expiryPrefix")}${d.toLocaleDateString("vi-VN")}`;
  } catch {
    return "";
  }
};

onMounted(() => {
  fetchPromotions();
});

const openDrawer = () => {
  showDrawer.value = true;
  if (typeof document !== "undefined") {
    document.body.style.overflow = "hidden";
  }
};

const closeDrawer = () => {
  showDrawer.value = false;
  if (typeof document !== "undefined") {
    document.body.style.overflow = "auto";
  }
};

const onTouchStart = (e: TouchEvent) => {
  touchStartY = e.touches?.[0]?.clientY || 0;
};

const onTouchMove = (e: TouchEvent) => {
  touchEndY = e.touches?.[0]?.clientY || 0;
};

const onTouchEnd = () => {
  if (touchEndY - touchStartY > 60) {
    closeDrawer();
  }
};

const openSearchModal = () => {
  showSearchModal.value = true;
  searchSelectionMode.value = "record";
};

const closeSearchModal = () => {
  showSearchModal.value = false;
};

const handleProductsAdded = () => {
  closeSearchModal();
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}
</style>
