<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="mx-auto">
      <div class="mb-4 md:mb-5">
        <Splide :options="postSlideHotOptions" id="post-slide-hot">
          <SplideSlide v-for="featuredPostItem in featuredPosts" :key="featuredPostItem.id">
            <NuxtLink :to="`/post/${featuredPostItem.slug}`" class="relative block group bg-white rounded-md overflow-hidden shadow-lg transition-all">
              <div class="absolute inset-0 bg-white"></div>
              <div class="relative p-4 md:p-8 flex flex-col md:flex-row items-center">
                <div class="flex-1 text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] animate-glow mb-6 md:mb-0 md:pr-8">
                  <span class="inline-block px-3 py-1 bg-gray-200 text-red-500 rounded-full text-xs md:text-sm font-medium mb-3 md:mb-4">Nổi bật</span>
                  <h3 class="text-2xl md:text-4xl font-bold mb-3 md:mb-4 leading-tight">
                    {{ featuredPostItem.name }}
                  </h3>
                  <p class="text-gray-600 text-sm md:text-base mb-4 md:mb-6 leading-relaxed">
                    {{ featuredPostItem.description }}
                  </p>
                  <div class="flex items-center text-gray-600 gap-2">
                    <span class="text-xs md:text-sm">Tác giả: {{ featuredPostItem.creator?.name }}</span>
                    <span class="mx-1 md:mx-2">•</span>
                    <span class="text-xs md:text-sm">
                      {{ featuredPostItem.createdAtHuman }}
                    </span>
                  </div>
                </div>
                <div class="flex-shrink-0 mt-4 md:mt-0">
                  <div class="w-full md:w-60 rounded-md">
                    <NuxtImg :src="featuredPostItem.image?.path ? featuredPostItem.image?.url ?? '' : IMAGEDEFAULT" :alt="featuredPostItem.name" class="rounded-md object-contain w-full h-auto" loading="lazy" />
                  </div>
                </div>
              </div>
            </NuxtLink>
          </SplideSlide>
        </Splide>
      </div>
      <div class="w-full max-w-8xl mx-auto my-4 md:my-5">
        <div class="h-[2px] w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4 bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4 md:mb-8">
        <template v-for="(featuredPostLimitItem, idx) in featuredPostLimits" :key="featuredPostLimitItem.id">
          <div v-if="idx === 0" class="lg:col-span-2">
            <div class="relative rounded-xl overflow-hidden border border-gray-100 hover:border-cyan-200 hover:shadow-xl transition-all">
              <NuxtImg :src="featuredPostLimitItem.image?.path ? featuredPostLimitItem.image?.url ?? '' : IMAGEDEFAULT" :alt="featuredPostLimitItem.name" class="w-full h-[220px] md:h-[400px] object-cover" loading="lazy" />
              <div v-if="featuredPostLimitItem.isHot" class="absolute top-2 left-2 md:top-4 md:left-4 bg-white/10 backdrop-blur-sm rounded-lg px-2 py-1">
                <span class="text-red-500 font-bold text-xs">HOT</span>
              </div>
              <div class="absolute bottom-0 left-0 w-full h-full max-h-32 md:max-h-40 bg-gradient-to-t from-white to-transparent"></div>
              <div class="absolute bottom-0 left-0 right-0 p-3 md:p-6">
                <NuxtLink :to="`/post/${featuredPostLimitItem.slug}`" class="text-gray-700 text-base md:text-2xl font-bold mb-1 md:mb-2 leading-tight hover:text-cyan-600 transition-colors line-clamp-2">{{ featuredPostLimitItem.name }}</NuxtLink>
                <p class="text-gray-600 hover:text-gray-500 text-xs md:text-sm font-medium line-clamp-2">{{ featuredPostLimitItem.description }}</p>
              </div>
            </div>
          </div>
          <NuxtLink v-else :to="`/post/${featuredPostLimitItem.slug}`" class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-100 hover:border-cyan-200 block">
            <div class="relative overflow-hidden">
              <div class="h-[140px] md:h-[216px] bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center relative">
                <NuxtImg :src="featuredPostLimitItem.image?.path ? featuredPostLimitItem.image?.url ?? '' : IMAGEDEFAULT" :alt="featuredPostLimitItem.name" class="object-cover w-full h-full" loading="lazy" />
                <div v-if="featuredPostLimitItem.isHot" class="absolute top-2 left-2 md:top-4 md:left-4 bg-white/10 backdrop-blur-sm rounded-lg px-2 py-1">
                  <span class="text-red-500 font-bold text-xs">HOT</span>
                </div>
              </div>
            </div>
            <div class="p-3 md:p-4">
              <NuxtLink :to="`/post/${featuredPostLimitItem.slug}`" class="text-base md:text-xl font-bold text-gray-700 mb-2 md:mb-3 group-hover:text-cyan-600 transition-colors line-clamp-2">{{ featuredPostLimitItem.name }}</NuxtLink>
              <p class="text-gray-600 text-xs md:text-sm mb-2 md:mb-4 leading-relaxed line-clamp-2">{{ featuredPostLimitItem.description }}</p>
              <div class="flex items-center justify-between text-xs md:text-sm text-gray-500">
                <span>{{ featuredPostLimitItem.creator?.name }}</span>
                <span>{{ featuredPostLimitItem.createdAtHuman }}</span>
              </div>
            </div>
          </NuxtLink>
        </template>
      </div>
    </section>
    <section class="shop-category bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4 md:mb-6">
      <div class="mb-4 md:mb-8">
        <div class="flex flex-wrap justify-between items-center gap-2">
          <div>
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg md:text-2xl font-bold">Danh mục</h2>
            <div class="h-1 max-w-64 my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="group flex items-center gap-2">
            <a href="#" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 group-hover:after:w-full">Xem tất cả</a>
          </div>
        </div>
        <p class="text-gray-600 mt-1 md:mt-2 max-w-2xl text-xs md:text-sm">Khám phá các danh mục công nghệ đa dạng tại Tech Mart Store – từ thiết bị điện tử, gia dụng thông minh đến phụ kiện hiện đại, đáp ứng nhu cầu học tập, làm việc và giải trí của bạn.</p>
      </div>
      <Splide :options="miniCategoryOptions" id="mini-category-slider">
        <SplideSlide v-for="postCatalogueItem in postCatalogueList" :key="postCatalogueItem.id" class="shadow-lg">
          <a href="#" class="block">
            <div class="mini-category-item" style="height: 180px; width: 120px">
              <NuxtImg :src="postCatalogueItem.image?.path ? postCatalogueItem.image?.url ?? '' : IMAGEDEFAULT" loading="lazy" class="rounded-md" alt="{{ postCatalogueItem.name }}" />
              <div class="mini-category-text">
                <span class="line-clamp-2">{{ postCatalogueItem.name }}</span>
              </div>
            </div>
          </a>
        </SplideSlide>
      </Splide>
    </section>

    <section class="product-accsessory bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4 md:mb-6">
      <div class="mb-4 md:mb-5">
        <div class="flex justify-between items-center gap-2">
          <div>
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg md:text-2xl font-bold">Yêu thích nhất</h2>
          </div>
        </div>
      </div>
      <Splide :options="postHotOptions" id="post-hot-slider">
        <SplideSlide>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
            <a v-for="id in 4" :key="id" href="#" class="flex items-center gap-2 md:gap-3 p-2 md:p-3 rounded-md hover:bg-gray-50 transition">
              <div class="text-2xl md:text-4xl font-bold text-gray-500">#{{ id }}</div>
              <div class="flex-1">
                <p class="text-sm md:text-lg font-medium text-gray-800 leading-snug hover:text-cyan-600 transition line-clamp-2">Toshiba lần đầu ra mắt máy rửa chén tại Việt Nam: Có 3 model, sấy khí nóng, tự hé cửa và gõ để mở</p>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Anh Tú.</p>
              </div>
            </a>
          </div>
        </SplideSlide>
      </Splide>
    </section>

    <section class="max-w-[1440px] mx-auto p-2 md:p-4 bg-white rounded-md shadow mb-2 md:mb-6">
      <h2 class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-lg md:text-2xl font-bold mb-3 md:mb-4">Bài viết sản phẩm</h2>
      <div v-if="byCatalogueLoading[3]" class="text-sm text-gray-500 p-2">Đang tải...</div>
      <div v-else-if="!byCatalogueCache[3] || byCatalogueCache[3].length === 0" class="text-sm text-gray-500 p-2">Chưa có bài viết.</div>
      <Splide v-else :options="postProductOptions" id="post-product-slider">
        <SplideSlide v-for="post in byCatalogueCache[3]" :key="post.id">
          <NuxtLink :to="`/post/${post.slug}`" class="relative rounded-xl overflow-hidden border border-gray-100 hover:border-cyan-200 hover:shadow-xl transition-all block">
            <NuxtImg :src="post.image?.path ? post.image?.url ?? '' : IMAGEDEFAULT" :alt="post.name" class="w-full h-40 md:h-72 object-cover" loading="lazy" />
            <div class="absolute bottom-0 left-0 w-full h-20 md:h-40 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 right-0 p-3 md:p-6">
              <h2 class="text-gray-800 text-base md:text-2xl font-bold mb-1 md:mb-2 leading-tight hover:text-cyan-600 transition-colors line-clamp-2">{{ post.name }}</h2>
              <p class="text-gray-600 text-xs md:text-sm font-medium line-clamp-2">{{ post.description }}</p>
            </div>
          </NuxtLink>
        </SplideSlide>
      </Splide>
    </section>

    <section class="bg-white shadow-md rounded-md py-2 md:py-3 px-2 md:px-6 mb-2 md:mb-6">
      <h2 class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-lg md:text-2xl font-bold mb-3 md:mb-4">Tin mới trong ngày</h2>
      <div class="grid grid-cols-12 gap-3 md:gap-4">
        <div class="col-span-12 lg:col-span-9">
          <div class="space-y-4 md:space-y-6">
            <NuxtLink v-for="postLatestItem in latestItems" :key="postLatestItem.id" :to="`/post/${postLatestItem.slug}`" class="flex items-start gap-2 md:gap-4 rounded-lg hover:bg-gray-100 border border-gray-100 hover:border-cyan-200 mb-3 md:mb-5">
              <NuxtImg :src="postLatestItem.image?.path ? postLatestItem.image?.url ?? '' : IMAGEDEFAULT" :alt="postLatestItem.name" class="w-28 md:w-[240px] h-24 md:h-[150px] rounded-lg object-cover flex-shrink-0" loading="lazy" />
              <div class="p-2">
                <h3 class="text-base md:text-lg font-semibold text-gray-800 hover:text-cyan-600 transition-colors md:line-clamp-2 line-clamp-1">{{ postLatestItem.name }}</h3>
                <p class="text-xs md:text-sm text-gray-600 line-clamp-1 md:line-clamp-2">{{ postLatestItem.description }}</p>
                <div class="flex items-center justify-between mt-1 md:mt-2">
                  <div class="flex items-center">
                    <NuxtImg :src="postLatestItem.creator?.avatar ? postLatestItem.creator.avatar : IMAGEDEFAULT" :alt="postLatestItem.creator?.name || 'Author'" class="w-6 md:w-8 h-6 md:h-8 rounded-full mr-2" loading="lazy" />
                    <span class="text-xs md:text-sm text-gray-700 font-medium">{{ postLatestItem.creator?.name }}</span>
                  </div>
                  <span class="text-xs md:text-sm">{{ postLatestItem.createdAtHuman }}</span>
                </div>
              </div>
            </NuxtLink>
          </div>
          <div class="mt-3 md:mt-5 flex justify-center">
            <button v-if="latestPage <= latestLastPage" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs md:text-sm disabled:opacity-60" :disabled="isLoading || latestLoading" @click="loadLatest()">
              {{ latestLoading ? "Đang tải..." : "Xem thêm" }}
            </button>
          </div>
        </div>
        <div class="col-span-12 lg:col-span-3">
          <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200 mb-3 md:mb-5">
            <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">Xem nhanh</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-2 gap-2 mb-3 md:mb-4">
              <NuxtLink v-for="post in hotQuickItems" :key="post.id" :to="`/post/${post.slug}`" class="group block">
                <NuxtImg :src="post.image?.path ? post.image?.url ?? '' : IMAGEDEFAULT" :alt="post.name" class="rounded-md object-cover w-full h-16 md:w-[145px] md:h-[80px]" loading="lazy" />
                <span class="text-xs md:text-sm text-gray-800 font-medium group-hover:text-cyan-600 transition-colors line-clamp-2">{{ post.name }}</span>
              </NuxtLink>
            </div>
            <div class="flex justify-center">
              <button v-if="hotQuickPage <= hotQuickLastPage" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs md:text-sm disabled:opacity-60" :disabled="isLoading || hotQuickLoading" @click="loadHotQuick()">
                {{ hotQuickLoading ? "Đang tải..." : "Xem thêm" }}
              </button>
            </div>
          </div>
          <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200">
            <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">
              <span class="text-sky-600">#</span>
              Tags
            </h2>
            <div class="flex flex-wrap gap-2">
              <NuxtLink to="" v-for="tagItem in tagList" :key="tagItem.id" class="px-2 md:px-3 py-1 text-xs md:text-[12px] bg-[#d9dfe3] max-w-max rounded font-semibold text-[#7281a3]">#{{ tagItem.name }}</NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import { usePostSlider } from "~/composables/usePostSlider";
import { usePost } from "~/composables/client/post/usePost";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";

const { isLoading, fetchList, loadLatest, latestItems, latestPage, latestLastPage, latestLoading, loadHotQuick, hotQuickItems, hotQuickPage, hotQuickLastPage, hotQuickLoading, tagList, fetchTags, fetchPostCatalogues, postCatalogueList, fetchHotCataloguePosts, hotCataloguePosts, fetchPostsByCatalogueId, byCatalogueCache, byCatalogueLoading } = usePost();
const { postSlideHotOptions, miniCategoryOptions, postHotOptions, postProductOptions } = usePostSlider();
const featuredPosts = await fetchList("featured");
const featuredPostLimits = await fetchList("featuredLimit");
await fetchTags();
await fetchPostCatalogues();
await fetchHotCataloguePosts();
await fetchPostsByCatalogueId(3);
await loadLatest();
await loadHotQuick();

const breadcrumbs = [{ label: "Bài viết", href: "/post" }];
definePageMeta({
  layout: "client",
});
</script>
