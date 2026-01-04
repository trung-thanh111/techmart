<template>
  <div>
    <!-- Form thêm menu mới -->
    <VeeForm :validation-schema="formSchema" @submit="onAddItem" class="w-full" ref="formRef">
      <div class="flex gap-3 items-center w-full">
        <div class="flex-1">
          <label class="form-label block">
            {{ $t("admin.common.form.name") }}
          </label>
          <div class="mt-1">
            <Field name="name" v-slot="{ field, errorMessage, meta }">
              <InputText v-bind="field" class="form-control" placeholder="Nhập tên Menu" />
              <small class="block h-4 text-danger">
                <span v-if="errorMessage && meta.touched">
                  {{ $t(errorMessage) }}
                </span>
              </small>
            </Field>
          </div>
        </div>

        <div class="flex-1">
          <label class="form-label block">
            {{ $t("admin.common.form.slug") }}
          </label>
          <div class="mt-1">
            <Field name="slug" v-slot="{ field, errorMessage, meta }">
              <InputText v-bind="field" class="form-control" placeholder="URL / slug menu" />
              <small class="block h-4 text-danger">
                <span v-if="errorMessage && meta.touched">
                  {{ $t(errorMessage) }}
                </span>
              </small>
            </Field>
          </div>
        </div>

        <div class="w-28">
          <label class="form-label block mb-1">
            {{ $t("admin.common.status") }}
          </label>
          <div class="mt-1 flex items-center h-[38px]">
            <Field name="publish" v-slot="{ field }">
              <ToggleSwitch :modelValue="field.value ?? true" @update:modelValue="field.onChange" class="scale-75" />
            </Field>
          </div>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-outline-success btn-icon waves-effect waves-light material-shadow-none">
            <i class="ri-add-line"></i>
          </button>
        </div>
      </div>
    </VeeForm>

    <!-- Danh sách menu -->
    <ul class="list-unstyled root-list mt-3" ref="rootListRef">
      <li v-for="(item, idx) in itemsLocal" :key="item.__localId" class="border rounded p-2 mb-2" :data-id="item.__localId">
        <!-- Header của menu item -->
        <div class="d-flex items-center justify-content-between">
          <div class="d-flex gap-2 align-items-center">
            <i class="drag-handle ri-drag-move-line cursor-move"></i>
            <i :class="item.icon || 'ri-layout-grid-line'" class="text-muted"></i>
            <strong>{{ item.name }}</strong>
            <small class="text-muted">({{ item.slug }})</small>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveUp(idx)" :disabled="idx === 0">
              <i class="ri-arrow-up-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveDown(idx)" :disabled="idx === itemsLocal.length - 1">
              <i class="ri-arrow-down-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="addChild(idx)">
              <i class="ri-add-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeItem(idx)">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        </div>

        <!-- Form chỉnh sửa menu item -->
        <div class="mt-3">
          <div class="flex items-center gap-3">
            <div class="col-md-5">
              <label class="form-label">Tên</label>
              <input v-model="item.name" class="w-full form-control" placeholder="Nhập tên menu" />
            </div>
            <div class="col-md-4">
              <label class="form-label">URL / Slug</label>
              <input v-model="item.slug" class="w-full form-control" placeholder="URL / liên kết" />
            </div>
            <div class="col-md-2">
              <label class="form-label">Trạng thái</label>
              <div class="mt-1">
                <ToggleSwitch :modelValue="item.publish === 2" @update:modelValue="(val) => handlePublishToggle(item, val)" class="scale-75" />
              </div>
            </div>
          </div>
        </div>

        <!-- Menu con - QUAN TRỌNG: v-if kiểm tra children -->
        <div v-if="item.children && item.children.length > 0" class="mt-3 ms-4">
          <ul class="nested-list list-unstyled ps-0" :data-parent-id="item.__localId">
            <MenuItemRecursive v-for="(child, cidx) in item.children" :key="child.__localId" :item="child" :index="cidx as any" :parent="item" :is-first="cidx === 0" :is-last="cidx === item.children.length - 1" @refresh="forceRefresh" />
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { toRaw, nextTick, onMounted, ref, watch } from "vue";
import { Form as VeeForm, Field } from "vee-validate";
import z from "zod";
import { toTypedSchema } from "@vee-validate/zod";
import MenuItemRecursive from "./MenuItemRecursive.vue";

const props = defineProps({
  items: {
    type: Array as () => any[] | undefined,
    default: () => [],
  },
});

const emits = defineEmits(["update:items"]);

// Refs
const rootListRef = ref<HTMLElement | null>(null);
const formRef = ref<any>(null);

// Key để force re-render
const refreshKey = ref(0);

const itemsLocal = ref<any[]>(
  (props.items as any[])?.map((it: any, i: number) => ({
    ...(it || {}),
    __localId: it?.id ?? `n-${i}`,
    children: it?.children || [], // ĐẢM BẢO children luôn là array
  })) ?? []
);

// Watch props
watch(
  () => props.items,
  (v) => {
    itemsLocal.value = ((v as any[])?.map((it: any, i: number) => ({
      ...(it || {}),
      __localId: it?.id ?? `n-${i}`,
      children: it?.children || [],
    })) ?? []) as any[];
  },
  { deep: true }
);

// Watch itemsLocal và emit changes
watch(
  itemsLocal,
  (v) => {
    emits(
      "update:items",
      toRaw(v).map(({ __localId, ...rest }) => rest)
    );
    nextTick(() => initSortables());
  },
  { deep: true }
);

// Schema validation
const formSchema = toTypedSchema(
  z.object({
    name: z.string({ required_error: "admin.validation.nameRequired" }).min(1, "admin.validation.nameRequired"),
    slug: z.string().max(255).optional(),
    publish: z.boolean().optional(),
  })
);

/**
 * Thêm menu mới vào root level
 */
const onAddItem = (values: any, actions: any) => {
  try {
    const publishNum = values.publish === undefined ? 2 : values.publish ? 2 : 1;
    const payload = {
      id: undefined,
      name: values.name,
      slug: values.slug || "",
      publish: publishNum,
      icon: "",
      children: [], // ĐẢM BẢO children là array
      __localId: `n-${Date.now()}`,
    };

    itemsLocal.value.push(payload);

    actions.resetForm({
      values: { name: "", slug: "", publish: true },
    });

    console.log("✅ Đã thêm menu mới:", payload.name);
  } catch (error) {
    console.error("❌ Lỗi khi thêm menu:", error);
  }
};

/**
 * Force refresh component
 */
const forceRefresh = () => {
  refreshKey.value++;
  nextTick(() => {
    initSortables();
  });
};

/**
 * Xóa menu item
 */
const removeItem = (idx: number) => {
  const itemName = itemsLocal.value[idx]?.name;
  itemsLocal.value.splice(idx, 1);
  console.log(`🗑️ Đã xóa menu: ${itemName}`);
  forceRefresh();
};

/**
 * Di chuyển menu lên
 */
const moveUp = (idx: number) => {
  if (idx === 0) return;
  const tmp = itemsLocal.value[idx - 1];
  itemsLocal.value[idx - 1] = itemsLocal.value[idx];
  itemsLocal.value[idx] = tmp;
  forceRefresh();
};

/**
 * Di chuyển menu xuống
 */
const moveDown = (idx: number) => {
  if (idx === itemsLocal.value.length - 1) return;
  const tmp = itemsLocal.value[idx + 1];
  itemsLocal.value[idx + 1] = itemsLocal.value[idx];
  itemsLocal.value[idx] = tmp;
  forceRefresh();
};

/**
 * Toggle trạng thái publish
 */
const handlePublishToggle = (item: any, val: boolean) => {
  item.publish = val ? 2 : 1;
};

/**
 * Thêm menu con - FIX CHÍNH Ở ĐÂY
 */
const addChild = (idx: number) => {
  const parent = itemsLocal.value[idx];
  if (!parent) {
    console.error("❌ Không tìm thấy parent menu");
    return;
  }

  // Đảm bảo children là array
  if (!Array.isArray(parent.children)) {
    parent.children = [];
  }

  // Tạo menu con mới
  const newChild = {
    id: undefined,
    name: "Menu con",
    slug: "",
    icon: "",
    publish: 2,
    children: [],
    __localId: `n-${Date.now()}`,
  };

  // Thêm vào children
  parent.children.push(newChild);

  console.log(`➕ Đã thêm menu con vào: ${parent.name}`, newChild);
  console.log("📊 Children hiện tại:", parent.children);

  // Force update để Vue detect thay đổi
  itemsLocal.value = [...itemsLocal.value];

  // Refresh UI
  nextTick(() => {
    forceRefresh();
  });
};

// ============================================
// DRAG & DROP
// ============================================

let sortableInstances: any[] = [];

const initSortables = () => {
  sortableInstances.forEach((s) => s?.destroy?.());
  sortableInstances = [];

  const Sortable = (window as any).Sortable;
  if (!Sortable) return;

  if (rootListRef.value) {
    const s = Sortable.create(rootListRef.value, {
      group: "menu",
      animation: 150,
      handle: ".drag-handle",
      onEnd: () => rebuildFromDom(),
    });
    sortableInstances.push(s);
  }

  document.querySelectorAll(".nested-list").forEach((el) => {
    const s = Sortable.create(el as any, {
      group: "menu",
      animation: 150,
      handle: ".drag-handle",
      onEnd: () => rebuildFromDom(),
    });
    sortableInstances.push(s);
  });
};

const flattenMap = (arr: any[], map: Record<string, any> = {}) => {
  for (const it of arr) {
    const id = (it as any).__localId;
    map[id] = it;
    if (Array.isArray((it as any).children) && (it as any).children.length) {
      flattenMap((it as any).children, map);
    }
  }
  return map;
};

const readListFromDom = (ul: Element, map: Record<string, any>) => {
  const res: any[] = [];
  ul.querySelectorAll(":scope > li").forEach((li) => {
    const id = (li as HTMLElement).dataset.id;
    if (!id) return;
    const item = map[id];
    if (!item) return;
    const copy = { ...item };
    const nested = li.querySelector(":scope > .nested-list");
    if (nested) {
      copy.children = readListFromDom(nested, map);
    } else {
      copy.children = Array.isArray(item.children) ? [...item.children] : [];
    }
    res.push(copy);
  });
  return res;
};

const rebuildFromDom = () => {
  const map = flattenMap(itemsLocal.value, {});
  if (!rootListRef.value) return;
  const rebuilt = readListFromDom(rootListRef.value, map);
  itemsLocal.value = rebuilt;
  forceRefresh();
};

onMounted(() => {
  nextTick(() => initSortables());
});
</script>

<style scoped>
.form-control {
  height: 38px;
}

.drag-handle {
  cursor: move;
  opacity: 0.5;
  transition: opacity 0.2s;
}

.drag-handle:hover {
  opacity: 1;
}
</style>
