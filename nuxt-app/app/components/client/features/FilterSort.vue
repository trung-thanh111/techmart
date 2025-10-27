<template>
  <div class="relative inline-block group">
    <a href="#" class="flex items-center space-x-1 text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]" @click.prevent>
      <span class="text-xs md:text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">Sắp xếp</span>
      <svg viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5">
        <defs>
          <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color: rgba(43, 57, 144, 0.91)" />
            <stop offset="50%" style="stop-color: #0097cc" />
            <stop offset="100%" style="stop-color: #8dc63f" />
          </linearGradient>
        </defs>
        <path fill="url(#iconGradient)" d="M151.6 469.6C145.5 476.2 137 480 128 480s-17.5-3.8-23.6-10.4l-88-96c-11.9-13-11.1-33.3 2-45.2s33.3-11.1 45.2 2L96 365.7V64c0-17.7 14.3-32 32-32s32 14.3 32 32v301.7l32.4-35.4c11.9-13 32.2-13.9 45.2-2s13.9 32.2 2 45.2l-88 96zM320 32h32c17.7 0 32 14.3 32 32s-14.3 32-32 32h-32c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128h96c17.7 0 32 14.3 32 32s-14.3 32-32 32h-96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128h160c17.7 0 32 14.3 32 32s-14.3 32-32 32H320c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128h224c17.7 0 32 14.3 32 32s-14.3 32-32 32H320c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
      </svg>
    </a>
    <div class="absolute top-full right-0 w-full h-12 group-hover:block hidden z-0"></div>
    <div class="absolute right-0 z-50 mt-2 w-48 md:w-64 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-2xl hidden group-hover:block transition-all duration-300 ease-in-out">
      <div class="py-2 px-3 space-y-1 text-xs md:text-sm text-gray-500">
        <button v-for="opt in sortOptions" :key="opt.value" @click="handleSelectSort(opt.value)" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'bg-gray-100': props.sort === opt.value }">
          <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
            <span v-html="opt.icon" class="w-4 h-4"></span>
            {{ opt.label }}
          </span>
          <svg v-if="props.sort === opt.value" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500">
            <path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n";

const props = defineProps<{
  sort: string;
  setSort: (sort: string) => void;
}>();

const { t } = useI18n();

const sortOptions = [
  {
    label: t("client.common.filter.newest"),
    value: "created_at,desc",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.3 0-200-89.7-200-200S145.7 56 256 56s200 89.7 200 200-89.7 200-200 200zm12-200V136c0-6.6-5.4-12-12-12s-12 5.4-12 12v120c0 6.6 5.4 12 12 12h88c6.6 0 12-5.4 12-12s-5.4-12-12-12h-76z"/></svg>`,
  },
  {
    label: t("client.common.filter.oldest"),
    value: "created_at,asc",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.3 0-200-89.7-200-200S145.7 56 256 56s200 89.7 200 200-89.7 200-200 200zm12-200V136c0-6.6-5.4-12-12-12s-12 5.4-12 12v120c0 6.6 5.4 12 12 12h88c6.6 0 12-5.4 12-12s-5.4-12-12-12h-76z"/></svg>`,
  },
  {
    label: t("client.common.filter.price_high"),
    value: "price_origin,desc",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M576 80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80s21.5 48 48 48h480c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H176c-26.5 0-48 21.5-48 48s21.5 48 48 48h352c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H304c-26.5 0-48 21.5-48 48s21.5 48 48 48h224c26.5 0 48-21.5 48-48z"/></svg>`,
  },
  {
    label: t("client.common.filter.price_low"),
    value: "price_origin,asc",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M576 432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48s21.5-48 48-48h480c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48s21.5-48 48-48h224c26.5 0 48 21.5 48 48z"/></svg>`,
  },
  {
    label: t("client.common.filter.best_seller"),
    value: "sold_count,desc",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 301.3l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L320 301.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l112-112z"/></svg>`,
  },
];

const handleSelectSort = (value: string) => {
  if (props.sort !== value) {
    props.setSort(value);
  }
};
</script>
