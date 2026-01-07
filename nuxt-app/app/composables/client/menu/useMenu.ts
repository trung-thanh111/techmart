import { ref, computed } from "vue";
import { menuItemService } from "~/services/menu/item.service";
import type { IMenuItem } from "~/interfaces/menu/menu.interface";

export const useMenu = () => {
  const menuTree = ref<IMenuItem[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Cache để tránh gọi API nhiều lần
  const treeCache = new Map<number, IMenuItem[]>();

  const loadMenu = async (menuId: number) => {
    if (treeCache.has(menuId)) {
      menuTree.value = treeCache.get(menuId) || [];
      return;
    }

    loading.value = true;
    error.value = null;

    try {
      const response = await menuItemService.paginationPublic({
        menu_id: menuId,
        publish: 2,
        per_page: 1000,
      });

      if (response.data?.data && response.data.data.length > 0) {
        const rootItems = response.data.data.filter((item: any) => item.parentId === 0);
        menuTree.value = rootItems;

        // Lưu vào cache
        treeCache.set(menuId, rootItems);
      } else {
        throw new Error("No menu items found");
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : "Unknown error";
      menuTree.value = [];
    } finally {
      loading.value = false;
    }
  };

  const getMenuByLevel = (level: number): IMenuItem[] => {
    return menuTree.value.filter((item) => item.level === level);
  };

  const getChildren = (parentId: number): IMenuItem[] => {
    const findChildren = (items: IMenuItem[]): IMenuItem[] => {
      for (const item of items) {
        if (item.id === parentId) {
          return item.children || [];
        }
        if (item.children) {
          const children = findChildren(item.children);
          if (children.length > 0) {
            return children;
          }
        }
      }
      return [];
    };

    return findChildren(menuTree.value);
  };

  const clearCache = () => {
    treeCache.clear();
    menuTree.value = [];
  };

  return {
    menuTree: computed(() => menuTree.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    loadMenu,
    getMenuByLevel,
    getChildren,
    clearCache,
  };
};
