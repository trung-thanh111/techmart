<template>
    <div>
        <div v-if="multiple" class="d-flex flex-wrap gap-2">
            <div v-for="(f, idx) in previews" :key="idx" class="position-relative">
                <NuxtImg :src="f.url || fLocalUrl(f.file)" class="rounded img-thumbnail object-cover" width="120" height="120" />
            </div>
            <label class="btn btn-light border-dashed px-3 py-4 cursor-pointer d-inline-flex align-items-center gap-2">
                <i class="ri-upload-2-line"></i>
                <span>{{ $t("admin.common.upload") }}</span>
                <input ref="inputRef" type="file" class="d-none" :accept="accept" multiple @change="onChange" />
            </label>
        </div>
        <div v-else class="text-center">
            <div class="profile-user">
                <NuxtImg :src="displayUrl" class="rounded material-shadow" width="200" alt="image" loading="lazy" />
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                    <input ref="inputRef" type="file" class="profile-img-file-input" :accept="accept" @change="onChange" />
                    <label class="profile-photo-edit avatar-xs" @click="trigger">
                        <span class="avatar-title rounded-circle bg-light text-body material-shadow">
                            <i class="ri-camera-fill"></i>
                        </span>
                    </label>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-1 mb-1">
                <Button type="button" variant="custom" severity="secondary" :label="$t('admin.common.upload')" icon="ri-upload-2-line" @click="trigger" />
            </div>
            <p v-if="note" class="text-muted text-xs mb-0">{{ note }}</p>
        </div>

        <small v-if="isUploading" class="text-muted d-block mt-2">{{ $t("admin.common.uploading") }}...</small>
    </div>
</template>

<script setup lang="ts">
import Button from "~/components/custom/Button.vue";
import { useTempUpload } from "~/composables/useTempUpload";
// import type { UploadedFile } from "~/interfaces/upload/image.interface";

const props = defineProps<{ initialUrl?: string | null; note?: string; accept?: string; multiple?: boolean }>();
// const emit = defineEmits<{ (e: "uploaded", files: UploadedFile[]): void }>();

const inputRef = ref<HTMLInputElement | null>(null);
const accept = computed(() => props.accept || "image/jpeg, image/png, image/webp, image/jpg");
const defaultImage = "/shared/images/defaults/record.jpg";

const { upload, isUploading } = useTempUpload();

const previews = ref<Array<{ url: string; file?: File }>>([]);
const displayUrl = computed(() => previews.value[0]?.url || props.initialUrl || defaultImage);

const trigger = () => inputRef.value?.click();
const fLocalUrl = (f?: File) => (f ? URL.createObjectURL(f) : "");

const onChange = async (ev: Event) => {
    const files = (ev.target as HTMLInputElement).files;
    if (!files || files.length === 0) return;
    const uploaded = await upload(files);
    // preview: dùng url trả về; form: nơi dùng component sẽ lấy path từ emit
    previews.value = uploaded.map((u) => ({ url: u.url }));
    // emit("uploaded", uploaded);
    (ev.target as HTMLInputElement).value = "";
};

// init preview nếu có initialUrl
onMounted(() => {
    if (props.initialUrl) {
        previews.value = [{ url: props.initialUrl }];
    }
});
</script>
