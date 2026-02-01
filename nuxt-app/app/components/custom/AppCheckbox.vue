<script setup lang="ts">
interface Props {
  modelValue?: boolean | number[] | string[] | string | number | null;
  value?: number | string | boolean;
  isSelectAll?: boolean;
  disabled?: boolean;
  class?: string;
  size?: "small" | "medium" | "large";
}

interface Emits {
  (e: "update:modelValue", value: any): void;
  (e: "change", value: any): void;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  disabled: false,
  class: "",
  size: "medium",
});

const emit = defineEmits<Emits>();

const isChecked = computed(() => {
  if (props.isSelectAll) {
    const selectedIds = Array.isArray(props.modelValue) ? props.modelValue : [];
    const totalItems = getTotalItems();
    return totalItems > 0 && selectedIds.length === totalItems;
  }
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value as never);
  }
  if (typeof props.modelValue === "boolean") {
    return props.modelValue;
  }
  // Radio-like behavior check
  return props.modelValue === props.value;
});

const getTotalItems = inject<() => number>("getTotalItems", () => 0);
const getAllItemIds = inject<() => number[]>("getAllItemIds", () => []);

const handleChange = (value: boolean) => {
  if (props.isSelectAll) {
    const newSelectedIds = value ? getAllItemIds() : [];
    emit("update:modelValue", newSelectedIds);
    emit("change", newSelectedIds);
  } else {
    if (Array.isArray(props.modelValue)) {
      const currentIds = [...props.modelValue];
      if (value) {
        if (!currentIds.includes(props.value as never)) {
          currentIds.push(props.value as never);
        }
      } else {
        const index = currentIds.indexOf(props.value as never);
        if (index > -1) {
          currentIds.splice(index, 1);
        }
      }
      emit("update:modelValue", currentIds);
      emit("change", currentIds);
    } else if (typeof props.modelValue === "boolean") {
      emit("update:modelValue", value);
      emit("change", value);
    } else {
      // Radio-like / Single value selection behavior
      if (value) {
        emit("update:modelValue", props.value);
        emit("change", props.value);
      }
    }
  }
};
</script>

<template>
  <div class="checkbox-wrapper" :class="class">
    <Checkbox :model-value="isChecked" :disabled="disabled" @update:model-value="handleChange" :class="size === 'large' ? 'scale-100' : size === 'small' ? 'scale-50' : 'scale-75'" binary />
  </div>
</template>
