<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="bg-gradient-to-r from-sky-700 via-cyan-600 to-green-500 text-white py-3 md:py-6 lg:py-12 px-3 md:px-6 mb-4 md:mb-8 rounded-md shadow-md">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-2xl md:text-4xl font-bold mb-2">Bạn đang gặp thắc mắc</h2>
        <p class="mb-4 md:mb-6 text-xs md:text-base">Hãy chia sẻ cho chúng tôi, cùng nhau giải quyết vấn thắc mắc nhé!</p>
        <form id="searchFaq" class="flex rounded overflow-hidden bg-white shadow-sm mx-auto max-w-xl" @submit.prevent>
          <input v-model="keyword" type="text" name="keyword" placeholder="Vấn đề, thắc mắc mà bạn đang găp phải " class="flex-1 px-3 md:px-4 py-1 md:py-2 text-gray-600 focus:outline-none" />
          <button type="button" class="bg-sky-600 px-3 md:px-4 transition-all hover:bg-sky-700">
            <i class="fas fa-search text-white"></i>
          </button>
        </form>
      </div>
    </section>
    <section class="mx-auto px-4 bg-white shadow-md rounded-md sm:py-3 md:py-6 sm:px-3 md:px-6 md:mb-8 sm:mb-4">
      <div class="col-span-full mb-5">
        <h2 class="text-xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ title }}</h2>
        <p class="mt-1.5 text-gray-600 text-sm md:text-base">Tổng hợp các câu hỏi thuộc nhóm này</p>
      </div>
      <div class="mx-auto max-w-4xl">
        <template v-if="isLoading">
          <div class="text-center text-gray-400 py-8">Đang tải...</div>
        </template>
        <template v-else-if="!faqs.length">
          <div class="text-center text-gray-400 py-8">Chưa có câu hỏi</div>
        </template>
        <template v-else>
          <div v-for="(item, idx) in faqs" :key="item.id" class="bg-white rounded-md border border-gray-100 hover:border-cyan-500 shadow transition hover:shadow-lg overflow-hidden lg:mb-3 md:mb-2 mb-1">
            <h2>
              <button type="button" class="flex items-center justify-between w-full px-4 py-3 text-left text-base font-semibold text-gray-800 hover:text-cyan-600 transition" @click="toggle(idx)">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 flex items-center justify-center bg-cyan-100 text-cyan-600 font-bold rounded-md text-sm">{{ idx + 1 }}</div>
                  <span class="line-clamp-2">{{ item.question }}</span>
                </div>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300" :class="{ 'rotate-180': opened[idx] }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </h2>
            <transition name="accordian">
              <div v-show="opened[idx]" class="px-4 pb-4 text-sm text-gray-600" v-html="item.answer"></div>
            </transition>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { useFaq } from "~/composables/client/faq/useFaq";
import { faqService } from "~/services/faq/faq.service";

const route = useRoute();
const slug = computed(() => String(route.params.slug || ""));
const opened = ref<Record<number, boolean>>({});
const title = ref<string>("");
const keyword = ref("");
const faqs = ref<any[]>([]);
const debounceMs = 300;
let debounceId: ReturnType<typeof setTimeout> | null = null;

const { fetchBySlug, faq, isLoading } = useFaq();

const fetchFaqs = async () => {
  if (!faq.value?.id) return;
  try {
    const res = await faqService.paginationPublic({
      keyword: keyword.value,
      publish: 2,
      faq_catalogue_id: faq.value.id,
      perpage: 20,
      page: 1,
    });
    faqs.value = (res as any)?.data?.data || [];
  } catch (e) {
    faqs.value = [];
  }
};

const toggle = (idx: number) => {
  opened.value[idx] = !opened.value[idx];
};

watch(
  () => keyword.value,
  (v) => {
    if (debounceId) clearTimeout(debounceId);
    debounceId = setTimeout(() => {
      fetchFaqs();
    }, debounceMs);
  }
);

onMounted(async () => {
  if (!slug.value) return;
  const cat = await fetchBySlug(slug.value);
  title.value = (cat as any)?.name || "Câu hỏi";
  await fetchFaqs();
});

const breadcrumbs = computed(() => [
  { label: "Câu hỏi", href: "/faq" },
  { label: title.value || slug.value, href: `/faq/${slug.value}` },
]);
</script>
