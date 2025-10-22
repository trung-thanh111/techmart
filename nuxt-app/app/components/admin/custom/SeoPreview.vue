<template>
  <div v-if="showPreview" class="seo-preview bg-gray-900 text-white p-4 rounded-md space-y-1 my-1">
    <div class="text-xs text-green-400 truncate">
      {{ url }}
    </div>
    <div class="text-xl text-sky-500 hover:underline truncate">
      {{ title }}
    </div>
    <div class="text-sm text-gray-300 line-clamp-2">
      {{ description }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { slugify } from "~/utils/helpers";

interface Props {
  metaTitle?: string | null;
  metaDescription?: string | null;
  metaKeyword?: string | null;
}
const config = useRuntimeConfig();
const props = defineProps<Props>();
const title = computed(() => props.metaTitle?.trim() || "");
const slug = computed(() => (title.value ? slugify(title.value) : ""));
const url = computed(() => {
  return slug.value ? `${config.public.baseUrl}/${slug.value}` : config.public.baseUrl;
});
const description = computed(() => props.metaDescription?.trim() || "");
const showPreview = computed(() => {
  return !!(props.metaTitle || props.metaDescription || props.metaKeyword);
});
</script>

<style scoped>
.seo-preview {
  font-family: Arial, sans-serif;
}
</style>
