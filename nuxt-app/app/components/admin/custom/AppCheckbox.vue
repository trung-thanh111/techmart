<script setup lang="ts">
interface Props {
  modelValue?: boolean | number[];
  value?: number;
  isSelectAll?: boolean;
  disabled?: boolean;
  class?: string;
  size?: "small" | "medium" | "large";
}

interface Emits {
  (e: "update:modelValue", value: boolean | number[]): void;
  (e: "change", value: boolean | number[]): void;
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
    return props.modelValue.includes(props.value!);
  }
  return Boolean(props.modelValue);
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
        if (!currentIds.includes(props.value!)) {
          currentIds.push(props.value!);
        }
      } else {
        const index = currentIds.indexOf(props.value!);
        if (index > -1) {
          currentIds.splice(index, 1);
        }
      }
      emit("update:modelValue", currentIds);
      emit("change", currentIds);
    } else {
      emit("update:modelValue", value);
      emit("change", value);
    }
  }
};
</script>

<template>
  <div class="checkbox-wrapper" :class="class">
    <Checkbox :model-value="isChecked" :disabled="disabled" @update:model-value="handleChange" :class="size === 'large' ? 'scale-100' : size === 'small' ? 'scale-50' : 'scale-75'" binary />
  </div>
</template>
