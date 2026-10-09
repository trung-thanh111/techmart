<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="isOpen" class="fixed inset-0 z-50 md:hidden">
        <div @click="closeMenu" class="absolute inset-0 bg-black/50" />
        <Transition enter-active-class="transition-transform duration-300 ease-out" enter-from-class="-translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-200 ease-in" leave-from-class="translate-x-0" leave-to-class="-translate-x-full">
          <div v-if="isOpen" class="absolute left-0 top-0 h-full w-80 bg-white shadow-xl">
            <div class="flex items-center justify-between px-4 py-3 border-b bg-gradient-to-r from-[#00b88c] to-[#007c8b] text-white">
              <button v-if="currentLevel > 0" @click="goBack" class="p-2 hover:bg-white/10 rounded transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                  <polyline points="15,18 9,12 15,6" />
                </svg>
              </button>
              <h3 class="text-lg font-semibold flex-1" :class="currentLevel > 0 ? 'text-center' : ''">
                {{ currentTitle }}
              </h3>
              <button @click="closeMenu" class="p-2 hover:bg-white/10 rounded transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
            <div class="h-[calc(100%-64px)] overflow-y-auto">
              <div v-if="currentLevel === 0" class="p-4">
                <div class="space-y-2 text-sm">
                  <div @click="navigateToLevel(1)" class="flex items-center justify-between p-3 rounded transition-colors cursor-pointer text-gray-500 hover:bg-gray-100 hover:text-sky-600 group">
                    <div class="flex items-center gap-3">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 group-hover:text-sky-600 text-gray-500">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                      </svg>
                      <span class="group-hover:text-sky-600">{{ $t("nav.categories") }}</span>
                    </div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 group-hover:text-sky-600 text-gray-500">
                      <polyline points="9,18 15,12 9,6" />
                    </svg>
                  </div>
                </div>
                <div class="border-t border-gray-300 mt-4 pt-4 space-y-2 text-sm">
                  <NuxtLink :to="TOVOUCHER" @click="closeMenu" class="flex group items-center gap-3 p-3 rounded transition-colors text-gray-500 hover:bg-gray-100 hover:text-sky-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-500 group-hover:text-sky-600">
                      <polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2" />
                    </svg>
                    <span class="group-hover:text-sky-600">{{ $t("menu.promotion") }}</span>
                  </NuxtLink>
                  <NuxtLink :to="TOFAQ" @click="closeMenu" class="flex group items-center gap-3 p-3 rounded transition-colors text-gray-500 hover:bg-gray-100 hover:text-sky-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-500 group-hover:text-sky-600">
                      <circle cx="12" cy="12" r="10" />
                      <path d="M9.09 9a3 3 0 1 1 5.82 1c0 2-3 3-3 3" />
                      <line x1="12" y1="17" x2="12" y2="17" />
                    </svg>
                    <span class="group-hover:text-sky-600">{{ $t("menu.faq") }}</span>
                  </NuxtLink>
                  <NuxtLink :to="TOPOLICY" @click="closeMenu" class="flex group items-center gap-3 p-3 rounded transition-colors text-gray-500 hover:bg-gray-100 hover:text-sky-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 group-hover:text-sky-600">
                      <path d="M4 18v-6a8 8 0 0 1 16 0v6" />
                      <rect x="2" y="18" width="4" height="4" rx="1" />
                      <rect x="18" y="18" width="4" height="4" rx="1" />
                    </svg>
                    <span class="group-hover:text-sky-600">{{ $t("menu.supportPolicy") }}</span>
                  </NuxtLink>
                </div>
              </div>
              <div v-else class="p-4 space-y-2">
                <template v-for="item in currentItems" :key="item.id">
                  <div v-if="hasChildren(item)" @click="navigateToChildren(item)" class="flex items-center justify-between p-3 rounded transition-colors cursor-pointer text-gray-500 hover:bg-gray-100 hover:text-sky-600 group">
                    <span class="group-hover:text-sky-600 text-sm">{{ item.name }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 group-hover:text-sky-600 text-gray-500">
                      <polyline points="9,18 15,12 9,6" />
                    </svg>
                  </div>
                  <NuxtLink v-else :to="`/${item.slug}`" @click="closeMenu" class="flex items-center gap-3 p-3 rounded transition-colors text-gray-500 hover:bg-gray-100 hover:text-sky-600">
                    <span class="group-hover:text-sky-600 text-sm">{{ item.name }}</span>
                  </NuxtLink>
                </template>
                <div v-if="currentParent?.slug" class="mt-4 pt-4 border-t border-gray-200">
                  <NuxtLink :to="`/${currentParent.slug}`" @click="closeMenu" class="text-xs text-sky-600 hover:text-sky-700 font-medium hover:underline">{{ $t("common.viewAll") }} {{ currentParent.name }}</NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { TOVOUCHER, TOFAQ, TOPOLICY } from "~/utils/constants";
import type { TreeNode } from "~/utils/nested-tree";
import type { IMenuItem } from "~/interfaces/menu/menu.interface";

const { t } = useI18n();

// Props
interface Props {
  isOpen: boolean;
  menuTree: TreeNode<IMenuItem>[];
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits<{
  close: [];
}>();

// State
const currentLevel = ref(0); // 0: main menu, 1+: menu items
const navigationStack = ref<Array<{ items: TreeNode<IMenuItem>[]; parent: TreeNode<IMenuItem> | null }>>([]);
const currentParent = ref<TreeNode<IMenuItem> | null>(null);

// Computed
const level1Items = computed(() => {
  return props.menuTree.filter((item) => item.level === 1);
});

const currentItems = computed(() => {
  if (currentLevel.value === 0) return [];
  if (currentLevel.value === 1) return level1Items.value;

  const lastNav = navigationStack.value[navigationStack.value.length - 1];
  return lastNav?.items || [];
});

const currentTitle = computed(() => {
  if (currentLevel.value === 0) return $t("menu.title");
  if (currentLevel.value === 1) return $t("menu.categories");
  return currentParent.value?.name || $t("menu.title");
});

const hasChildren = (item: TreeNode<IMenuItem>): boolean => {
  return !!(item.children && item.children.length > 0);
};

const navigateToLevel = (level: number) => {
  currentLevel.value = level;
  if (level === 1) {
    navigationStack.value = [];
    currentParent.value = null;
  }
};

const navigateToChildren = (item: TreeNode<IMenuItem>) => {
  if (!item.children || item.children.length === 0) return;

  navigationStack.value.push({
    items: item.children,
    parent: item,
  });

  currentParent.value = item;
  currentLevel.value++;
};

const goBack = () => {
  if (currentLevel.value === 1) {
    currentLevel.value = 0;
    navigationStack.value = [];
    currentParent.value = null;
  } else if (currentLevel.value > 1) {
    navigationStack.value.pop();
    currentLevel.value--;

    const lastNav = navigationStack.value[navigationStack.value.length - 1];
    currentParent.value = lastNav?.parent || null;
  }
};

const closeMenu = () => {
  emit("close");
  setTimeout(() => {
    currentLevel.value = 0;
    navigationStack.value = [];
    currentParent.value = null;
  }, 300);
};

// Watch để reset khi menu đóng từ bên ngoài
watch(
  () => props.isOpen,
  (newVal) => {
    if (!newVal) {
      currentLevel.value = 0;
      navigationStack.value = [];
      currentParent.value = null;
    }
  },
);
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
