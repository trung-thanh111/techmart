<script setup lang="ts">
import { ref, watch, computed } from "vue";
import AppPagination from "~/components/navigation/AppPagination.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import DataState from "~/components/ui/DataState.vue";
import { useFaq } from "~/composables/faq/useFaq";
import { TOFAQ } from "~/utils/constants";
import { useI18n } from "vue-i18n";
import { definePageMeta } from "#imports";

const { t } = useI18n();
const breadcrumbs = [{ label: t("faq.breadcrumb"), href: TOFAQ }];

const { data, isLoading, filters, search, setPage, getCoverUrl } = useFaq({ keyword: "", perpage: 8, publish: 2 });
const keyword = ref(filters.value?.keyword ?? "");
const debounceMs = 300;
let debounceId: ReturnType<typeof setTimeout> | null = null;

watch(
  () => keyword.value,
  (v) => {
    if (debounceId) clearTimeout(debounceId);
    debounceId = setTimeout(() => {
      filters.value.keyword = v ?? "";
      search({ keyword: v ?? "", publish: 2, perpage: 8 });
    }, debounceMs);
  },
);

const catalogues = computed(() => (data.value?.data?.data as any[]) || []);

definePageMeta({ layout: "client" });
</script>
<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="bg-gradient-to-r from-sky-700 via-cyan-600 to-green-500 text-white py-3 md:py-6 lg:py-12 px-3 md:px-6 mb-4 md:mb-8 rounded-md shadow-md">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-2xl md:text-4xl font-bold mb-2">{{ $t("faq.havingQuestions") }}</h2>
        <p class="mb-4 md:mb-6 text-xs md:text-base">{{ $t("faq.shareWithUs") }}</p>
        <form id="searchFaq" class="flex rounded overflow-hidden bg-white shadow-sm mx-auto max-w-xl" @submit.prevent>
          <input v-model="keyword" type="text" name="keyword" :placeholder="$t('faq.searchPlaceholder')" class="flex-1 px-3 md:px-4 py-1 md:py-2 text-gray-600 focus:outline-none" />
          <button type="button" class="bg-sky-600 px-3 md:px-4 transition-all hover:bg-sky-700">
            <i class="fas fa-search text-white"></i>
          </button>
        </form>
      </div>
    </section>
    <section class="mx-auto px-4 bg-white shadow-md rounded-md py-3 md:py-6 mb-4 md:mb-8">
      <div class="mb-4 md:mb-6 text-center">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ $t("faq.faqTitle") }}</h2>
        <div class="mt-1.5 text-gray-600 text-xs md:text-sm flex gap-1 justify-center">
          {{ $t("faq.faqDescription") }}
          <span for="formContact" class="text-sky-500 underline hover:text-sky-600 cursor-pointer">{{ $t("faq.contactNow") }}</span>
        </div>
      </div>
      <DataState :loading="isLoading" :error="false" :data="catalogues" :show-empty-image="true">
        <template v-if="catalogues.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <NuxtLink v-for="item in catalogues" :key="item.id" :to="`${TOFAQ}/${item.slug}`" class="flex flex-col bg-white rounded-xl border border-gray-100 hover:border-cyan-500 shadow transition hover:shadow-lg overflow-hidden">
              <NuxtImg :src="getCoverUrl(item)" :alt="item.name" class="w-full h-40 md:h-48 object-cover" loading="lazy" />
              <div class="p-4 flex flex-col flex-1">
                <h3 class="text-base font-semibold mb-2 hover:text-cyan-700 line-clamp-2">{{ item.name }}</h3>
                <button type="button" class="mt-auto inline-flex items-center justify-center bg-gradient-to-r from-sky-700 to-sky-600 hover:from-sky-600 hover:to-sky-500 text-white text-xs md:text-sm font-semibold py-2 px-5 rounded transition">{{ $t("faq.viewDetails") }}</button>
              </div>
            </NuxtLink>
          </div>
        </template>
      </DataState>
      <AppPagination v-if="!isLoading && data?.data?.links" :pagination="data.data" @update:page="setPage" class="mt-3" />
    </section>
    <section class="mx-auto p-4 bg-white shadow-md rounded-md py-3 md:py-6 mb-4 md:mb-8">
      <div class="mt-4 md:mt-6 max-w-2xl mx-auto bg-white shadow-lg border border-gray-200 p-2 md:p-4 lg:p-6 rounded-lg">
        <h2 class="text-xl md:text-2xl text-center font-bold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] mb-2 md:mb-4 lg:mb-5">{{ $t("faq.contactTitle") }}</h2>
        <form class="mx-auto max-w-xl my-2 md:my-4" id="formContact">
          <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label for="company" class="block text-sm font-semibold leading-6 text-gray-900">{{ $t("form.fullName") }}</label>
              <div class="mt-1.5">
                <input required type="text" name="company" id="company" autocomplete="organization" :placeholder="$t('form.fullNamePlaceholder')" class="block w-full border border-gray-200 rounded-md px-3 py-2 focus:outline-none text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm" />
              </div>
            </div>
            <div class="sm:col-span-2">
              <label for="email" class="block text-sm font-semibold leading-6 text-gray-900">{{ $t("form.email") }}</label>
              <div class="mt-1.5">
                <input required type="email" name="email" id="email" autocomplete="email" :placeholder="$t('form.emailPlaceholder')" class="block w-full border border-gray-200 rounded-md px-3 py-2 focus:outline-none text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm" />
              </div>
            </div>
            <div class="sm:col-span-2">
              <label for="phone" class="block text-sm font-semibold leading-6 text-gray-900">{{ $t("form.subject") }}</label>
              <div class="mt-1.5">
                <input required type="tel" name="phone" id="phone" autocomplete="tel" :placeholder="$t('form.subjectPlaceholder')" class="block w-full border border-gray-200 rounded-md px-3 py-2 focus:outline-none text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm" />
              </div>
            </div>
            <div class="sm:col-span-2">
              <label for="message" class="block text-sm font-semibold leading-6 text-gray-900">{{ $t("form.content") }}</label>
              <div class="mt-1.5">
                <textarea name="message" id="message" rows="4" :placeholder="$t('form.contentPlaceholder')" class="block w-full border border-gray-200 rounded-md px-3 py-2 focus:outline-none text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm"></textarea>
              </div>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-block bg-cyan-600 text-white rounded-sm py-1 md:py-2 px-4 md:px-6 hover:bg-cyan-700 transition text-sm">{{ $t("common.confirm") }}</button>
          </div>
        </form>
      </div>
    </section>
  </div>
</template>
