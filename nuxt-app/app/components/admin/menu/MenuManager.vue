<template>
  <div>
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
        <div class="d-flex items-center justify-content-between">
          <div class="d-flex gap-2 align-items-center">
            <i class="drag-handle ri-drag-move-line cursor-move"></i>
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

        <div class="mt-3">
          <div class="flex items-center gap-3">
            <div class="col-md-5">
              <label class="form-label">
                Tên
                <span class="text-danger">*</span>
              </label>
              <InputText :model-value="item.name" @update:model-value="(val: any) => handleNameChange(item, val)" class="w-full form-control" placeholder="Nhập tên menu" />
            </div>
            <div class="col-md-4">
              <label class="form-label">
                URL / Slug
                <span class="text-danger">*</span>
              </label>
              <InputText :model-value="item.slug" @update:model-value="(val: any) => handleSlugChange(item, val)" class="form-control" />
            </div>
            <div class="col-md-2">
              <label class="form-label">Trạng thái</label>
              <div class="mt-1">
                <ToggleSwitch :modelValue="item.publish === 2" @update:modelValue="(val) => handlePublishToggle(item, val)" class="scale-75" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="item.children && item.children.length > 0" class="mt-3">
          <ul class="nested-list list-unstyled ps-0" :data-parent-id="item.__localId">
            <MenuItemRecursive v-for="(child, cidx) in item.children" :key="child.__localId" :item="child" :index="cidx as any" :parent="item" :is-first="cidx === 0" :is-last="cidx === item.children.length - 1" @update="immediateEmitChange" />
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
import { slugify } from "~/utils/helpers";
import MenuItemRecursive from "./MenuItemRecursive.vue";

const props = defineProps({
  items: {
    type: Array as () => any[],
    default: () => [],
  },
});

const emits = defineEmits(["update:items"]);

// ============================================
// REFS & STATE
// ============================================
const rootListRef = ref<HTMLElement | null>(null);
const formRef = ref<any>(null);
const itemsLocal = ref<any[]>([]);
const isEmitting = ref(false);

const debounce = (func: Function, delay: number) => {
  let timeoutId: number;
  return (...args: any[]) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func(...args), delay);
  };
};

const initializeItems = (items: any[]): any[] => {
  if (!Array.isArray(items)) return [];

  return items.map((it, i) => ({
    ...it,
    __localId: it.__localId || it.id || `n-${Date.now()}-${i}-${Math.random().toString(36).substr(2, 9)}`,
    children: Array.isArray(it.children) ? initializeItems(it.children) : [],
  }));
};

watch(
  () => props.items,
  (v) => {
    if (isEmitting.value) return;
    itemsLocal.value = initializeItems(v);
  },
  { deep: true, immediate: true }
);

const emitChange = () => {
  if (isEmitting.value) return;

  isEmitting.value = true;
  const cleaned = cleanNestedData(toRaw(itemsLocal.value));
  emits("update:items", cleaned);

  setTimeout(() => {
    isEmitting.value = false;
  }, 50);
};

// Immediate emit - không debounce
const immediateEmitChange = () => {
  emitChange();
};

const cleanNestedData = (items: any[]): any[] => {
  if (!Array.isArray(items)) return [];

  return items.map((item) => {
    const { __localId, __userModifiedSlug, ...rest } = item;

    return {
      ...rest,
      children: Array.isArray(item.children) && item.children.length > 0 ? cleanNestedData(item.children) : [],
    };
  });
};

const handleNameChange = (item: any, value: string) => {
  item.name = value;

  // Auto-generate slug
  if (!item.__userModifiedSlug) {
    item.slug = slugify(value || "");
  }

  // Emit ngay lập tức để không bị out focus
  immediateEmitChange();
};

const handleSlugChange = (item: any, value: string) => {
  item.slug = value;
  item.__userModifiedSlug = true;
  immediateEmitChange();
};

const handlePublishToggle = (item: any, val: boolean) => {
  item.publish = val ? 2 : 1;
  immediateEmitChange();
};

const formSchema = toTypedSchema(
  z.object({
    name: z.string({ required_error: "admin.validation.nameRequired" }).min(1, "admin.validation.nameRequired"),
    slug: z.string().max(255).optional(),
    publish: z.boolean().optional(),
  })
);

const onAddItem = (values: any, actions: any) => {
  const publishNum = values.publish === undefined ? 2 : values.publish ? 2 : 1;
  const localId = `n-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

  const newItem = {
    id: undefined,
    __localId: localId,
    name: values.name,
    slug: values.slug || slugify(values.name),
    publish: publishNum,
    order: itemsLocal.value.length + 1,
    children: [],
  };

  itemsLocal.value.push(newItem);

  immediateEmitChange();
  actions.resetForm({ values: { name: "", slug: "", publish: true } });
};

const removeItem = (idx: number) => {
  const item = itemsLocal.value[idx];
  if (!item) return;

  if (item.children?.length > 0) {
    if (!confirm(`Xóa "${item.name}" sẽ xóa ${item.children.length} menu con. Bạn có chắc?`)) {
      return;
    }
  }

  itemsLocal.value.splice(idx, 1);
  updateOrder();
  immediateEmitChange();
  nextTick(() => initSortables());
};

const moveUp = (idx: number) => {
  if (idx === 0) return;
  [itemsLocal.value[idx - 1], itemsLocal.value[idx]] = [itemsLocal.value[idx], itemsLocal.value[idx - 1]];
  updateOrder();
  immediateEmitChange();
  nextTick(() => initSortables());
};

const moveDown = (idx: number) => {
  if (idx === itemsLocal.value.length - 1) return;
  [itemsLocal.value[idx], itemsLocal.value[idx + 1]] = [itemsLocal.value[idx + 1], itemsLocal.value[idx]];
  updateOrder();
  immediateEmitChange();
  nextTick(() => initSortables());
};

const updateOrder = () => {
  itemsLocal.value.forEach((item, index) => {
    item.order = index + 1;
  });
};

const addChild = (idx: number) => {
  const parent = itemsLocal.value[idx];
  if (!parent) return;

  if (!Array.isArray(parent.children)) {
    parent.children = [];
  }

  const localId = `n-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

  parent.children.push({
    id: undefined,
    __localId: localId,
    name: "Menu con",
    slug: "menu-con",
    publish: 2,
    order: parent.children.length + 1,
    children: [],
  });

  // Force reactivity
  itemsLocal.value = [...itemsLocal.value];
  immediateEmitChange();
  nextTick(() => initSortables());
};

let sortableInstances: any[] = [];

const initSortables = () => {
  sortableInstances.forEach((s) => s?.destroy?.());
  sortableInstances = [];

  const Sortable = (window as any).Sortable;
  if (!Sortable) return;

  if (rootListRef.value) {
    sortableInstances.push(
      Sortable.create(rootListRef.value, {
        group: "menu",
        animation: 150,
        handle: ".drag-handle",
        onEnd: () => rebuildFromDom(),
      })
    );
  }

  document.querySelectorAll(".nested-list").forEach((el) => {
    sortableInstances.push(
      Sortable.create(el as any, {
        group: "menu",
        animation: 150,
        handle: ".drag-handle",
        onEnd: () => rebuildFromDom(),
      })
    );
  });
};

const flattenMap = (arr: any[], map: Record<string, any> = {}) => {
  for (const it of arr) {
    map[it.__localId] = it;
    if (Array.isArray(it.children) && it.children.length) {
      flattenMap(it.children, map);
    }
  }
  return map;
};

const readListFromDom = (ul: Element, map: Record<string, any>): any[] => {
  const res: any[] = [];

  ul.querySelectorAll(":scope > li").forEach((li) => {
    const id = (li as HTMLElement).dataset.id;
    if (!id || !map[id]) return;

    const copy = { ...map[id] };
    const nested = li.querySelector(":scope > div > .nested-list, :scope > .nested-list");

    copy.children = nested ? readListFromDom(nested, map) : Array.isArray(copy.children) ? [...copy.children] : [];

    res.push(copy);
  });

  return res;
};

const rebuildFromDom = () => {
  const map = flattenMap(itemsLocal.value, {});
  if (!rootListRef.value) return;

  itemsLocal.value = readListFromDom(rootListRef.value, map);
  updateOrderRecursive(itemsLocal.value);
  emitChange();
  nextTick(() => initSortables());
};

const updateOrderRecursive = (items: any[], startOrder: number = 0) => {
  items.forEach((item, index) => {
    item.order = startOrder + index + 1;

    if (Array.isArray(item.children) && item.children.length > 0) {
      updateOrderRecursive(item.children, 0);
    }
  });
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

.nested-list {
  margin-left: 2rem;
  border-left: 2px solid #e5e7eb;
  padding-left: 1rem;
  margin-top: 0.5rem;
}

.nested-list .nested-list {
  margin-left: 1.5rem;
}

.nested-list:hover {
  border-left-color: #3b82f6;
}
</style>
