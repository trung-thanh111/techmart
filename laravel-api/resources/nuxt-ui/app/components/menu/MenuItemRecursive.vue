<template>
    <li class="border rounded p-2 mb-2" :data-id="item.__localId">
        <div class="d-flex items-center justify-content-between mb-3">
            <div class="d-flex gap-2 align-items-center">
                <i class="drag-handle ri-drag-move-line cursor-move"></i>
                <strong>{{ item.name }}</strong>
                <small class="text-muted">({{ item.slug }})</small>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveUp" :disabled="isFirst">
                    <i class="ri-arrow-up-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="moveDown" :disabled="isLast">
                    <i class="ri-arrow-down-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="addChildItem">
                    <i class="ri-add-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" @click="removeThis">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>

        <div class="flex gap-3 items-center w-full">
            <div class="flex-1">
                <label class="form-label">
                    Tên
                    <span class="text-danger">*</span>
                </label>
                <InputText :model-value="item.name" @update:modelValue="handleNameChange" class="form-control" />
            </div>
            <div class="flex-1">
                <label class="form-label">
                    Slug
                    <span class="text-danger">*</span>
                </label>
                <InputText :model-value="item.slug" @update:modelValue="handleSlugChange" class="form-control" />
            </div>
            <div class="w-28">
                <label class="form-label">Trạng thái</label>
                <div class="mt-1">
                    <ToggleSwitch :modelValue="item.publish === 2" @update:modelValue="handlePublishChange" class="scale-75" />
                </div>
            </div>
        </div>

        <div v-if="item.children && item.children.length > 0" class="mt-3">
            <ul class="nested-list list-unstyled ps-0" :data-parent-id="item.__localId">
                <MenuItemRecursive v-for="(child, cidx) in item.children" :key="child.__localId" :item="child" :index="cidx as any" :parent="item" :is-first="cidx === 0" :is-last="cidx === item.children.length - 1" @update="$emit('update')" />
            </ul>
        </div>
    </li>
</template>

<script setup lang="ts">
import { slugify } from "~/utils/helpers";

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

const emit = defineEmits(["update"]);

const handleNameChange = (value: any) => {
    props.item.name = value;

    if (!props.item.__userModifiedSlug) {
        props.item.slug = slugify(value || "");
    }

    emit("update");
};

const handleSlugChange = (value: any) => {
    props.item.slug = value;
    props.item.__userModifiedSlug = true;
    emit("update");
};

const handlePublishChange = (val: boolean) => {
    props.item.publish = val ? 2 : 1;
    emit("update");
};

const moveUp = () => {
    if (!props.parent || props.isFirst) return;

    const siblings = props.parent.children;
    [siblings[props.index - 1], siblings[props.index]] = [siblings[props.index], siblings[props.index - 1]];

    updateSiblingsOrder(siblings);
    emit("update");
};

const moveDown = () => {
    if (!props.parent || props.isLast) return;

    const siblings = props.parent.children;
    [siblings[props.index], siblings[props.index + 1]] = [siblings[props.index + 1], siblings[props.index]];

    updateSiblingsOrder(siblings);
    emit("update");
};

const updateSiblingsOrder = (siblings: any[]) => {
    siblings.forEach((sibling, idx) => {
        sibling.order = idx + 1;
    });
};

const removeThis = () => {
    if (!props.parent) return;

    if (props.item.children?.length > 0) {
        if (!confirm(`Xóa "${props.item.name}" sẽ xóa ${props.item.children.length} menu con. Bạn có chắc?`)) {
            return;
        }
    }

    props.parent.children.splice(props.index, 1);
    updateSiblingsOrder(props.parent.children);
    emit("update");
};

const addChildItem = () => {
    if (!Array.isArray(props.item.children)) {
        props.item.children = [];
    }

    const localId = `n-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

    props.item.children.push({
        id: undefined,
        __localId: localId,
        name: "Menu con",
        slug: "menu-con",
        publish: 2,
        order: props.item.children.length + 1,
        children: [],
    });

    emit("update");
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
