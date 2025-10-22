<script setup lang="ts">
interface Option {
  name: string;
  value: number;
}

const props = defineProps<{
  modelValue: number | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: number): void;
}>();

const { t } = useI18n();

const options = computed<Option[]>(() => [
  { name: t("admin.common.published"), value: 2 },
  { name: t("admin.common.draft"), value: 1 },
  { name: t("admin.common.hidden"), value: 0 },
]);
</script>

<template>
  <Select :options="options" optionLabel="name" optionValue="value" :modelValue="props.modelValue" @update:modelValue="emit('update:modelValue', $event)" :placeholder="$t('admin.components.filterBar.selectStatus')" checkmark class="rounded py-[1px] w-100 md:w-56" size="small" />
</template>
