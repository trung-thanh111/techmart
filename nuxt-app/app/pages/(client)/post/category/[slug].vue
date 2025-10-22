<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="">
      <div class="max-w-7xl mx-auto mb-5">
        <div class="bg-white/10 backdrop-blur-lg rounded-md p-6 md:p-8 border border-white/20 shadow-xl">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center px-2 sm:px-4 lg:px-6">
            <div class="lg:col-span-9">
              <h2 class="text-4xl md:text-5xl font-bold text-white mb-5">
                <span class="block bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">{{ categoryDetail?.name || $t("client.post.categories") }}</span>
              </h2>
              <p v-if="categoryDetail?.description" class="text-base text-gray-600 mb-8 leading-relaxed line-clamp-3">{{ categoryDetail.description }}</p>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
                <div>
                  <div class="text-3xl font-bold text-cyan-400 mb-2">{{ categoryPosts.length }}</div>
                  <div class="text-sm text-gray-400">bài viết</div>
                </div>
                <div>
                  <div class="text-3xl font-bold text-cyan-700 mb-2">{{ totalLikesFormatted }}</div>
                  <div class="text-sm text-gray-400">lượt thích</div>
                </div>
              </div>
            </div>
            <div class="lg:col-span-3 flex justify-center">
              <div class="relative overflow-hidden">
                <NuxtImg :src="categoryDetail?.image?.path ? categoryDetail?.image?.url ?? '' : IMAGEDEFAULT" :alt="categoryDetail?.name || 'category'" class="w-[120px] h-[120px] md:w-[160px] md:h-[160px] rounded-full object-cover" sizes="(max-width: 640px) 120px, (max-width: 1024px) 160px, 160px" loading="lazy" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <Category description="Khám phá các danh mục công nghệ đa dạng tại Tech Mart Store – từ thiết bị điện tử, gia dụng thông minh đến phụ kiện hiện đại, đáp ứng nhu cầu học tập, làm việc và giải trí của bạn." :items="(postCatalogueList as any)" basePath="/post/category" viewAllLink="/post/categories" viewAllText="Xem tất cả" />

    <section class="mx-auto bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div>
        <div v-if="categoryPostsLoading" class="text-sm text-gray-500 p-2">{{ $t("client.post.loading") }}</div>
        <div v-else-if="categoryPosts.length === 0" class="text-center text-gray-500 py-4">{{ $t("client.post.noPosts") }}</div>
        <ul v-else class="grid gap-4">
          <li v-for="post in categoryPosts" :key="post.id">
            <NuxtLink :to="`/post/${post.slug}`" :prefetch="false" class="block">
              <div class="bg-white rounded-xl shadow-lg overflow-hidden h-full relative">
                <div class="flex flex-col lg:flex-row h-full">
                  <div class="lg:w-1/2 relative">
                    <NuxtImg :src="post.image?.path ? post.image?.url ?? '' : IMAGEDEFAULT" :alt="post.name" class="h-32 lg:h-[250px] w-full object-cover" sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 600px" loading="lazy" />
                  </div>
                  <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                    <NuxtImg :src="post.creator?.avatar ?? post.image?.url ?? IMAGEDEFAULT" :alt="post.creator?.name ?? post.name" class="w-12 h-12 lg:w-20 lg:h-20 rounded-full border-3 border-sky-400 shadow-2xl object-cover" sizes="(max-width: 640px) 48px, 80px" loading="lazy" />
                  </div>
                  <div class="lg:w-1/2 p-6 flex flex-col justify-center">
                    <div class="flex items-center mb-4">
                      <span v-if="post.isHot == 1" class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mr-3">HOT</span>
                    </div>
                    <h4 class="text-xl lg:text-2xl font-bold text-gray-800 mb-4 leading-tight line-clamp-2">{{ post.name }}</h4>
                    <p class="text-gray-600 mb-5 leading-relaxed ml-5 line-clamp-2">{{ post.description }}</p>
                    <div class="flex items-center">
                      <div class="text-xs text-gray-500 flex items-center gap-2">
                        <span class="font-medium text-gray-700">{{ post.creator?.name || "—" }}</span>
                        <span>•</span>
                        <span>{{ post.createdAtHuman }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </NuxtLink>
          </li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { IMAGEDEFAULT } from "~/utils/constants";
import { usePostCatalogue } from "~/composables/client/post/usePostCatalogue";
import { usePost } from "~/composables/client/post/usePost";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import { usePostSlider } from "~/composables/usePostSlider";
import { useDebounceFn } from "@vueuse/core";
import Category from "~/components/client/ui/Category.vue";

const route = useRoute();
const slug = computed(() => route.params.slug as string);

const { categoryDetail, fetchBySlug, categoryPosts, categoryPostsLoading, resetCategoryPosts, loadCategoryPostsById } = usePostCatalogue();
const { postCatalogueList, fetchPostCatalogues } = usePost();
const { miniCategoryOptions } = usePostSlider();

// compute total likes safely
const totalLikes = computed<number>(() => {
  try {
    return (categoryPosts.value ?? []).reduce((sum: number, p: any) => sum + (p.like_count ?? p.likeCount ?? 0), 0);
  } catch {
    return 0;
  }
});
const totalLikesFormatted = computed(() => totalLikes.value.toLocaleString());

const breadcrumbs = computed(() => [
  { label: $t("client.post.breadcrumb"), href: "/post" },
  { label: categoryDetail.value?.name ?? slug.value, href: `/post/category/${categoryDetail.value?.slug ?? slug.value}` },
]);

async function loadCategoryData(currSlug: string) {
  await fetchBySlug(currSlug);
  if (categoryDetail.value?.id) {
    resetCategoryPosts();
    await loadCategoryPostsById(categoryDetail.value.id, 5);
  }
}

onMounted(async () => {
  const jobs: Promise<any>[] = [loadCategoryData(slug.value)];
  if (!postCatalogueList.value || postCatalogueList.value.length === 0) {
    jobs.push(fetchPostCatalogues());
  }
  await Promise.all(jobs);
});

watch(
  () => slug.value,
  useDebounceFn(async (val?: string) => {
    if (!val) return;
    await loadCategoryData(val);
  }, 300)
);

definePageMeta({
  layout: "client",
});
</script>
