<template>
  <div class="relative inline-block group">
    <a href="#" class="flex items-center space-x-1 text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f">
      <span class="text-xs md:text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">Sắp xếp</span>
      <svg viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5">
        <defs>
          <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color: rgba(43, 57, 144, 0.91); stop-opacity: 1" />
            <stop offset="50%" style="stop-color: #0097cc; stop-opacity: 1" />
            <stop offset="100%" style="stop-color: #8dc63f; stop-opacity: 1" />
          </linearGradient>
        </defs>
        <path fill="url(#iconGradient)" d="M151.6 469.6C145.5 476.2 137 480 128 480s-17.5-3.8-23.6-10.4l-88-96c-11.9-13-11.1-33.3 2-45.2s33.3-11.1 45.2 2L96 365.7 96 64c0-17.7 14.3-32 32-32s32 14.3 32 32l0 301.7 32.4-35.4c11.9-13 32.2-13.9 45.2-2s13.9 32.2 2 45.2l-88 96zM320 32l32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-96 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l160 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-160 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
      </svg>
    </a>
    <div class="absolute top-full right-0 w-full h-6 group-hover:block hidden z-0"></div>
    <div class="absolute right-0 z-50 mt-2 w-48 md:w-64 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-2xl ring-1 ring-black/10 hidden group-hover:block transition-all duration-300 ease-in-out">
      <div class="py-2 px-3 space-y-1 text-xs md:text-sm text-gray-600">
        <button class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.3 0-200-89.7-200-200S145.7 56 256 56s200 89.7 200 200-89.7 200-200 200zm12-200V136c0-6.6-5.4-12-12-12s-12 5.4-12 12v120c0 6.6 5.4 12 12 12h88c6.6 0 12-5.4 12-12s-5.4-12-12-12h-76z" /></svg>
            Mới nhất
          </span>
          <svg v-if="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500 opacity-0 group-[.active]:opacity-100"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
        </button>
        <button class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80s21.5 48 48 48h480c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H176c-26.5 0-48 21.5-48 48s21.5 48 48 48h352c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H304c-26.5 0-48 21.5-48 48s21.5 48 48 48h224c26.5 0 48-21.5 48-48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
            Giá: Thấp đến Cao
          </span>
          <svg v-if="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500 opacity-0 group-[.active]:opacity-100"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
        </button>
        <button class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48s21.5-48 48-48h480c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48s21.5-48 48-48h224c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
            Giá: Cao đến Thấp
          </span>
          <svg v-if="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500 opacity-0 group-[.active]:opacity-100"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
        </button>
        <button class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors">
              <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 301.3l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L320 301.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l112-112z" />
            </svg>
            Bán chạy nhất
          </span>
          <svg v-if="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500 opacity-0 group-[.active]:opacity-100"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
        </button>
        <button class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M287.9 17.8L354 150.2l146.1 21.3c26.2 3.8 36.7 36 17.7 54.6l-105.7 103 25 145.5c4.5 26.2-23 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.5-46.4-33.7l25-145.5L30.2 226.1c-19-18.6-8.5-50.8 17.7-54.6L194 150.2 260.1 17.8c11.7-23.6 45.6-23.9 57.8 0z" /></svg>
            Đánh giá cao
          </span>
          <svg v-if="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500 opacity-0 group-[.active]:opacity-100"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
        </button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
const props = defineProps<{
  sort: string;
  setSort: (sort: string) => void;
}>();
</script>
