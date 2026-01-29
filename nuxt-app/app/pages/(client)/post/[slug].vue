<template>
  <div class="main-content max-w-7xl mx-auto">
    <div v-if="postDetailLoading" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
        <p class="text-gray-600">{{ $t("post.loading") }}</p>
      </div>
    </div>
    <div v-else-if="postDetailError" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <div class="text-red-500 text-6xl mb-4">⚠️</div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $t("post.cannotLoadPost") }}</h2>
        <p class="text-gray-600 mb-4">{{ postDetailError }}</p>
        <NuxtLink :to="TOPOST" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">{{ $t("post.backToList") }}</NuxtLink>
      </div>
    </div>
    <div v-else-if="postDetail">
      <Breadcrumb :crumbs="breadcrumbs" />
      <section class="relative w-full mb-5">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-12 lg:col-span-9 bg-white rounded-lg shadow-md p-2">
            <div class="h-[40vw] max-h-[400px] min-h-[180px] overflow-hidden rounded-xl">
              <NuxtImg :src="postDetail?.image?.url || '/images/posts/post-home-4.jpg'" :alt="postDetail?.name || 'Cover'" class="w-full h-full object-cover object-center" loading="lazy" />
            </div>
            <div class="max-w-4xl mx-auto px-2 sm:px-4 -mt-16 sm:-mt-24 relative z-10">
              <div class="bg-white shadow-xl rounded-2xl p-3 sm:p-6 md:p-8">
                <div class="flex flex-wrap gap-3 items-center mb-4">
                  <span v-for="catalogue in (postDetail?.postCatalogues as any) || []" :key="catalogue.id" class="bg-red-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                    {{ catalogue.name }}
                  </span>
                  <span v-for="tag in (postDetail?.tags as any) || []" :key="tag.id" class="bg-sky-100 text-sky-600 text-xs font-medium px-3 py-1 rounded-full">#{{ tag.name }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 leading-snug mb-4 break-words">
                  {{ postDetail?.name || "Đang tải..." }}
                </h1>
                <div class="flex items-center text-sm text-gray-500 mb-6">
                  <NuxtImg :src="postDetail?.creator?.avatar || '/images/social/zalo.png'" :alt="postDetail?.creator?.name || 'Author'" class="w-8 h-8 rounded-full mr-3 border border-gray-300" loading="lazy" />
                  <div>
                    <p class="text-red-600 font-semibold">{{ postDetail?.creator?.name || $t("post.author") }}</p>
                    <div class="flex items-center text-gray-400">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 0 0-1 1v1H5a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3h-.01V3a1 1 0 1 0-2 0v1H8V3a1 1 0 0 0-1-1H6z" /></svg>
                      <span>{{ postDetail?.publishedAtHuman }}</span>
                    </div>
                  </div>
                </div>
                <div v-if="toc.length" class="mb-5 bg-sky-50 border-l-4 border-sky-400 rounded-md">
                  <div class="flex items-center justify-between w-full px-5 py-3 cursor-pointer" @click="isTocOpen = !isTocOpen">
                    <p class="font-semibold text-sky-700 text-lg">{{ $t("post.tableOfContent") }}</p>
                    <svg class="w-4 h-4 shrink-0 transition-transform" :class="isTocOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                  <transition name="accordian">
                    <div v-show="isTocOpen" class="px-5 pb-5">
                      <ul class="space-y-2 text-sky-600 text-sm mt-2">
                        <li v-for="item in toc" :key="item.id">
                          <a :href="`#${item.id}`" class="hover:underline font-medium" :class="{ 'pl-4': item.level > 2, 'pl-8': item.level > 3 }">
                            {{ item.text }}
                          </a>
                        </li>
                      </ul>
                    </div>
                  </transition>
                </div>
                <div v-if="contentWithIds" class="text-gray-700 space-y-3 sm:space-y-4 text-base leading-relaxed break-words prose prose-lg max-w-none" v-html="contentWithIds"></div>
                <div v-else-if="postDetailLoading" class="text-gray-500 italic">{{ $t("post.loadingContent") }}</div>
                <div v-else class="text-gray-500 italic">{{ $t("post.noContent") }}</div>
                <div class="flex flex-wrap items-center justify-between mt-3 gap-1 sm:gap-0">
                  <div class="flex items-center gap-5">
                    <PostLikeButton :post-id="postDetail.id" :is-liked="isLiked" :like-count="likeCount" :is-loading="isLikeLoading" @toggle="handleToggleLike" />
                    <div class="flex items-center space-x-1 text-gray-500">
                      <svg class="w-5 h-5 sm:w-4 sm:h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                        <path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z" />
                      </svg>
                      <span class="text-xs sm:text-sm">{{ formatNumber(postDetail.viewCount as any) }} {{ $t("post.views") }}</span>
                    </div>
                  </div>
                  <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-3">
                    <span class="caption font-medium text-gray-500 mr-2 md:mr-3 hidden sm:block text-xs sm:text-sm">{{ $t("post.share") }}</span>
                    <NuxtLink aria-label="message-share" class="hover:opacity-70">
                      <img src="/images/social/messenger.png" alt="facebook message" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <NuxtLink aria-label="facebook-share" class="hover:opacity-70">
                      <img src="/images/social/facebook.png" alt="facebook share" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <NuxtLink aria-label="printer-share" class="hover:opacity-70">
                      <img src="/images/social/zalo.png" alt="sharing zalo" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <div class="relative">
                      <div class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 hover:opacity-70">
                        <span>
                          <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 24">
                            <rect x=".5" fill="#D8DCE7" rx="12"></rect>
                            <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.156 8.03l1.532-1.532a3.404 3.404 0 1 1 4.814 4.816l-2.188 2.189a3.403 3.403 0 0 1-4.814 0"></path>
                            <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.844 15.97l-1.532 1.532a3.406 3.406 0 0 1-4.815 0 3.406 3.406 0 0 1 .001-4.816l2.188-2.189a3.404 3.404 0 0 1 4.814 0"></path>
                          </svg>
                        </span>
                      </div>
                      <input id="input-copy" type="hidden" />
                      <div class="absolute top-0 bg-green-300 text-white duration-300 py-1 px-3 w-max left-[160%] hidden">{{ $t("post.copiedLink") }}</div>
                    </div>
                  </div>
                </div>
                <div class="w-full max-w-8xl mx-auto my-5">
                  <div class="h-[2px] w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
                </div>
                <div id="comment-form" class="max-w-full mt-5">
                  <h1 class="text-2xl font-bold text-gray-600 mb-4 tracking-wide">{{ $t("post.yourThoughts") }}</h1>
                  <form @submit.prevent="handleSubmitComment" class="rounded-md bg-gray-100 overflow-hidden shadow-md">
                    <div class="relative">
                      <Field name="content" as="textarea" class="w-full p-2 text-gray-600 placeholder-gray-500 focus:outline-none resize-none bg-gray-100" :class="{ 'border-red-500': false }" rows="4" :placeholder="replyToId ? $t('post.replyingTo', { user: replyToUser }) : $t('post.whatAreYouThinking')" />
                    </div>
                    <div class="flex items-center justify-between bg-gray-200 px-4 py-2">
                      <div class="flex items-center space-x-4 text-gray-600">
                        <button type="button" class="hidden">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18v14H3V5zm3 8l3 3 4-4 5 5H5z" /></svg>
                        </button>
                        <span v-if="replyToId" class="text-sm text-sky-600">Đang trả lời {{ replyToUser }}</span>
                      </div>
                      <div class="flex items-center space-x-2">
                        <button v-if="replyToId" type="button" @click="cancelReply" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800">Hủy</button>
                        <button type="submit" :disabled="isSubmitting" class="flex items-center justify-center ml-3 text-white rounded-md h-9 w-14 bg-sky-500 hover:bg-sky-600 disabled:opacity-50 xl:ml-5">
                          <svg v-if="!isSubmitting" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.98481 2.44399C3.11333 1.57147 1.15325 3.46979 1.96543 5.36824L3.82086 9.70527C3.90146 9.89367 3.90146 10.1069 3.82086 10.2953L1.96543 14.6323C1.15326 16.5307 3.11332 18.4291 4.98481 17.5565L16.8184 12.0395C18.5508 11.2319 18.5508 8.76865 16.8184 7.961L4.98481 2.44399ZM3.34453 4.77824C3.0738 4.14543 3.72716 3.51266 4.35099 3.80349L16.1846 9.32051C16.762 9.58973 16.762 10.4108 16.1846 10.68L4.35098 16.197C3.72716 16.4879 3.0738 15.8551 3.34453 15.2223L5.19996 10.8853C5.21944 10.8397 5.23735 10.7937 5.2537 10.7473L9.11784 10.7473C9.53206 10.7473 9.86784 10.4115 9.86784 9.99726C9.86784 9.58304 9.53206 9.24726 9.11784 9.24726L5.25157 9.24726C5.2358 9.20287 5.2186 9.15885 5.19996 9.11528L3.34453 4.77824Z" fill="white"></path></svg>
                          <div v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  <div v-if="auth.isAuthenticated === false" class="flex justify-center text-sm gap-1 py-2 text-gray-600">
                    <p>{{ $t("post.loginRequired") }}</p>
                    <NuxtLink :to="TOLOGIN" class="text-sky-500 hover:text-underline">{{ $t("auth.login") }}</NuxtLink>
                  </div>
                </div>
                <!-- list cmt -->
                <div class="max-w-full mx-auto space-y-4 mt-5">
                  <h1 v-if="totalComments > 0" class="text-xl font-bold text-gray-500 mb-4">{{ $t("post.comment") }} ({{ totalComments }})</h1>
                  <div v-if="isCommentLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-500">{{ $t("common.loading") }}</p>
                  </div>
                  <div v-else-if="rootComments.length === 0" class="text-center py-8 text-sm text-gray-500">
                    <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                  </div>
                  <div v-else class="space-y-4">
                    <CommentTree v-for="comment in rootComments" :key="comment.id" :node="comment" :is-root="true" @refresh="handleRefresh" @reply="handleReply" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Sidebar -->
          <div class="col-span-12 lg:col-span-3">
            <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200 mb-3 md:mb-5">
              <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">{{ $t("action.quickView") }}</h2>
              <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-2 gap-2 mb-3 md:mb-4">
                <NuxtLink v-for="post in (hotQuickItems || []).slice(0, 6)" :key="post.id" :to="TOPOSTDETAIL(post.slug as any)" class="group space-y-1 block">
                  <NuxtImg :src="post.image?.url || '/images/posts/post-home-5.jpg'" :alt="post.name" class="rounded-md object-cover w-full h-16 md:w-[145px] md:h-[80px]" loading="lazy" />
                  <span class="text-xs md:text-sm text-gray-800 font-medium group-hover:text-cyan-600 transition-colors line-clamp-2">
                    {{ post.name }}
                  </span>
                </NuxtLink>
              </div>
              <NuxtLink :to="TOPOST" class="block w-full text-center text-xs md:text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded py-2 transition">{{ $t("common.viewAll") }} ({{ (hotQuickItems || []).length }})</NuxtLink>
            </div>
            <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200">
              <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">
                <span class="text-sky-600">#</span>
                {{ $t("post.tags") }}
              </h2>
              <div class="flex flex-wrap gap-2">
                <NuxtLink v-for="tag in tagList || []" :key="tag.id" :to="`/post?tag=${tag.slug}`" class="px-2 md:px-3 py-1 text-xs md:text-[12px] bg-[#d9dfe3] max-w-max rounded font-semibold text-[#7281a3] hover:bg-[#c8d1d9] transition-colors">#{{ tag.name }}</NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section v-if="relateds.length > 0" class="mx-auto bg-white rounded-md shadow sm:py-1 mb-2 md:py-2 px-1 sm:px-3 md:px-6 md:mb-6 sm:mb-3">
        <h2 class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ $t("post.relatedPosts") }}</h2>
        <Splide :options="postRelatedOptions(relateds.length)" id="post-related-slide">
          <SplideSlide v-for="relatedItem in relateds" :key="relatedItem.id">
            <NuxtLink :to="TOPOSTDETAIL(encodeURIComponent(relatedItem.slug))">
              <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-cyan-200 flex flex-col h-full">
                <div class="relative overflow-hidden aspect-[16/9] w-full">
                  <NuxtImg :src="relatedItem.image?.url || '/images/posts/post-home-3.jpg'" :alt="relatedItem.name" class="w-full h-full object-cover object-center rounded-t-xl" loading="lazy" />
                  <div v-if="relatedItem.isHot" class="absolute top-2 left-2 bg-white/10 backdrop-blur-sm rounded-lg px-2 py-1">
                    <span class="text-red-500 font-bold text-xs">{{ $t("post.hot") }}</span>
                  </div>
                </div>
                <div class="p-3 sm:p-4 flex flex-col flex-1">
                  <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-700 mb-2 sm:mb-1 hover:text-cyan-600 transition-colors line-clamp-2 break-words">
                    {{ relatedItem.name }}
                  </h3>
                  <p class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-1 leading-relaxed line-clamp-2 break-words">
                    {{ relatedItem.description }}
                  </p>
                  <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 mt-auto">
                    <span class="font-semibold">{{ relatedItem.creator?.name }}</span>
                    <span>{{ relatedItem.publishedAtHumen }}</span>
                  </div>
                </div>
              </div>
            </NuxtLink>
          </SplideSlide>
        </Splide>
      </section>
    </div>
  </div>
</template>
<script setup lang="ts">
import { Field } from "vee-validate";
import { TOPOST, TOPOSTDETAIL, TOLOGIN } from "~/utils/constants";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import CommentTree from "~/components/features/post/CommentTree.vue";
import PostLikeButton from "~/components/features/post/PostLikeButton.vue";
import { buildToc, type TocItem } from "~/composables/useToc";
import { usePostSlider } from "~/composables/usePostSlider";
import { usePost } from "~/composables/post/usePost";
import { usePostComment } from "~/composables/post/usePostComment";
import { usePostLike } from "~/composables/post/usePostLike";
import { usePostCommentLike } from "~/composables/post/usePostCommentLike";
import { useAuthStore } from "~/stores/auth/auth";
import { useSeo } from "~/composables/useSeo";
import { computed, nextTick, ref, watch, watchEffect } from "vue";
import { onBeforeRouteUpdate, useRoute } from "vue-router";
import { definePageMeta } from "#imports";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const { setPageSeo } = useSeo();
const { postRelatedOptions } = usePostSlider();
const { rootComments, totalComments, isLoading: isCommentLoading, isSubmitting, createComment, resetForm, loadComments } = usePostComment();
const { tagList, hotQuickItems, postDetail, postDetailLoading, postDetailError, fetchTags, loadHotQuick, fetchById, trackView } = usePost();
const { isLiked, likeCount, isLoading: isLikeLoading, toggleLike, getLikeStatus, setCurrent, resetAll: resetPostLikes } = usePostLike();
const { resetAll: resetCommentLikes } = usePostCommentLike();

const auth = useAuthStore();
const isTocOpen = ref(true);
const contentWithIds = ref<string>("");
const toc = ref<TocItem[]>([]);

const replyToId = ref<number | null>(null);
const replyToUser = ref<string | null>(null);

const slug = computed(() => String(useRoute().params.slug || ""));
const relateds = computed(() => (Array.isArray((postDetail.value as any)?.relateds) ? (postDetail.value as any).relateds : []));

let loadToken = 0;

const initForSlug = async (targetSlug: string) => {
  if (!targetSlug) return;
  const token = ++loadToken;
  resetPostLikes();
  resetCommentLikes();
  const common = Promise.all([fetchTags(), loadHotQuick()]);
  await fetchById(targetSlug);
  await common;
  if (token !== loadToken) return;
  const id = postDetail.value?.id;
  if (!id) return;
  setCurrent(id);
  await Promise.all([loadComments(id), getLikeStatus(id)]);
};

onBeforeRouteUpdate((to, from, next) => {
  if (to.params.slug && to.params.slug !== from.params.slug) {
    if (from.name) {
      trackView(postDetail.value?.id || 0);
    }
  }
  next();
});

watch(
  () => postDetail.value?.content,
  (content) => {
    if (content) {
      const { toc: t, html } = buildToc(content);
      toc.value = t;
      contentWithIds.value = html;
    } else {
      toc.value = [];
      contentWithIds.value = "";
    }
  },
  { immediate: true },
);

watch(
  () => slug.value,
  async (newSlug, oldSlug) => {
    if (!newSlug || newSlug === oldSlug) return;
    await initForSlug(newSlug);
  },
  { immediate: true },
);

watch(
  () => auth.isAuthenticated,
  (isAuthenticated) => {
    const id = postDetail.value?.id;
    if (isAuthenticated && id) {
      getLikeStatus(id);
    }
  },
);

const breadcrumbs = computed(() => [
  { label: t("post.breadcrumb"), href: TOPOST },
  { label: postDetail.value?.name || slug.value, href: TOPOSTDETAIL(slug.value) },
]);

const handleRefresh = async () => {
  if (postDetail.value?.id) {
    await loadComments(postDetail.value.id);
  }
};

const handleReply = (parentId: number) => {
  replyToId.value = parentId;
  // tìm tên ng dùng cần reply
  const findCommentUser = (comments: any[], targetId: number): string | null => {
    for (const comment of comments) {
      if (comment.id === targetId) {
        return comment.author?.name || "Người dùng";
      }
      if (comment.replies && comment.replies.length > 0) {
        const found = findCommentUser(comment.replies, targetId);
        if (found) return found;
      }
    }
    return null;
  };

  replyToUser.value = findCommentUser(rootComments.value, parentId);

  nextTick(() => {
    document.getElementById("comment-form")?.scrollIntoView({ behavior: "smooth" });
  });
};

// cancel reply
const cancelReply = () => {
  replyToId.value = null;
  replyToUser.value = null;
  resetForm();
};

const handleSubmitComment = async () => {
  if (!postDetail.value?.id) return;

  const success = await createComment(postDetail.value.id, replyToId.value || undefined);
  if (success) {
    replyToId.value = null;
    replyToUser.value = null;
  }
};

const handleToggleLike = async () => {
  if (!postDetail.value?.id) return;

  await toggleLike(postDetail.value.id);
};

watchEffect(() => {
  if (postDetail.value) {
    setPageSeo({
      title: postDetail.value?.metaTitle || postDetail.value?.name || "Bài viết",
      description: postDetail.value?.metaDescription || postDetail.value?.description || "",
      keywords: postDetail.value?.metaKeyword || "",
      ogTitle: postDetail.value?.name || "Bài viết",
      ogDescription: postDetail.value?.description || "",
      ogImage: postDetail.value?.image?.url || "",
      ogType: "article",
    });
  }
});

definePageMeta({
  layout: "client",
});
</script>
