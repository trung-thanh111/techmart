<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb />
    <section class="w-full overflow-hidden">
      <div class="flex items-center">
        <div class="backdrop-blur-md bg-gray-200/40 p-8 rounded-lg shadow-lg w-full">
          <h1 class="text-xl md:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00ca9b] mb-2">
            {{ isLoading ? $t("common.loading") : policy ? getPolicyTitle(policy.keyword as any) : $t("common.notFound") }}
          </h1>
          <div class="h-1 max-w-64 my-2 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
          </div>
          <p class="text-xs md:text-sm leading-6 mt-2 text-gray-900">
            {{ $t("common.policyContactText") }}
            <NuxtLink to="#" class="underline text-sky-500">{{ $t("common.contactUs") }}</NuxtLink>
            {{ $t("common.withUs") }}
          </p>
        </div>
      </div>
    </section>
    <section class="flex flex-col md:flex-row bg-white shadow-md rounded-lg md:my-5 my-2">
      <SidebarPolicy />
      <main class="flex-1 px-4 md:px-6 lg:px-8 mx-auto">
        <DataState :loading="isLoading" :error="!policy && !isLoading" :data="policy" :empty-message="$t('common.notFound')">
          <div class="max-w-full text-gray-800 text-sm leading-7 md:leading-9">
            <div class="relative mb-4">
              <NuxtImg src="/images/bg-title-policy.png" alt="image-policy-bg" loading="lazy" class="w-full h-auto object-cover" />
              <div class="absolute inset-0 flex flex-col justify-center items-center gap-2">
                <div class="text-lg md:text-[24px] font-semibold uppercase">
                  {{ policy ? $t("policy.prefix", { title: getPolicyTitle(policy.keyword as any) }) : $t("common.noData") }}
                </div>
                <p class="text-sm text-gray-600">{{ $t("common.lastUpdated") }} {{ policy ? new Date(policy.updatedAt as any).toLocaleDateString("vi-VN") : "" }}</p>
              </div>
            </div>
            <div class="prose" v-if="policy && policy.content" v-html="policy.content"></div>
            <div class="prose" v-else>
              <div class="text-center text-gray-500">{{ $t("common.updatingContent") }}</div>
            </div>
          </div>
        </DataState>
      </main>
    </section>
  </div>
</template>
<script setup lang="ts">
import type { ISystem } from "~/interfaces/system/system.interface";
import { ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { systemService } from "~/services/system/system.service";
import { getPolicyTitle } from "~/utils/system/policy";
import SidebarPolicy from "~/components/navigation/SidebarPolicy.vue";
import DataState from "~/components/ui/DataState.vue";
import { useRoute, useRouter } from "vue-router";
import { TOPRIVACYPOLICY } from "~/utils/constants";
import { definePageMeta } from "#imports";

const route = useRoute();
const router = useRouter();
const policy = ref<ISystem | null>(null);
const isLoading = ref(true);

const toast = useAppToast();
const { t } = useI18n();

const loadPolicy = async (keyword: string) => {
  try {
    isLoading.value = true;
    const response = await systemService.findByKeyword(keyword);
    if (response.data && Array.isArray(response.data) && response.data.length > 0) {
      (policy.value as any) = response.data[0];
    } else if (response.data && !Array.isArray(response.data)) {
      (policy.value as any) = response.data;
    } else {
      toast.value = t("policy.notFound");
    }
  } catch (err) {
    console.error("Error loading policy:", err);
    toast.value = t("message.errorLoading");
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  const keyword = route.params.keyword as string;

  if (!keyword) {
    await router.replace(TOPRIVACYPOLICY);
    return;
  }

  await loadPolicy(keyword);
});

watch(
  () => route.params.keyword,
  async (newKeyword) => {
    if (newKeyword) {
      await loadPolicy(newKeyword as string);
    }
  },
);

definePageMeta({
  layout: "client",
});
</script>
