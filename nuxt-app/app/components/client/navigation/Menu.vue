<template>
  <div class="relative group/menu">
    <button type="button" class="flex items-center justify-center cursor-pointer px-4 py-2 hover:bg-white/10 hover:text-white transition-colors duration-200 rounded" @mouseenter="handleMenuOpen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 mt-0.5 mx-2">
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
      </svg>
      <span>{{ $t("client.nav.categories") }}</span>
    </button>
    <div v-if="isOpen && hasAnyChildren" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40" @click="handleMenuClose" />
    <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-150 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
      <div v-if="isOpen && hasAnyChildren" class="absolute top-full left-0 z-50 bg-white text-gray-700 shadow-2xl rounded-lg overflow-hidden mt-2" style="width: 1280px" @mouseenter="handleMenuOpen" @mouseleave="handleMenuClose">
        <div class="relative flex">
          <div class="flex-shrink-0 bg-gray-50 w-[250px]">
            <div class="p-4">
              <ul class="space-y-1">
                <li v-for="item in level1Items" :key="item.id" @mouseenter="handleLevel1Hover(item)">
                  <NuxtLink :to="`/${item.slug}`" :class="['flex items-center justify-between px-3 py-2.5 rounded cursor-pointer transition-all duration-150', hoveredLevel1?.id === item.id ? 'bg-sky-50 text-sky-600 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-sky-500']">
                    <span class="text-sm">{{ item.name }}</span>
                    <svg v-if="hasChildren(item)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 flex-shrink-0">
                      <polyline points="9,18 15,12 9,6" />
                    </svg>
                  </NuxtLink>
                </li>
              </ul>
            </div>
          </div>
          <div class="flex-1 flex relative">
            <div class="flex flex-1">
              <div v-if="hoveredLevel1 && level2Items.length > 0" class="flex-shrink-0 w-[280px] bg-white border-r border-gray-100">
                <div class="p-4">
                  <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    {{ hoveredLevel1.name }}
                  </h4>
                  <ul class="space-y-1">
                    <li v-for="item in level2Items" :key="item.id" @mouseenter="handleLevel2Hover(item)">
                      <NuxtLink :to="`/${item.slug}`" :class="['flex items-center justify-between px-3 py-2 rounded text-sm transition-all duration-150', hoveredLevel2?.id === item.id ? 'bg-sky-50 text-sky-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-sky-500']">
                        <span>{{ item.name }}</span>
                        <svg v-if="hasChildren(item)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 flex-shrink-0">
                          <polyline points="9,18 15,12 9,6" />
                        </svg>
                      </NuxtLink>
                    </li>
                  </ul>
                  <div v-if="hoveredLevel1.slug" class="mt-4 pt-3 border-t border-gray-100">
                    <NuxtLink :to="`/${hoveredLevel1.slug}`" class="text-xs text-sky-600 hover:text-sky-700 font-medium hover:underline">{{ $t("common.viewAll") }} {{ hoveredLevel1.name }}</NuxtLink>
                  </div>
                </div>
              </div>
              <div v-if="hoveredLevel2 && level3Items.length > 0" class="flex-shrink-0 w-[280px] bg-white border-r border-gray-100">
                <div class="p-4">
                  <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    {{ hoveredLevel2.name }}
                  </h4>
                  <ul class="space-y-1">
                    <li v-for="item in level3Items" :key="item.id" @mouseenter="handleLevel3Hover(item)">
                      <NuxtLink :to="`/${item.slug}`" :class="['flex items-center justify-between px-3 py-2 rounded text-sm transition-all duration-150', hoveredLevel3?.id === item.id ? 'bg-sky-50 text-sky-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-sky-500']">
                        <span>{{ item.name }}</span>
                        <svg v-if="hasChildren(item)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 flex-shrink-0">
                          <polyline points="9,18 15,12 9,6" />
                        </svg>
                      </NuxtLink>
                    </li>
                  </ul>
                  <div v-if="hoveredLevel2.slug" class="mt-4 pt-3 border-t border-gray-100">
                    <NuxtLink :to="`/${hoveredLevel2.slug}`" class="text-xs text-sky-600 hover:text-sky-700 font-medium hover:underline">{{ $t("common.viewAll") }} {{ hoveredLevel2.name }}</NuxtLink>
                  </div>
                </div>
              </div>
              <div v-if="hoveredLevel3 && level4Items.length > 0" class="flex-shrink-0 w-[280px] bg-white">
                <div class="p-4">
                  <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    {{ hoveredLevel3.name }}
                  </h4>
                  <ul class="space-y-1">
                    <li v-for="item in level4Items" :key="item.id">
                      <NuxtLink :to="`/${item.slug}`" class="block px-3 py-2 rounded text-sm text-gray-700 hover:bg-gray-50 hover:text-sky-500 transition-all duration-150">
                        {{ item.name }}
                      </NuxtLink>
                    </li>
                  </ul>
                  <div v-if="hoveredLevel3.slug" class="mt-4 pt-3 border-t border-gray-100">
                    <NuxtLink :to="`/${hoveredLevel3.slug}`" class="text-xs text-sky-600 hover:text-sky-700 font-medium hover:underline">{{ $t("common.viewAll") }} {{ hoveredLevel3.name }}</NuxtLink>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="hoveredLevel1 && level2Items.length > 0" class="absolute top-4 right-4 z-10">
              <img :src="logoWebsite" class="h-8 w-auto object-contain" alt="Logo" loading="lazy" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import logoWebsite from "~/assets/shared/images/logo/logo-website.png";
import type { TreeNode } from "~/utils/nested-tree";
import type { IMenuItem } from "~/interfaces/menu/menu.interface";

// Props
interface Props {
  menuTree: TreeNode<IMenuItem>[];
}

const props = defineProps<Props>();

// State
const isOpen = ref(false);
const hoveredLevel1 = ref<TreeNode<IMenuItem> | null>(null);
const hoveredLevel2 = ref<TreeNode<IMenuItem> | null>(null);
const hoveredLevel3 = ref<TreeNode<IMenuItem> | null>(null);

// Computed: Level 1 items (parentId = 0)
const level1Items = computed(() => {
  return props.menuTree.filter((item) => item.level === 1);
});

// Computed: Check xem có menu nào có children không
const hasAnyChildren = computed(() => {
  return level1Items.value.some((item) => hasChildren(item));
});

// Computed: Level 2 items (children của level 1 đang hover)
const level2Items = computed(() => {
  if (!hoveredLevel1.value) return [];
  return hoveredLevel1.value.children || [];
});

// Computed: Level 3 items (children của level 2 đang hover)
const level3Items = computed(() => {
  if (!hoveredLevel2.value) return [];
  return hoveredLevel2.value.children || [];
});

// Computed: Level 4 items (children của level 3 đang hover)
const level4Items = computed(() => {
  if (!hoveredLevel3.value) return [];
  return hoveredLevel3.value.children || [];
});

const hasChildren = (item: TreeNode<IMenuItem>): boolean => {
  return !!(item.children && item.children.length > 0);
};

const handleMenuOpen = () => {
  isOpen.value = true;
};

const handleMenuClose = () => {
  isOpen.value = false;
  hoveredLevel1.value = null;
  hoveredLevel2.value = null;
  hoveredLevel3.value = null;
};

const handleLevel1Hover = (item: TreeNode<IMenuItem>) => {
  hoveredLevel1.value = item;
  hoveredLevel2.value = null;
  hoveredLevel3.value = null;
};

const handleLevel2Hover = (item: TreeNode<IMenuItem>) => {
  hoveredLevel2.value = item;
  hoveredLevel3.value = null;
};

const handleLevel3Hover = (item: TreeNode<IMenuItem>) => {
  hoveredLevel3.value = item;
};
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
