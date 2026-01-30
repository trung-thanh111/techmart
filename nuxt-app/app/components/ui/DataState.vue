<template>
  <div class="w-full">
    <div v-if="loading" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <p class="text-gray-600 text-base">
          Đang tải
          <span :class="loadingDotsClass">{{ loadingDots }}</span>
        </p>
      </div>
    </div>
    <div v-else-if="error" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <p class="text-red-500 text-base font-medium">{{ errorMessage || "Có lỗi xảy ra" }}</p>
      </div>
    </div>
    <div v-else-if="isEmpty" class="flex flex-col items-center justify-center py-10 text-center text-gray-500 min-h-[400px]">
      <slot name="empty">
        <div class="flex flex-col items-center justify-center">
          <NuxtImg v-if="showEmptyImage" :src="EMPTYBOX" alt="Không có dữ liệu" class="object-contain mb-4" loading="lazy" />
          <p class="text-sm text-center">{{ emptyMessage || "Không có dữ liệu" }}</p>
        </div>
      </slot>
    </div>
    <div v-else>
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onBeforeUnmount, onMounted } from "vue";
import { EMPTYBOX } from "~/utils/constants";

interface Props {
  loading?: boolean;
  error?: string | boolean | null;
  data?: any[] | any | null;
  emptyMessage?: string;
  errorMessage?: string;
  showEmptyImage?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  error: false,
  data: null,
  emptyMessage: "Không có dữ liệu",
  errorMessage: "Có lỗi xảy ra",
  showEmptyImage: true,
});

const loadingDots = ref(".");
let loadingInterval: ReturnType<typeof setInterval> | null = null;

const loadingDotsClass = computed(() => {
  return "inline-block w-4 text-left";
});

const isEmpty = computed(() => {
  if (props.loading || props.error) return false;
  if (props.data === null || props.data === undefined) return true;
  if (Array.isArray(props.data)) return props.data.length === 0;
  if (typeof props.data === "object") return Object.keys(props.data).length === 0;
  return false;
});

const startLoadingAnimation = () => {
  if (import.meta.server) return;
  if (loadingInterval) {
    clearInterval(loadingInterval);
  }
  loadingInterval = setInterval(() => {
    if (loadingDots.value === ".") {
      loadingDots.value = "..";
    } else if (loadingDots.value === "..") {
      loadingDots.value = "...";
    } else {
      loadingDots.value = ".";
    }
  }, 500);
};

const stopLoadingAnimation = () => {
  if (loadingInterval) {
    clearInterval(loadingInterval);
    loadingInterval = null;
  }
};

watch(
  () => props.loading,
  (isLoading) => {
    if (isLoading) {
      startLoadingAnimation();
    } else {
      stopLoadingAnimation();
    }
  },
);

onMounted(() => {
  if (props.loading) {
    startLoadingAnimation();
  }
});

onBeforeUnmount(() => {
  stopLoadingAnimation();
});
</script>
