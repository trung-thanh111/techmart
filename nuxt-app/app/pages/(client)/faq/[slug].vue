<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="bg-gradient-to-r from-sky-700 via-cyan-600 to-green-500 text-white py-3 md:py-6 lg:py-12 px-3 md:px-6 mb-4 md:mb-8 rounded-md shadow-md">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-2xl sm:text-4xl font-bold mb-2">{{ title || $t("faq.faqGroup") }}</h2>
        <p class="text-sm md:text-base">{{ $t("faq.groupDescription") }}</p>
      </div>
    </section>
    <section class="mx-auto px-4 bg-white shadow-md rounded-md sm:py-3 md:py-6 sm:px-3 md:px-6 md:mb-8 sm:mb-4">
      <div class="mx-auto max-w-4xl">
        <template v-if="isLoading">
          <div class="text-center text-gray-400 py-8">{{ $t("faq.loading") }}</div>
        </template>
        <template v-else-if="!faqs.length">
          <div class="text-center text-gray-400 py-8">{{ $t("faq.noQuestions") }}</div>
        </template>
        <template v-else>
          <div v-for="(item, idx) in faqs" :key="item.id" class="bg-white rounded-md border border-gray-100 hover:border-cyan-500 shadow transition hover:shadow-lg overflow-hidden lg:mb-3 md:mb-2 mb-1">
            <button type="button" class="flex items-center justify-between w-full px-4 py-3 text-left text-base font-semibold text-gray-800 hover:text-cyan-600 transition" @click="toggle(idx)">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 flex items-center justify-center bg-cyan-100 text-cyan-600 font-bold rounded-md text-sm">{{ idx + 1 }}</div>
                <span class="line-clamp-2">{{ item.question }}</span>
              </div>
              <svg class="w-4 h-4 shrink-0 transition-transform duration-300" :class="{ 'rotate-180': opened[idx] }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-show="opened[idx]" class="px-4 pb-4 text-sm text-gray-600 overflow-hidden" v-html="item.answer"></div>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import { definePageMeta } from "#imports";
import { TOFAQ } from "~/utils/constants";
import { ref, onMounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import { useFaq } from "~/composables/faq/useFaq";

const { t } = useI18n();
const route = useRoute();
const slug = computed(() => String(route.params.slug || ""));
const opened = ref<Record<number, boolean>>({});
const title = ref<string>("");
const faqs = ref<any[]>([]);

const { fetchBySlug, isLoading } = useFaq();

const toggle = (idx: number) => {
  opened.value[idx] = !opened.value[idx];
};

onMounted(async () => {
  if (!slug.value) return;
  const cata = await fetchBySlug(slug.value);
  title.value = (cata as any)?.name || "Câu hỏi";
  faqs.value = Array.isArray((cata as any)?.faqs) ? (cata as any).faqs : [];
});

const breadcrumbs = computed(() => [
  { label: t("faq.breadcrumbDetail"), href: TOFAQ },
  { label: title.value, href: `${TOFAQ}/${slug.value}` },
]);

definePageMeta({ layout: "client" });
</script>
