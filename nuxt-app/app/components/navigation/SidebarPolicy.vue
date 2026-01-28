<template>
  <div>
    <button class="md:hidden p-2 flex items-center justify-center" v-if="!isSidebarOpen" @click="isSidebarOpen = true">
      <svg class="md:w-5 md:h-5 h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 448 512">
        <path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zm448 160c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" />
      </svg>
    </button>

    <div v-if="isSidebarOpen" class="fixed inset-0 bg-black/30 z-40 md:hidden" @click="isSidebarOpen = false"></div>

    <aside :class="['transition-all duration-300 bg-white border-r border-gray-200 sticky md:static top-0 flex-shrink-0', isSidebarOpen ? 'fixed z-20 top-0 left-0 h-full md:relative md:translate-x-0' : 'md:relative', !isSidebarOpen && 'md:block hidden', 'w-[95vw] md:w-64 p-4']" v-show="isSidebarOpen || isDesktop">
      <button class="md:hidden flex items-center justify-end w-full mb-2 text-gray-500 hover:text-sky-600 focus:outline-none" @click="isSidebarOpen = false">
        <svg class="w-6 h-6" fill="none" stroke-width="2" viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
      <div v-for="(section, index) in menuSections" :key="index" class="mb-4 md:mb-8">
        <p class="text-sm font-medium text-gray-800 uppercase mb-2">
          {{ section.title }}
        </p>
        <nav class="space-y-1">
          <div v-if="section.title === $t('sidebarPolicy.policy') && isLoading" class="flex p-2 text-sm md:p-3 text-gray-400">{{ $t("sidebarPolicy.loading") }}</div>
          <NuxtLink v-else v-for="item in section.items" :key="item.path" :to="item.path" :class="['flex p-2 text-sm md:p-3 font-normal text-center whitespace-normal duration-75 cursor-pointer tabs_item', $route.path === item.path ? 'font-medium bg-gradient-to-r from-sky-500 to-sky-600 text-white' : 'text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white']" @click="!isDesktop && (isSidebarOpen = false)">
            {{ item.title }}
          </NuxtLink>
        </nav>
      </div>
    </aside>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { ISystem } from "~/interfaces/system/system.interface";
import { systemService } from "~/services/system/system.service";
import { getMenuSections } from "~/utils/system/policy";

const isSidebarOpen = ref(false);
const isDesktop = ref(false);
const policies = ref<ISystem[]>([]);
const isLoading = ref(true);

const menuSections = computed(() => {
  return getMenuSections(policies.value);
});

const loadPolicies = async () => {
  try {
    const response = await systemService.all();
    if (response.data) {
      // Lọc chỉ lấy policies
      policies.value = Array.isArray(response.data) ? response.data : [];
    }
  } catch (error) {
    console.error("Error loading policies:", error);
  } finally {
    isLoading.value = false;
  }
};

const handleResize = () => {
  isDesktop.value = window.innerWidth >= 768;
  if (isDesktop.value) {
    isSidebarOpen.value = true;
  } else {
    isSidebarOpen.value = false;
  }
};

onMounted(async () => {
  handleResize();
  window.addEventListener("resize", handleResize);
  await loadPolicies();
});
</script>
