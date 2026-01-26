<script setup lang="ts">
import Paginator from "primevue/paginator";
import type { IPaginate } from "~/interfaces/base.interface";

const props = defineProps<{
  pagination: IPaginate<any>;
  rowsPerPageOptions?: number[];
}>();

const emit = defineEmits<{
  (e: "update:page", value: number): void;
  (e: "update:rows", value: number): void;
}>();

const { t } = useI18n();

const first = computed(() => (props.pagination.current_page - 1) * props.pagination.per_page);
const from = computed(() => (props.pagination.total === 0 ? 0 : first.value + 1));
const to = computed(() => Math.min(first.value + props.pagination.per_page, props.pagination.total));
</script>

<template>
  <div class="flex items-center justify-between">
    <p class="text-sm text-gray-500">{{ $t('admin.components.pagination.showingResults', { from, to, total: props.pagination.total }) }}</p>
    <Paginator
      :first="first"
      :rows="props.pagination.per_page"
      :totalRecords="props.pagination.total"
      :rowsPerPageOptions="rowsPerPageOptions"
      @page="
        (e) => {
          emit('update:page', e.page + 1);
          emit('update:rows', e.rows);
        }
      "
      class="font-medium"
    />
  </div>
</template>
