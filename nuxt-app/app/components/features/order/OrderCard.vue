<template>
  <div class="rounded-md shadow overflow-hidden border border-gray-100 hover:border-sky-400 cursor-pointer transition-all duration-300 bg-white">
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-2 bg-gray-100">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 384 512">
          <path d="M280 64l40 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l40 0 9.6 0C121 27.5 153.3 0 192 0s71 27.5 78.4 64l9.6 0zM64 112c-8.8 0-16 7.2-16 16l0 320c0 8.8 7.2 16 16 16l256 0c8.8 0 16-7.2 16-16l0-320c0-8.8-7.2-16-16-16l-16 0 0 24c0 13.3-10.7 24-24 24l-88 0-88 0c-13.3 0-24-10.7-24-24l0-24-16 0zm128-8a24 24 0 1 0 0-48 24 24 0 1 0 0 48z" />
        </svg>
        <NuxtLink :to="`${PROFILEORDER}/${order.code}`" class="text-sm font-medium text-gray-700 hover:text-sky-600 flex items-center gap-2">
          <span class="font-semibold">#{{ order.code }}</span>
          <span v-if="isNew" class="inline-flex items-center gap-1 bg-emerald-100/80 text-emerald-400 text-[9px] uppercase font-semibold px-2 py-0.5 rounded-full">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full shadow-lg"></span>
            mới
          </span>
        </NuxtLink>
      </div>
      <div class="flex items-center gap-2">
        <span :class="statusClass" class="text-[9px] uppercase font-semibold px-2 py-0.5 rounded">
          {{ ORDER_STATUS_LABELS[order.status || ""] || order.status }}
        </span>
        <div class="relative" ref="dropdownRef">
          <button @click.stop="toggleDropdown" class="p-1 hover:bg-gray-200 rounded transition-colors">
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
            </svg>
          </button>
          <Transition name="dropdown">
            <div v-if="showDropdown" class="absolute right-0 top-full mt-1 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-50 text-gray-500 text-xs">
              <NuxtLink :to="`${PROFILEORDER}/${order.code}`" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100" @click="showDropdown = false">Xem chi tiết</NuxtLink>
              <button
                v-if="order.status === 'delivered'"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100"
                @click="
                  emit('review', order);
                  showDropdown = false;
                "
              >
                Đánh giá
              </button>
              <button
                v-if="order.status === 'pending'"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100"
                @click="
                  navigateTo(`${PROFILEORDER}/${order.code}`);
                  showDropdown = false;
                "
              >
                Theo dõi đơn
              </button>
              <button
                v-if="order.status === 'pending'"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50"
                @click="
                  emit('cancel', order);
                  showDropdown = false;
                "
              >
                Hủy đơn hàng
              </button>
              <button
                v-if="order.status === 'delivered'"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100"
                @click="
                  emit('reorder', order);
                  showDropdown = false;
                "
              >
                Mua lại
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <!-- Products -->
    <NuxtLink :to="`${PROFILEORDER}/${order.code}`" class="block px-4 py-2 divide-y divide-gray-100">
      <OrderProductItem v-for="(item, idx) in order.items" :key="item.id || idx" :item="item" :show-price="true" />
    </NuxtLink>

    <!-- Footer -->
    <div class="flex items-center justify-end px-4 py-2 bg-gray-50 border-t border-gray-100">
      <p class="text-sm">
        Tổng tiền:
        <span class="text-red-500 font-semibold">{{ formatPrice(order.totalAmount || 0) }}</span>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import type { IOrder } from "~/interfaces/order/order.interface";
import { ORDER_STATUS_LABELS } from "~/utils/order.constants";
import { formatPrice } from "~/utils/helpers";
import { getStatusClass } from "~/utils/order.helpers";
import OrderProductItem from "./OrderProductItem.vue";

const props = defineProps<{ order: IOrder }>();
const emit = defineEmits<{
  (e: "cancel", order: IOrder): void;
  (e: "review", order: IOrder): void;
  (e: "reorder", order: IOrder): void;
}>();

const showDropdown = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const isNew = computed(() => {
  if (!props.order.createdAt) return false;
  return Date.now() - new Date(props.order.createdAt).getTime() < 24 * 60 * 60 * 1000;
});

const statusClass = computed(() => getStatusClass(props.order.status));

const toggleDropdown = () => (showDropdown.value = !showDropdown.value);

const handleClickOutside = (e: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) showDropdown.value = false;
};

onMounted(() => document.addEventListener("click", handleClickOutside));
onUnmounted(() => document.removeEventListener("click", handleClickOutside));
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.15s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
