<template>
  <div class="relative rounded-lg overflow-hidden mx-auto mb-5">
    <NuxtImg src="/client/images/slide/banner-category-1.jpg" alt="Cover" class="w-full h-48 md:h-64 object-cover blur-[0.5px]" loading="lazy" />
    <div class="absolute bottom-0 left-0 w-full flex items-center px-4 md:px-6 pb-3 md:pb-4 bg-[#eef2f6] shadow-md">
      <div class="relative">
        <NuxtImg :src="avatar" :alt="'Avatar ' + user?.name" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-1 md:border-4 border-sky-500 -mt-12 md:-mt-15 shadow-lg object-cover" loading="lazy" />
      </div>
      <div class="flex-1 ml-3 md:ml-4 -mt-8 md:-mt-9 h-16 md:h-20">
        <h2 class="text-lg md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] my-1">{{ user?.name }}</h2>
        <div class="flex items-center flex-wrap gap-1 md:gap-2">
          <span :class="`inline-flex items-center rounded-md bg-${user?.userCatalogueId === 1 ? 'green' : 'red'}-100 px-2 py-1 text-xs font-medium text-${user?.userCatalogueId === 1 ? 'green' : 'red'}-500 ring-1 ring-inset`">
            <svg class="md:w-3 md:h-3 w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 512 512">
              <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm113-303L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
            </svg>
            {{ user?.userCatalogueId === 1 ? $t("client.profileHeader.customer") : $t("client.profileHeader.admin") }}
          </span>
          <span class="text-xs text-gray-500">{{ $t("client.profileHeader.since") }}</span>
          <span class="text-sky-500 text-xs hover:underline flex items-center">{{ formattedJoinedAt }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/auth/auth";
import { formatDate } from "@/utils/helpers";

const authStore = useAuthStore();
const { user } = storeToRefs(authStore);

const DEFAULT_AVATAR = "/shared/images/defaults/avatar.jpg";

const avatar = computed(() => {
  const img = user.value?.image;
  if (img && img.path) return img.url || DEFAULT_AVATAR;
  return DEFAULT_AVATAR;
});

const formattedJoinedAt = computed(() => {
  const createdAt = user.value?.createdAt;
  if (!createdAt) return "";
  const d = new Date(createdAt);
  return isNaN(d.getTime()) ? "" : formatDate(d);
});
</script>
