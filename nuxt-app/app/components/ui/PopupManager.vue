<template>
  <Dialog v-model:visible="visible" modal :header="headerText" :style="{ width: dialogWidth }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" append-to="body" @hide="onHide">
    <div v-if="currentPopup" class="space-y-4">
      <NuxtLink :to="currentPopup.link as any" v-if="hasImage(currentPopup)" class="w-full flex items-center justify-center">
        <NuxtImg :src="getImageUrl(currentPopup)" :alt="headerText" class="max-h-[60vh] w-full object-contain rounded" loading="lazy" />
      </NuxtLink>
      <div class="px-2">
        <h3 class="text-lg font-semibold mb-2">{{ headerText }}</h3>
        <div v-if="currentPopup.type" class="text-sm text-gray-500 mb-2">{{ $t(`client.popup.type.${currentPopup.type}`) || currentPopup.type }}</div>
        <div class="prose max-w-none" v-html="getHtmlContent(currentPopup)"></div>
      </div>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { popupService } from "~/services/system/popup.service";
import type { IPopup } from "~/interfaces/system/popup.interface";

const visible = ref(false);
const currentPopup = ref<IPopup | null>(null);

const dialogWidth = computed(() => "40rem");
const headerText = computed<string | undefined>(() => {
  return currentPopup.value?.title ?? undefined;
});

const loadPopup = async () => {
  try {
    const res = (await popupService.pagination({ perpage: 5 })) as any;
    let rows: any[] = [];
    if (Array.isArray(res?.data)) rows = res.data;
    else if (res?.data?.data) rows = res.data.data;
    else if (res?.data) rows = Array.isArray(res.data) ? res.data : [res.data];

    // select only a single priority popup
    const priority = rows.find((r) => r.priority === true);
    if (priority) {
      currentPopup.value = priority;
      visible.value = true;
    }
  } catch (err) {
    console.error("PopupManager load error:", err);
  }
};

const hasImage = (p: any) => !!(p?.image?.url || p?.image?.path || p?.image);
const getImageUrl = (p: any) => String(p?.image?.url || p?.image?.path || p?.image || "");
const getHtmlContent = (p: any) => p?.content || p?.form_html || "";

const close = () => {
  visible.value = false;
};

const onConfirm = () => {
  if (!currentPopup.value) return close();
  const link = currentPopup.value.link;
  if (link) window.open(link, "_blank");
  close();
};

const onHide = () => {};

onMounted(() => {
  loadPopup();
});
</script>
