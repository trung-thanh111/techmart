// Utilities to transform flat nested-set data <-> tree and map to UI components
// These functions are generic, fast (O(n)), and reusable across modules.

export interface NestedSetNodeBase {
    id: number | string;
    parentId: number | string | null;
    lft?: number;
    rgt?: number;
    level?: number;
    [key: string]: any;
}

export type TreeNode<T extends NestedSetNodeBase = NestedSetNodeBase> = T & {
    children?: Array<TreeNode<T>>;
};

export type BuildTreeOptions<T extends NestedSetNodeBase> = {
    idKey?: keyof T;
    parentKey?: keyof T;
    childrenKey?: string;
    rootParentValues?: Array<any>;
};

export type PrimeMapOptions<T extends NestedSetNodeBase> = {
    labelKey?: keyof T;
    valueKey?: keyof T;
    childrenKey?: string;
    selectable?: (node: TreeNode<T>) => boolean;
    disabled?: (node: TreeNode<T>) => boolean;
    icon?: (node: TreeNode<T>) => string | undefined;
};

/**
 * Build a tree from a flat list using parentId (O(n)).
 * - Keeps original item references (shallow) and attaches `children` arrays.
 * - Accepts custom id/parent keys via options to support heterogeneous payloads.
 */
export function buildTree<T extends NestedSetNodeBase>(items: T[], options?: BuildTreeOptions<T>): Array<TreeNode<T>> {
    const idKey = (options?.idKey as string) || "id";
    const parentKey = (options?.parentKey as string) || "parentId";
    const childrenKey = options?.childrenKey || "children";
    const rootParentValues = options?.rootParentValues ?? [0, null, undefined];

    const nodeById = new Map<any, TreeNode<T>>();
    const roots: Array<TreeNode<T>> = [];

    // Create shallow clones and register
    for (const item of items) {
        const clone = { ...(item as any), [childrenKey]: [] } as TreeNode<T> & Record<string, any>;
        nodeById.set((clone as any)[idKey], clone);
    }

    // Link children
    for (const item of items) {
        const id = (item as any)[idKey];
        const parentId = (item as any)[parentKey];
        const node = nodeById.get(id)!;
        const isRoot = rootParentValues.includes(parentId);
        if (isRoot) {
            roots.push(node);
            continue;
        }
        const parent = nodeById.get(parentId);
        if (parent) (parent as any)[childrenKey].push(node);
        else roots.push(node);
    }

    return roots;
}

/**
 * Flatten a tree back to a list with depth-first order.
 */
export function flattenTree<T extends NestedSetNodeBase>(roots: Array<TreeNode<T>>, options?: { childrenKey?: string }): T[] {
    const childrenKey = options?.childrenKey || "children";
    const out: T[] = [];
    const stack: Array<TreeNode<T>> = [...roots];
    while (stack.length) {
        const node = stack.pop()!;
        const children = ((node as any)[childrenKey] as Array<TreeNode<T>>) ?? [];
        const { [childrenKey]: _omit, ...rest } = node as any;
        out.push(rest as T);
        for (let i = children.length - 1; i >= 0; i--) {
            const child = children[i];
            if (child) stack.push(child);
        }
    }
    return out;
}

/**
 * Map tree nodes to PrimeVue Tree/TreeSelect node format.
 * - labelKey/valueKey configurable
 * - Supports disabled, selectable, icons via resolver callbacks
 */
export function toPrimeVueTreeNodes<T extends NestedSetNodeBase>(roots: Array<TreeNode<T>>, options?: PrimeMapOptions<T>) {
    const labelKey = (options?.labelKey as string) || "name";
    const valueKey = (options?.valueKey as string) || "id";
    const childrenKey = options?.childrenKey || "children";

    const mapNode = (node: TreeNode<T>): any => {
        const pvNode: any = {
            key: String((node as any)[valueKey]),
            label: (node as any)[labelKey],
            data: node,
        };
        const children = (node as any)[childrenKey] as Array<TreeNode<T>> | undefined;
        if (Array.isArray(children) && children.length > 0) pvNode.children = (children as Array<TreeNode<T>>).map((c) => mapNode(c as TreeNode<T>));
        if (options?.selectable) pvNode.selectable = options.selectable(node);
        if (options?.disabled) pvNode.disabled = options.disabled(node);
        const icon = options?.icon?.(node);
        if (icon) pvNode.icon = icon;
        return pvNode;
    };

    return roots.map(mapNode);
}

/**
 * Convenience: from flat -> PrimeVue nodes directly.
 */
export function flatToPrimeVueTreeNodes<T extends NestedSetNodeBase>(items: T[], options?: BuildTreeOptions<T> & Omit<PrimeMapOptions<T>, "childrenKey"> & { childrenKey?: string }) {
    const childrenKey = options?.childrenKey || "children";
    const tree = buildTree(items, {
        idKey: options?.idKey as any,
        parentKey: options?.parentKey as any,
        childrenKey,
        rootParentValues: options?.rootParentValues,
    });
    return toPrimeVueTreeNodes(tree, {
        labelKey: options?.labelKey as any,
        valueKey: options?.valueKey as any,
        childrenKey,
        selectable: options?.selectable,
        disabled: options?.disabled,
        icon: options?.icon,
    });
}
