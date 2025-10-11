<script setup lang="ts">
import type { BasePayloadFilter } from '~/interfaces/base.interface';

interface Props {
  initialFilters?: Partial<BasePayloadFilter>;
}

const props = withDefaults(defineProps<Props>(), {
  initialFilters: () => ({
    keyword: "",
    perpage: 10,
    publish: null,
  }),
});

const emit = defineEmits<{
  search: [filters: BasePayloadFilter];
}>();

// Local state
const keyword = ref(props.initialFilters.keyword || "");
const perpage = ref<number>(Number(props.initialFilters.perpage) || 10);
const publish = ref<number | null>(typeof props.initialFilters.publish === "number" ? props.initialFilters.publish : null);

const { t } = useI18n();

const perpageOptions = computed(() => [
  { name: `10 ${t("admin.components.filterBar.records")}`, value: 10 },
  { name: `20 ${t("admin.components.filterBar.records")}`, value: 20 },
  { name: `50 ${t("admin.components.filterBar.records")}`, value: 50 },
  { name: `100 ${t("admin.components.filterBar.records")}`, value: 100 },
  { name: `200 ${t("admin.components.filterBar.records")}`, value: 200 },
  { name: `500 ${t("admin.components.filterBar.records")}`, value: 500 },
  { name: `1000 ${t("admin.components.filterBar.records")}`, value: 1000 },
]);

const publishOptions = computed(() => [
  { name: t("admin.common.published"), value: 2 },
  { name: t("admin.common.draft"), value: 1 },
  { name: t("admin.common.hidden"), value: 0 },
]);

const handleSearch = () => {
  emit("search", {
    keyword: keyword.value,
    perpage: perpage.value,
    publish: publish.value,
  });
};
</script>

<template>
  <div class="my-4">
    <form @submit.prevent="handleSearch" class="flex flex-nowrap justify-end gap-3">
      <div class="min-w-[200px]">
        <IconField>
          <InputIcon class="fa-solid fa-magnifying-glass" role="button" tabindex="0" @click.prevent="handleSearch" @keydown.enter.prevent="handleSearch" />
          <InputText v-model="keyword" :placeholder="$t('admin.components.filterBar.searchPlaceholder')" class="text-sm w-full" />
        </IconField>
      </div>

      <div class="min-w-[200px]">
        <Select v-model="perpage" :options="perpageOptions" option-label="name" option-value="value" filter :placeholder="$t('admin.components.filterBar.selectRecords')" class="w-full text-sm" />
      </div>

      <div class="min-w-[200px]">
        <Select v-model="publish" :options="publishOptions" option-label="name" option-value="value" showClear :placeholder="$t('admin.components.filterBar.selectStatus')" class="w-full text-sm" />
      </div>

      <slot />
    </form>
  </div>
</template>
