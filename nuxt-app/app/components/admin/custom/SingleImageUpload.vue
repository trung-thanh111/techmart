<template>
  <div>
    <div v-if="!defaultImage" class="text-center">
      <div class="w-100 d-flex flex-column align-items-center justify-content-center text-center rounded border border-dashed p-4" style="min-height: 160px; background: #fff; color: #6c757d; cursor: pointer" @click="trigger">
        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: rgba(0, 0, 0, 0.05)">
          <i class="ri-upload-cloud-2-line" :class="'text-success'" style="font-size: 20px"></i>
        </div>
        <div class="mt-2 fw-semibold text-dark">
          {{ $t("admin.common.clickToUpload") }}
        </div>
        <div class="mt-1 small text-muted">{{ extsLabel }} (max. {{ maxSizeLabel }})</div>
        <input ref="inputRef" type="file" class="d-none" :accept="accept" @change="onChange" />
      </div>
    </div>
    <div v-else class="text-center position-relative d-inline-block">
      <button type="button" class="btn btn-sm btn-link text-danger position-absolute" style="top: -8px; right: -8px" @click.stop="clearImage" aria-label="clear">
        <i class="ri-close-circle-fill" style="font-size: 20px"></i>
      </button>
      <NuxtImg :src="defaultImage" class="rounded material-shadow d-block mx-auto cursor-pointer" width="260" height="120" alt="preview" loading="lazy" @click="trigger" />
      <input ref="inputRef" type="file" class="d-none" :accept="accept" @change="onChange" />
      <div class="mt-1 small text-muted">{{ extsLabel }} (max. {{ maxSizeLabel }})</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useImageUpload } from "~/composables/useImageUpload";
import { MAX_FILE_SIZE } from "~/utils/constants";

const props = defineProps<{
  initialUrl?: string | null;
  note?: string;
  accept?: string;
}>();
const emit = defineEmits<{ (e: "selected", file: File | null): void }>();

const { previewUrl, file, setInitialPreview, onChange } = useImageUpload();
const inputRef = ref<HTMLInputElement | null>(null);
const defaultImage = computed(() => previewUrl.value || props.initialUrl || null);
const accept = computed(() => props.accept || "image/jpeg, image/png, image/webp, image/jpg");
const extsLabel = computed(() => {
  const m = (accept.value || "")
    .replace(/image\//g, "")
    .toUpperCase()
    .split(",")
    .map((s) => s.trim().split(";")[0]);
  return m.length ? m.join(", ") : "SVG, PNG, JPG, GIF";
});
const maxSizeLabel = computed(() => `${Math.round(MAX_FILE_SIZE / (1024 * 1024))}MB`);

const trigger = () => inputRef.value?.click();

watch(file, (f) => {
  if (f) emit("selected", f);
});

onMounted(() => setInitialPreview(props.initialUrl || null));

const clearImage = () => {
  setInitialPreview(null);
  (inputRef.value as HTMLInputElement | null)?.value && ((inputRef.value as HTMLInputElement).value = "");
  emit("selected", null);
};
</script>
