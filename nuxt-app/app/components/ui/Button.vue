<template>
  <div class="w-full flex justify-center mt-4 md:mt-6">
    <a href="#" type="button" class="relative flex items-center py-1.5 px-4 overflow-hidden font-medium transition-all bg-sky-500 rounded group">
      <span class="absolute inset-0 transition-all duration-500 ease-in-out delay-200 -translate-x-full bg-sky-600 rounded-md group-hover:translate-x-0"></span>
      <span class="relative w-full text-xs sm:text-sm md:text-base text-center text-white transition-colors duration-200 ease-in-out group-hover:text-white">{{ $t("common.viewMore") }}</span>
    </a>
  </div>
</template>
<script setup></script>
