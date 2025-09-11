<template>
  <div class="text-center">
    <div class="profile-user">
      <NuxtImg :src="avatar" class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow object-cover" alt="user-profile-image" loading="lazy" />
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
</template>

<script setup lang="ts">
import Button from "~/components/admin/custom/Button.vue";
import { useImageUpload } from "~/composables/useImageUpload";

const props = defineProps<{
  initialUrl?: string | null;
  note?: string;
  accept?: string;
}>();
const emit = defineEmits<{ (e: "selected", file: File): void }>();

const { previewUrl, file, setInitialPreview, onChange } = useImageUpload();
const inputRef = ref<HTMLInputElement | null>(null);
const defaultAvatar = "/shared/images/defaults/avatar.jpg";
const avatar = computed(() => previewUrl.value || props.initialUrl || defaultAvatar);
const accept = computed(() => props.accept || "image/jpeg, image/png, image/webp, image/jpg");

const trigger = () => inputRef.value?.click();

watch(file, (f) => {
  if (f) emit("selected", f);
});

onMounted(() => setInitialPreview(props.initialUrl || null));
</script>
