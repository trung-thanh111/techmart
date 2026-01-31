<template>
  <div>
    <div v-if="!defaultImage" class="text-center">
      <div class="w-full flex flex-col items-center justify-center text-center rounded border border-dashed p-4 min-h-[160px] bg-white text-gray-500 cursor-pointer" @click="trigger">
        <div class="flex items-center justify-center rounded-full w-10 h-10 bg-black/5">
          <i class="fa-solid fa-cloud-arrow-up text-green-600 text-xl"></i>
        </div>
        <div class="mt-2 font-semibold text-gray-800">
          {{ $t("common.clickToUpload") }}
        </div>
        <div class="mt-1 text-xs text-gray-400">{{ extsLabel }} ({{ $t("common.maxSize", { size: maxSizeLabel }) }})</div>
        <input ref="inputRef" type="file" class="hidden" :accept="accept" @change="onChange" />
      </div>
    </div>
    <div v-else class="text-center relative inline-block">
      <button type="button" class="absolute -top-2 -right-1 text-red-500 hover:text-red-600" @click.stop="clearImage" aria-label="clear">
        <i class="fa-solid fa-circle-xmark text-xl"></i>
      </button>
      <NuxtImg :src="defaultImage" class="rounded shadow-md block mx-auto cursor-pointer" width="260" height="120" alt="preview" loading="lazy" @click="trigger" />
      <input ref="inputRef" type="file" class="hidden" :accept="accept" @change="onChange" />
      <div class="mt-1 text-xs text-gray-400">{{ extsLabel }} ({{ $t("common.maxSize", { size: maxSizeLabel }) }})</div>
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
