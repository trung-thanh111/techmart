<template>
  <li class="border rounded p-2 mb-2" :data-id="item.__localId">
    <div class="d-flex items-center justify-content-between mb-3">
      <div class="d-flex gap-2 align-items-center">
        <i class="drag-handle ri-drag-move-line cursor-move"></i>
        <i :class="item.icon || 'ri-layout-grid-line'" class="text-muted"></i>
        <strong>{{ item.name }}</strong>
        <small class="text-muted">({{ item.slug }})</small>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveUp" :disabled="isFirst" title="Di chuyển lên">
          <i class="ri-arrow-up-line"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveDown" :disabled="isLast" title="Di chuyển xuống">
          <i class="ri-arrow-down-line"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addChildItem" title="Thêm menu con">
          <i class="ri-add-line"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeThis" title="Xóa menu">
          <i class="ri-delete-bin-line"></i>
        </button>
      </div>
    </div>

    <div class="flex gap-3 items-center w-full">
      <div class="flex-1">
        <label class="form-label">Tên</label>
        <input v-model="item.name" class="form-control" placeholder="Nhập tên Menu" />
      </div>
      <div class="flex-1">
        <label class="form-label">Slug</label>
        <input v-model="item.slug" class="form-control" placeholder="URL / slug menu" />
      </div>
      <div class="w-28">
        <label class="form-label">Trạng thái</label>
        <div class="mt-1">
          <ToggleSwitch :modelValue="item.publish === 2" @update:modelValue="(val) => (item.publish = val ? 2 : 1)" class="scale-75" v.tooltip="trạng thái" />
        </div>
      </div>
    </div>
    <!-- recursive  -->
    <div v-if="item.children && item.children.length > 0" class="mt-3 ms-4">
      <ul class="nested-list list-unstyled ps-0" :data-parent-id="item.__localId">
        <MenuItemRecursive v-for="(child, cidx) in item.children" :key="child.__localId" :item="child" :index="cidx as any" :parent="item" :is-first="cidx === 0" :is-last="cidx === item.children.length - 1" @refresh="$emit('refresh')" />
      </ul>
    </div>
  </li>
</template>

<script setup lang="ts">
import { nextTick } from "vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  parent: {
    type: Object,
    default: null,
  },
  index: {
    type: Number,
    required: true,
  },
  isFirst: {
    type: Boolean,
    default: false,
  },
  isLast: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["refresh"]);

/**
 * Di chuyển menu lên trên
 */
const moveUp = () => {
  if (!props.parent || props.isFirst) return;
  const siblings = props.parent.children;
  const tmp = siblings[props.index - 1];
  siblings[props.index - 1] = siblings[props.index];
  siblings[props.index] = tmp;
  console.log(`⬆️ Di chuyển "${props.item.name}" lên`);
  emit("refresh");
};

/**
 * Di chuyển menu xuống dưới
 */
const moveDown = () => {
  if (!props.parent || props.isLast) return;
  const siblings = props.parent.children;
  const tmp = siblings[props.index + 1];
  siblings[props.index + 1] = siblings[props.index];
  siblings[props.index] = tmp;
  console.log(`⬇️ Di chuyển "${props.item.name}" xuống`);
  emit("refresh");
};

/**
 * Xóa menu này
 */
const removeThis = () => {
  if (!props.parent) return;
  const itemName = props.item.name;

  // Confirm trước khi xóa nếu có menu con
  if (props.item.children && props.item.children.length > 0) {
    if (!confirm(`Xóa "${itemName}" sẽ xóa tất cả ${props.item.children.length} menu con. Bạn có chắc?`)) {
      return;
    }
  }

  props.parent.children.splice(props.index, 1);
  console.log(`🗑️ Đã xóa menu: ${itemName}`);
  emit("refresh");
};

/**
 * Thêm menu con vào menu này
 */
const addChildItem = () => {
  // Đảm bảo children là array
  if (!Array.isArray(props.item.children)) {
    props.item.children = [];
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
  props.item.children.push(newChild);

  // Emit refresh để cập nhật UI
  nextTick(() => {
    emit("refresh");
  });
};
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

.btn-icon {
  width: 38px;
  height: 38px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.row.g-2 {
  --bs-gutter-x: 0.5rem;
  --bs-gutter-y: 0.5rem;
}

.form-label {
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
  font-weight: 500;
}
</style>
