<template>
  <div class="flex justify-start">
    <button type="button" @click="$emit('click')" class="flex items-center gap-2 text-sm md:px-4 md:py-2 px-3 py-1.5 border border-transparent rounded shadow-sm md:mt-5 mt-3 transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 4.5L3 12m0 0l7.5 7.5M3 12h18" />
      </svg>
      <slot>Trở lại</slot>
    </button>
  </div>
</template>

<script setup lang="ts">
defineEmits<{
  (e: "click"): void;
}>();
</script>
