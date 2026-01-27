<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-2">
            <h6 class="m-0 text-gray-600">{{ $t("admin.common.listAlbum") }}</h6>
            <div class="flex gap-2">
                <button v-if="images.length > 0" type="button" class="px-3 py-1 text-xs border-1 border-red-400 text-red-500 rounded-sm hover:bg-red-100 flex items-center transition-colors" @click="clearAll">
                    <i class="ri-delete-bin-line mr-1"></i>
                    {{ $t("admin.common.clearAll") }}
                </button>
                <button type="button" class="px-3 py-1 text-xs bg-sky-800 text-white rounded-sm hover:bg-sky-900 flex items-center" @click="triggerFileInput">
                    <i class="ri-image-add-line mr-1"></i>
                    {{ $t("admin.common.selectImages") }}
                </button>
            </div>
        </div>
        <div v-if="images.length === 0" class="flex flex-col items-center justify-center text-center rounded border-2 border-dashed p-3 bg-white text-gray-500 cursor-pointer transition hover:bg-gray-50 hover:border-sky-800" style="min-height: 200px" @click="triggerFileInput">
            <div class="flex items-center justify-center rounded-full mb-3 w-[60px] h-[60px] bg-black/5">
                <i class="ri-image-line text-gray-400 text-[28px]"></i>
            </div>
            <div class="text-gray-800 mb-2">{{ $t("admin.common.uploadPlaceholder") }}</div>
            <div class="text-sm font-semibold text-gray-500">{{ extsLabel }} (max. {{ maxSizeLabel }} mỗi ảnh)</div>
        </div>
        <div v-else class="grid grid-cols-[repeat(auto-fill,minmax(120px,1fr))] gap-3 md:grid-cols-[repeat(auto-fill,minmax(100px,1fr))] md:gap-2">
            <div v-for="(image, index) in images" :key="image.id" class="relative rounded-lg overflow-hidden shadow-sm" @mouseenter="hoveredIndex = index" @mouseleave="hoveredIndex = null">
                <NuxtImg :src="image.url" class="w-full h-full object-cover aspect-square transition-transform duration-200 hover:scale-105" alt="preview" loading="lazy" />
                <div class="absolute top-0 left-0 m-2">
                    <div class="w-[18px] h-[18px] rounded-full flex items-center justify-center bg-green-600">
                        <i class="ri-check-line text-white text-[12px]"></i>
                    </div>
                </div>
                <div v-if="hoveredIndex === index" class="absolute top-0 right-0 m-2 cursor-pointer" @click.stop="removeImage(index)">
                    <div class="w-[18px] h-[18px] rounded-full flex items-center justify-center bg-red-600 hover:bg-red-700">
                        <i class="ri-delete-bin-line text-white text-[12px]"></i>
                    </div>
                </div>
            </div>
        </div>
        <input ref="inputRef" type="file" class="hidden" :accept="accept" multiple @change="onFileChange" />
        <div v-if="isUploading" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black/50 z-[9999]">
            <div class="rounded p-4 flex items-center gap-3">
                <div class="w-5 h-5 rounded-full animate-spin" style="background: conic-gradient(from 0deg, rgba(43, 57, 144, 0.91) -22.07%, #0097cc 47.78%, #8dc63f 117.9%); -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black 0); mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black 0)"></div>
                <span class="text-white">{{ $t("admin.common.uploading") }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { uploadService } from "~/services/upload/image.service";
import { useI18n } from "vue-i18n";
import type { AlbumItem, ImageItem } from "~/interfaces/upload/image.interface";

const { t } = useI18n();
const config = useRuntimeConfig();

const props = defineProps<{
    modelValue?: AlbumItem[] | null;
    fieldName?: string;
    accept?: string;
    maxFiles?: number;
}>();

const emit = defineEmits<{
    (e: "update:modelValue", value: AlbumItem[] | null): void;
    (e: "change", paths: string[]): void;
}>();

const images = ref<ImageItem[]>([]);
const hoveredIndex = ref<number | null>(null);
const isUploading = ref(false);
const inputRef = ref<HTMLInputElement | null>(null);

const accept = computed(() => props.accept || "image/jpeg,image/png,image/webp,image/jpg");
const maxFiles = computed(() => props.maxFiles || 10);
const fieldName = computed(() => props.fieldName || "album");

const extsLabel = computed(() => {
    const extensions = accept.value
        .replace(/image\//g, "")
        .toUpperCase()
        .split(",")
        .map((s) => s.trim().split(";")[0]);
    return extensions.join(", ");
});

const maxSizeLabel = computed(() => "5MB");

const triggerFileInput = () => {
    inputRef.value?.click();
};

const onFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;

    if (!files || files.length === 0) return;

    const allowedTypes = accept.value.split(",").map((type) => type.trim());
    const validation = uploadService.validateFiles(files, 5 * 1024 * 1024, allowedTypes);

    if (!validation.valid) {
        alert(validation.errors.join("\n"));
        return;
    }

    if (images.value.length + validation.validFiles.length > maxFiles.value) {
        alert(t("admin.common.maxFilesExceeded", { max: maxFiles.value }));
        return;
    }

    await uploadFiles(validation.validFiles);

    target.value = "";
};

const uploadFiles = async (files: File[]) => {
    if (files.length === 0) return;

    isUploading.value = true;

    try {
        const sessionId = uploadService.generateSessionId();
        const response = await uploadService.temp(files, sessionId);

        const newImages: ImageItem[] = response.files.map((file, index) => ({
            id: `${response.id}_${index}`,
            url: file.url,
            path: file.path,
            file: files[index],
        }));

        images.value.push(...newImages);
        emitChange();
    } catch (error) {
        alert(t("admin.common.uploadError"));
    } finally {
        isUploading.value = false;
    }
};

const removeImage = (index: number) => {
    images.value.splice(index, 1);
    emitChange();
};

const clearAll = () => {
    images.value = [];
    emitChange();
};

const emitChange = () => {
    const albumArray: AlbumItem[] = images.value.map((img) => ({ path: img.path }));

    emit("update:modelValue", albumArray.length > 0 ? albumArray : null);
    emit(
        "change",
        images.value.map((img) => img.path),
    );
};

const initializeFromModelValue = () => {
    if (!props.modelValue) {
        images.value = [];
        return;
    }
    try {
        if (Array.isArray(props.modelValue)) {
            images.value = props.modelValue.map((item, index) => ({
                id: `existing_${index}`,
                url: item.path ? `${config.public.baseUrl}/storage/${item.path}` : "",
                path: item.path,
            }));
        }
    } catch {
        images.value = [];
    }
};

watch(() => props.modelValue, initializeFromModelValue, { immediate: true });
watch(images, emitChange, { deep: true });
</script>

<style scoped>
.multiple-image-upload {
    width: 100%;
}

.upload-placeholder {
    transition: all 0.2s ease;
}

.upload-placeholder:hover {
    background: #f8f9fa !important;
    border-color: #007bff !important;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.image-item {
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s ease;
}

.image-item:hover {
    transform: translateY(-2px);
}

.check-icon,
.delete-icon {
    transition: opacity 0.2s ease;
}

.material-shadow {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
    }
}
</style>
