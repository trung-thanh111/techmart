<script setup lang="ts">
import { definePageMeta } from "#imports";
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import AppPagination from "~/components/admin/navigation/AppPagination.vue";
import CommitMent from "~/components/client/ui/CommitMent.vue";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { useAgency } from "~/composables/client/agency/useAgency";
import { TOAGENCY } from "~/utils/constants";

const { t } = useI18n();
const { data, isLoading, filters, search, setPage, getCoverUrl, formatPhone } = useAgency({
  keyword: "",
  perpage: 12,
  publish: 2,
});

const agencies = ref<[]>([]) as any;
const keyword = ref(filters.value?.keyword ?? "");
const debounceMs = 300;
let debounceId: ReturnType<typeof setTimeout> | null = null;

watch(
  () => filters.value?.keyword,
  (v) => {
    if (v !== keyword.value) keyword.value = v ?? "";
  }
);

const onSearch = () => {
  filters.value.keyword = keyword.value ?? "";
  search({
    keyword: keyword.value,
    publish: 2,
    perpage: 12,
  });
};

// Realtime search with debounce
watch(
  () => keyword.value,
  (v) => {
    if (debounceId) clearTimeout(debounceId);
    debounceId = setTimeout(() => {
      filters.value.keyword = v ?? "";
      // reset về trang 1 khi search mới
      search({ keyword: v ?? "", publish: 2, perpage: 12 });
    }, debounceMs);
  }
);

watch(
  () => data.value,
  (val) => {
    agencies.value = (val?.data?.data as any) || [];
  },
  { immediate: true }
);

const activeMapIframe = ref<string | null>(null);

const buildMapEmbedSrcFromAddress = (address?: string): string => {
  const addr = (address || "").trim();
  if (!addr) return "";
  const q = encodeURIComponent(addr);
  return `https://www.google.com/maps?q=${q}&output=embed`;
};

const openMap = (input: string): void => {
  if (!input) return;
  const isIframe = /<iframe[\s\S]*?>/i.test(input);
  if (isIframe) {
    activeMapIframe.value = input;
    return;
  }
  const src = input.startsWith("http") ? input : buildMapEmbedSrcFromAddress(input);
  if (!src) return;
  const iframeHtml = `<iframe src="${src}" style="width:100%;height:100%;border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
  activeMapIframe.value = iframeHtml;
};

const closeMap = (): void => {
  activeMapIframe.value = null;
};

const totalAgencies = computed(() => data.value?.data?.total || 0);
const hasData = computed(() => agencies.value.length > 0);
const breadcrumbs = [{ label: t("client.agency.breadcrumb"), href: TOAGENCY }];

definePageMeta({ layout: "client" });
</script>

<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="relative w-full mb-0">
      <div class="w-full h-48 md:h-64 rounded-lg overflow-hidden mb-0">
        <NuxtImg src="/client/images/showroom/image-showroom.jpg" class="w-full h-full object-cover" alt="cover" loading="lazy" />
      </div>
      <div class="w-full rounded-b-lg bg-gradient-to-r from-[#1786c9] to-[#1ecba0] px-4 py-8 flex flex-col items-center justify-center text-center -mt-2">
        <h1 class="text-white text-xl md:text-4xl font-bold mb-2">{{ $t("client.agency.distributionTitle") }}</h1>
        <p class="text-white/90 text-xs md:text-base max-w-2xl mb-2">{{ $t("client.agency.distributionDesc") }}</p>
      </div>
    </section>
    <section>
      <CommitMent />
    </section>

    <section class="w-full bg-white rounded-lg shadow md:p-4 p-2 flex flex-col md:flex-row items-center gap-2 md:gap-4 md:my-8 my-4 z-10 relative">
      <form id="searchHome" class="flex rounded overflow-hidden bg-white shadow-sm" @submit.prevent="onSearch">
        <InputGroup>
          <InputText v-model="filters.keyword" :placeholder="$t('client.agency.searchPlaceholder')" class="flex-1" size="small" />
          <button type="submit" class="bg-sky-600 px-3 md:px-4 transition-all hover:bg-sky-700">
            <svg aria-hidden="true" focusable="false" class="md:w-4 md:h-4 h-3 w-3 text-white" fill="currentColor" viewBox="0 0 512 512">
              <path d="M505 442.7L405.3 343c28.4-34.9 45.5-79 45.5-127C450.8 96.5 354.3 0 232.9 0S15 96.5 15 216.1s96.5 216.1 216.1 216.1c48 0 92.1-17.1 127-45.5l99.7 99.7c10 10 26.2 10 36.2 0l11.3-11.3c10-10 10-26.2 0-36.2zM232.9 368.1c-83.9 0-152-68.1-152-152s68.1-152 152-152 152 68.1 152 152-68.1 152-152 152z" />
            </svg>
          </button>
        </InputGroup>
      </form>
    </section>
    <section v-if="agencies.length" class="mb-4">
      <div>
        <span class="text-sm text-gray-600">
          {{ $t("client.agency.currentAgencies") }}
          <span class="text-sky-400 underline">{{ totalAgencies }} {{ $t("client.agency.agenciesNationwide") }}</span>
        </span>
      </div>
    </section>
    <section>
      <div v-if="isLoading" class="flex items-center justify-center py-10 text-gray-400">{{ $t("client.common.loading") }}</div>
      <div v-else-if="!hasData" class="text-center py-5 text-gray-400">{{ $t("client.common.noData") }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 md:gap-6 gap-4">
        <div v-for="agency in agencies" :key="agency.id" class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center hover:shadow-xl transition-shadow group">
          <NuxtImg :src="getCoverUrl(agency)" :alt="agency.name" class="w-20 h-20 rounded-full object-cover mb-3 border-2 border-gray-200 group-hover:border-sky-500 transition-all" loading="lazy" />
          <NuxtLink :to="`${TOAGENCY}/${agency.slug}`" class="text-lg font-semibold text-gray-800 group-hover:text-sky-600 mb-1">
            {{ agency.name }}
          </NuxtLink>
          <p class="text-gray-500 text-sm mb-1 text-center flex items-center justify-center gap-1">
            <svg class="w-3.5 h-3.5 text-sky-400 mr-1" fill="currentColor" viewBox="0 0 384 512">
              <path d="M168 0C75.1 0 0 75.1 0 168c0 87.2 135.1 303.7 146.6 320.6 6 9.2 20.8 9.2 26.8 0C248.9 471.7 384 255.2 384 168 384 75.1 308.9 0 216 0zm0 240c-39.8 0-72-32.2-72-72s32.2-72 72-72 72 32.2 72 72-32.2 72-72 72z" />
            </svg>
            {{ agency.address }}
          </p>
          <NuxtLink :to="'tel:' + agency.phone" class="text-sky-600 font-medium text-sm hover:underline mb-2 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-sky-400 mr-1" fill="currentColor" viewBox="0 0 512 512">
              <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
            </svg>
            {{ formatPhone(agency.phone) }}
          </NuxtLink>

          <div>
            <button v-if="agency.iframe_map" @click="openMap(agency.iframe_map)" class="inline-flex items-center cursor-pointer gap-1 px-3 py-1.5 rounded bg-sky-50 text-sky-600 text-xs font-semibold hover:bg-sky-100 transition">
              <svg class="w-3.5 h-3.5 text-sky-400 mr-1" fill="currentColor" viewBox="0 0 384 512">
                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
              </svg>
              {{ $t("client.agency.directions") }}
            </button>
            <button v-else-if="agency.address" @click="openMap(agency.address)" class="inline-flex items-center cursor-pointer gap-1 px-3 py-1.5 rounded bg-sky-50 text-sky-600 text-xs font-semibold hover:bg-sky-100 transition">
              <svg class="w-3.5 h-3.5 text-sky-400 mr-1" fill="currentColor" viewBox="0 0 384 512">
                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
              </svg>
              {{ $t("client.agency.directions") }}
            </button>
            <span v-else class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-gray-50 text-gray-400 text-xs">
              <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 384 512">
                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
              </svg>
              {{ $t("client.agency.noMapLink") }}
            </span>
          </div>
        </div>
      </div>
    </section>
    <Teleport to="body">
      <div v-if="activeMapIframe" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]" @click.self="closeMap" style="isolation: isolate">
        <div class="bg-white rounded-lg w-11/12 md:w-4/5 lg:w-3/4 h-96 md:h-[500px] relative shadow-2xl">
          <button @click="closeMap" class="absolute top-3 right-3 bg-white rounded-full w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors z-10 shadow-lg" :title="$t('client.agency.closeMap')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <div class="w-full h-full rounded-lg overflow-hidden">
            <div v-html="activeMapIframe" class="w-full h-full"></div>
          </div>
        </div>
      </div>
    </Teleport>
    <AppPagination v-if="!isLoading && hasData && data?.data?.links" :pagination="data.data" @update:page="setPage" class="mt-3" />
  </div>
</template>
