<template>
  <Dialog v-model:visible="visible" modal :header="headerText" :style="{ width: dialogWidth }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" append-to="body" @hide="onHide">
    <div v-if="currentPopup" class="space-y-4">
      <NuxtLink :to="currentPopup.link as any" v-if="hasImage(currentPopup)" class="w-full flex items-center justify-center cursor-pointer" @click="close">
        <img :src="getImageUrl(currentPopup)" :alt="headerText" class="max-h-[60vh] w-full object-contain rounded" loading="lazy" />
      </NuxtLink>
      <div class="px-2">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ headerText }}</h3>
        <div v-if="currentPopup.type" class="text-sm font-medium text-sky-600 mb-2">{{ $t(`client.popup.type.${currentPopup.type}`) || currentPopup.type }}</div>
        <div class="prose max-w-none text-gray-600 text-sm md:text-base" v-html="getHtmlContent(currentPopup)"></div>
        <div v-if="getButtonText(currentPopup)" class="mt-4 flex justify-end gap-2">
          <button type="button" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors" @click="close">
            {{ $t("client.popup.close") || "Đóng" }}
          </button>
          <NuxtLink :to="(currentPopup.link as any) || '/shop'" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded shadow transition-colors" @click="close">
            {{ getButtonText(currentPopup) }}
          </NuxtLink>
        </div>
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

const dialogWidth = computed(() => "36rem");
const headerText = computed<string | undefined>(() => {
  return currentPopup.value?.title ?? undefined;
});

const loadPopup = async () => {
  try {
    const res = (await popupService.pagination({ perpage: 5, publish: 2 })) as any;
    let rows: any[] = [];
    if (Array.isArray(res?.data)) rows = res.data;
    else if (res?.data?.data) rows = res.data.data;
    else if (res?.data) rows = Array.isArray(res.data) ? res.data : [res.data];

    if (!rows.length) return;

    // Ưu tiên popup priority, nếu không có lấy popup đầu tiên
    const activePopup = rows.find((r) => r.priority === true) || rows[0];
    if (activePopup) {
      if (typeof window !== "undefined") {
        const seenKey = `techmart_popup_seen_${activePopup.id || activePopup.slug}`;
        if (sessionStorage.getItem(seenKey)) {
          return;
        }
        sessionStorage.setItem(seenKey, "true");
      }

      currentPopup.value = activePopup;
      const delay = Number(activePopup.showDelay ?? activePopup.show_delay ?? 1000);
      setTimeout(() => {
        visible.value = true;
      }, Math.max(delay, 500));
    }
  } catch (err) {
    console.error("PopupManager load error:", err);
  }
};

const hasImage = (p: any) => Boolean(p?.image?.url || p?.image?.path || p?.image);
const getImageUrl = (p: any) => {
  const target = p?.image?.url || p?.image?.path || (typeof p?.image === "string" ? p?.image : "");
  if (!target) return "";
  if (target.startsWith("http://") || target.startsWith("https://") || target.startsWith("/")) return target;
  try {
    const config = useRuntimeConfig();
    return `${config.public.baseUrl}/storage/${target.replace(/^\/+/, "")}`;
  } catch {
    return `/storage/${target}`;
  }
};
const getHtmlContent = (p: any) => p?.content || p?.form_html || "";
const getButtonText = (p: any) => p?.button_text || p?.buttonText || "";

const close = () => {
  visible.value = false;
};

const onHide = () => {};

onMounted(() => {
  loadPopup();
});
</script>
