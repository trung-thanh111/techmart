import { ref, computed } from "vue";
import { menuService } from "~/services/menu/menu.service";
import { buildTree } from "~/utils/nested-tree";
import type { IMenu, IMenuItem } from "~/interfaces/menu/menu.interface";
import type { TreeNode } from "~/utils/nested-tree";

export const useMenu = () => {
  // State
  const menu = ref<IMenu | null>(null);
  const menuTree = ref<TreeNode<IMenuItem>[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const menuCache = new Map<string, { menu: IMenu; tree: TreeNode<IMenuItem>[] }>();

  /**
   * Load menu theo slug và build tree structure
   */
  const loadMenuBySlug = async (slug: string) => {
    // Kiểm tra cache trước
    if (menuCache.has(slug)) {
      const cached = menuCache.get(slug)!;
      menu.value = cached.menu;
      menuTree.value = cached.tree;
      return;
    }

    loading.value = true;
    error.value = null;

    try {
      const response = await menuService.show(slug);

      if (!response.data) {
        throw new Error("Menu not found");
      }

      menu.value = response.data;

      const items = response.data.menuItems || [];
      const tree = buildTree<IMenuItem>(items, {
        idKey: "id",
        parentKey: "parentId",
        childrenKey: "children",
        rootParentValues: [0, null],
      });

      menuTree.value = tree;

      menuCache.set(slug, { menu: response.data, tree });
    } catch (err) {
      error.value = err instanceof Error ? err.message : "Unknown error";
      menu.value = null;
      menuTree.value = [];
    } finally {
      loading.value = false;
    }
  };

  /**
   * Lấy menu items theo level
   */
  const getMenuByLevel = (level: number): TreeNode<IMenuItem>[] => {
    const collectByLevel = (nodes: TreeNode<IMenuItem>[], targetLevel: number): TreeNode<IMenuItem>[] => {
      const result: TreeNode<IMenuItem>[] = [];

      for (const node of nodes) {
        if (node.level === targetLevel) {
          result.push(node);
        }
        if (node.children && node.children.length > 0) {
          result.push(...collectByLevel(node.children, targetLevel));
        }
      }

      return result;
    };

    return collectByLevel(menuTree.value, level);
  };

  /**
   * Lấy children của một menu item
   */
  const getChildren = (parentId: number): TreeNode<IMenuItem>[] => {
    const findNode = (nodes: TreeNode<IMenuItem>[], id: number): TreeNode<IMenuItem> | null => {
      for (const node of nodes) {
        if (node.id === id) {
          return node;
        }
        if (node.children && node.children.length > 0) {
          const found = findNode(node.children, id);
          if (found) return found;
        }
      }
      return null;
    };

    const node = findNode(menuTree.value, parentId);
    return node?.children || [];
  };

  /**
   * Check xem menu item có children không
   */
  const hasChildren = (item: IMenuItem | TreeNode<IMenuItem>): boolean => {
    return !!(item.children && item.children.length > 0);
  };

  /**
   * Giới hạn children hiển thị (tối đa 3 cấp)
   */
  const getChildrenWithLimit = (item: TreeNode<IMenuItem>, maxLevel: number = 3): TreeNode<IMenuItem>[] => {
    if (item.level && item.level >= maxLevel) {
      return [];
    }
    return item.children || [];
  };

  const clearCache = () => {
    menuCache.clear();
    menu.value = null;
    menuTree.value = [];
  };

  return {
    // State
    menu: computed(() => menu.value),
    menuTree: computed(() => menuTree.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),

    // Methods
    loadMenuBySlug,
    getMenuByLevel,
    getChildren,
    hasChildren,
    getChildrenWithLimit,
    clearCache,
  };
};
