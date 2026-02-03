<template>
  <div>
    <button v-if="!isSidebarOpen" class="md:hidden bg-white shadow rounded-full p-2 flex items-center justify-center" @click="isSidebarOpen = true" :aria-label="$t('sidebar.openMenu')">
      <svg class="md:w-5 md:h-5 h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 448 512">
        <path
          d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 
          32 14.3 32 32s-14.3 32-32 32H32C14.3 
          128 0 113.7 0 96zM0 256c0-17.7 
          14.3-32 32-32H416c17.7 0 32 
          14.3 32 32s-14.3 32-32 
          32H32c-17.7 0-32-14.3-32-32zm448 
          160c0 17.7-14.3 32-32 
          32H32c-17.7 0-32-14.3-32-32s14.3-32 
          32-32H416c17.7 0 32 14.3 32 32z"
        />
      </svg>
    </button>
    <div v-if="isSidebarOpen && !isDesktop" class="fixed inset-0 bg-black/30 z-40" @click="isSidebarOpen = false" />
    <aside :class="['transition-all duration-300 bg-white border-r border-gray-200 sticky md:static top-0 flex-shrink-0', isSidebarOpen ? 'fixed z-50 top-0 left-0 h-full md:relative' : 'hidden md:block', isSidebarCollapsed ? 'w-16 md:w-20 p-2' : 'w-[95vw] md:w-64 p-4']">
      <button class="hidden md:flex items-center justify-end w-full mb-2 text-gray-500 hover:text-sky-600" @click="isSidebarCollapsed = !isSidebarCollapsed" :aria-label="$t('sidebar.collapseExpand')">
        <svg v-if="!isSidebarCollapsed" class="md:w-4 md:h-4 h-3 w-3" fill="currentColor" viewBox="0 0 320 512">
          <path
            d="M9.4 233.4c-12.5 12.5-12.5 
            32.8 0 45.3l192 192c12.5 
            12.5 32.8 12.5 45.3 
            0s12.5-32.8 
            0-45.3L77.3 256 
            246.6 86.6c12.5-12.5 
            12.5-32.8 
            0-45.3s-32.8-12.5-45.3 
            0l-192 192z"
          />
        </svg>
        <svg v-else class="md:w-4 md:h-4 h-3 w-3" fill="currentColor" viewBox="0 0 448 512">
          <path
            d="M0 96C0 78.3 14.3 64 
            32 64l384 0c17.7 0 32 
            14.3 32 32s-14.3 32-32 
            32L32 128C14.3 128 0 
            113.7 0 96zM0 256c0-17.7 
            14.3-32 32-32l384 0c17.7 
            0 32 14.3 32 32s-14.3 32-32 
            32L32 288c-17.7 0-32-14.3-32-32zM448 
            416c0 17.7-14.3 32-32 
            32L32 448c-17.7 0-32-14.3-32-32s14.3-32 
            32-32l384 0c17.7 0 32 14.3 32 32z"
          />
        </svg>
      </button>
      <div v-if="!isSidebarCollapsed">
        <div v-for="section in sidebarSections" :key="section.title" class="mb-6">
          <p class="text-xs font-medium text-gray-800 uppercase mb-2">
            {{ section.title }}
          </p>
          <nav class="space-y-1">
            <NuxtLink v-for="item in section.items" :key="item.to" :to="item.to" :prefetch="false" class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-[12px]" :class="getLinkClass(item.to, item.class)">
              {{ item.label }}
            </NuxtLink>
          </nav>
        </div>
      </div>
      <div v-else>
        <nav class="flex flex-col items-center gap-4 mt-8">
          <NuxtLink v-for="item in collapsedMenu" :key="item.to" :to="item.to" :prefetch="false" class="flex items-center justify-center w-10 h-10 rounded-full" :class="getIconLinkClass(item.to)">
            <component :is="item.icon" class="md:w-4 md:h-4 h-3 w-3" />
          </NuxtLink>
        </nav>
      </div>
    </aside>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, h } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { PROFILEINDEX, PROFILECHANGEPASSWORD, PROFILEADDRESSORDER, PROFILEORDER, TOWISHLIST, TOVOUCHER } from "~/utils/constants";

const { t } = useI18n();
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);
const isDesktop = ref(false);
const route = useRoute();

const isActivePath = (basePath: string) => {
  const current = route.path.replace(/\/+$/, "");
  const base = basePath.replace(/\/+$/, "");
  if (base === "/profile") return current === base;
  return current === base || current.startsWith(base + "/");
};
const getLinkClass = (path: string, extra?: string) => (isActivePath(path) ? "font-medium bg-sky-50 text-sky-600" : extra || "text-gray-500 hover:bg-gray-100 hover:text-sky-600");
const getIconLinkClass = (path: string) => (isActivePath(path) ? "bg-sky-50 text-sky-600" : "text-gray-500 hover:bg-sky-50 hover:text-sky-600");

const handleResize = () => {
  isDesktop.value = window.innerWidth >= 768;
  isSidebarOpen.value = isDesktop.value;
  if (!isDesktop.value) isSidebarCollapsed.value = false;
};

const sidebarSections = [
  {
    title: t("sidebar.personal"),
    items: [
      { label: t("sidebar.information"), to: PROFILEINDEX },
      { label: t("sidebar.changePassword"), to: PROFILECHANGEPASSWORD },
      { label: t("sidebar.addressOrder"), to: PROFILEADDRESSORDER },
      { label: t("sidebar.myOrders"), to: PROFILEORDER },
      { label: t("sidebar.wishlists"), to: TOWISHLIST },
      { label: t("sidebar.myVouchers"), to: TOVOUCHER },
    ],
  },
  {
    title: t("sidebar.settings"),
    items: [
      { label: t("sidebar.apiKeys"), to: "#" },
      { label: t("sidebar.notifications"), to: "#" },
      { label: t("sidebar.accountSecurity"), to: "#" },
      { label: t("sidebar.activityHistory"), to: "#" },
    ],
  },
  {
    title: t("sidebar.support"),
    items: [
      { label: t("sidebar.supportCenter"), to: "#" },
      { label: t("sidebar.privacyPolicy"), to: "#" },
      {
        label: t("sidebar.logout"),
        to: "#",
        class: "text-red-500 hover:bg-red-50 rounded-md",
      },
    ],
  },
];

const collapsedMenu = [
  {
    to: PROFILEINDEX,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 448 512" }, [h("path", { d: "M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.7-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z" })]),
  },
  {
    to: PROFILECHANGEPASSWORD,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 512 512" }, [h("path", { d: "M512 176c0 97.2-78.8 176-176 176-16.1 0-31.7-2.2-46.5-6.2l-99.7 99.7c-12.5 12.5-32.8 12.5-45.3 0l-45.3-45.3c-12.5-12.5-12.5-32.8 0-45.3l99.7-99.7C82.2 271.7 80 256.1 80 240c0-97.2 78.8-176 176-176s176 78.8 176 176z" })]),
  },
  {
    to: PROFILEADDRESSORDER,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 384 512" }, [h("path", { d: "M168 0C75.1 0 0 75.1 0 168c0 87.2 135.1 303.7 146.6 320.6 6 9.2 20.8 9.2 26.8 0C248.9 471.7 384 255.2 384 168 384 75.1 308.9 0 216 0zm0 240c-39.8 0-72-32.2-72-72s32.2-72 72-72 72 32.2 72 72-32.2 72-72 72z" })]),
  },
  {
    to: PROFILEORDER,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 512 512" }, [h("path", { d: "M512 128v256c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128c0-35.3 28.7-64 64-64h384c35.3 0 64 28.7 64 64z" })]),
  },
  {
    to: TOWISHLIST,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 512 512" }, [h("path", { d: "M462.3 62.7C407 7.6 324.8-10.6 256 48.6 187.2-10.6 105-7.6 49.7 62.7c-55.2 70.3-43.6 181.3 23.7 248.6l175.1 175.1c12.5 12.5 32.8 12.5 45.3 0l175.1-175.1c67.3-67.3 78.9-178.3 23.7-248.6z" })]),
  },
  {
    to: TOVOUCHER,
    icon: () => h("svg", { fill: "currentColor", viewBox: "0 0 576 512" }, [h("path", { d: "M0 160c0-17.7 14.3-32 32-32h512c17.7 0 32 14.3 32 32v32c-17.7 0-32 14.3-32 32s14.3 32 32 32v32c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32v-32c17.7 0 32-14.3 32-32S17.7 192 0 192v-32z" })]),
  },
];

onMounted(() => {
  handleResize();
  window.addEventListener("resize", handleResize);
});
onUnmounted(() => {
  window.removeEventListener("resize", handleResize);
});
</script>
