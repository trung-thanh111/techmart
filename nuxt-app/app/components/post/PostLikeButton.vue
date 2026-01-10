<template>
  <button @click="handleToggleLike" :disabled="isLoading" class="flex items-center space-x-1 transition-all duration-200 hover:scale-105 active:scale-95" :class="isLiked ? 'text-red-500 hover:text-red-600' : 'text-gray-500 hover:text-red-500'">
    <svg v-if="!isLiked" class="w-5 h-5 sm:w-4 sm:h-4 transition-all duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
    </svg>
    <svg v-else class="w-5 h-5 sm:w-4 sm:h-4 transition-all duration-200 scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
    </svg>
    <span class="text-xs sm:text-sm">{{ likeCount }} {{ $t("client.post.likes") }}</span>
    <div v-if="isLoading" class="ml-1">
      <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-current"></div>
    </div>
  </button>
</template>

<script setup lang="ts">
interface Props {
  postId: number;
  isLiked: boolean;
  likeCount: number;
  isLoading: boolean;
}

interface Emits {
  (e: "toggle"): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const handleToggleLike = () => {
  emit("toggle");
};
</script>
