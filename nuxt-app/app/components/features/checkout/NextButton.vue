<template>
  <div class="flex justify-end sticky bottom-2 md:left-[56%] z-10">
    <button type="button" @click="$emit('click')" :disabled="disabled" :class="['flex items-center gap-2 text-sm md:px-4 md:py-2 px-3 py-1.5 border border-transparent rounded shadow-sm md:mt-5 mt-3 transition-colors', disabled ? 'text-gray-400 bg-gray-300 cursor-not-allowed' : 'text-white bg-teal-500 hover:bg-teal-600']">
      <slot>Tiếp tục</slot>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
      </svg>
    </button>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  disabled?: boolean;
}>();

defineEmits<{
  (e: "click"): void;
}>();
</script>
