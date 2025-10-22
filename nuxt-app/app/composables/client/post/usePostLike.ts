import type { Ref } from "vue";
import { postLikeService } from "~/services/post/post_like.service";
import type { IPostLike } from "~/interfaces/post/post_like.interface";
import { useAuthStore } from "~/stores/auth/auth";

export const usePostLike = () => {
  const auth = useAuthStore();
  const isLoading: Ref<boolean> = ref(false);
  const error: Ref<string | null> = ref(null);

  const postLikeState = reactive<Record<number, { isLiked: boolean; likeCount: number; currentLikeId: number | null }>>({});
  const currentPostId: Ref<number | null> = ref(null);

  const ensureState = (postId: number) => {
    if (!postLikeState[postId]) {
      postLikeState[postId] = { isLiked: false, likeCount: 0, currentLikeId: null };
    }
    return postLikeState[postId];
  };

  const setCurrent = (postId: number | null) => {
    currentPostId.value = postId;
    if (postId != null) ensureState(postId);
  };

  const isLiked = computed(() => {
    const pid = currentPostId.value;
    return pid != null ? !!postLikeState[pid]?.isLiked : false;
  });

  const likeCount = computed(() => {
    const pid = currentPostId.value;
    return pid != null ? Number(postLikeState[pid]?.likeCount ?? 0) : 0;
  });

  /**
   * Toggle like/unlike cho bài viết
   */
  const toggleLike = async (postId: number): Promise<boolean> => {
    if (!auth.isAuthenticated) {
      error.value = "Vui lòng đăng nhập để thích bài viết";
      return false;
    }

    if (isLoading.value) return false;

    isLoading.value = true;
    error.value = null;

    try {
      const state = ensureState(postId);
      if (state.isLiked && state.currentLikeId) {
        await postLikeService.delete(state.currentLikeId);
        state.isLiked = false;
        state.likeCount = Math.max(0, state.likeCount - 1);
        state.currentLikeId = null;
      } else {
        try {
          const response = await postLikeService.store(postId);
          const data = response.data;

          if (data) {
            state.isLiked = true;
            state.likeCount += 1;
            state.currentLikeId = data.id;
          }
        } catch (err: any) {
          if (String(err?.message || "").includes("Duplicate entry") || String(err?.message || "").includes("23000")) {
            await getLikeStatus(postId);
            return true;
          }
          throw err;
        }
      }
      return true;
    } catch (err: any) {
      error.value = err?.message || "Có lỗi xảy ra khi thích bài viết";
      console.error("Error toggling like:", err);
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy trạng thái like của bài viết
   */
  const getLikeStatus = async (postId: number): Promise<boolean> => {
    const state = ensureState(postId);
    isLoading.value = true;
    error.value = null;

    try {
      const userId = auth.user?.id;
      const existPromise = auth.isAuthenticated && typeof userId === "number"
        ? postLikeService.checkLiked(postId, userId)
        : Promise.resolve({ liked: false, records: [] as IPostLike[] });
      const countPromise = postLikeService.list({ post_id: postId, type: 'all' });

      const [exist, countRes] = await Promise.all([existPromise, countPromise]);

      const payload = countRes?.data;
      let allLikes: IPostLike[] = Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];
      allLikes = allLikes.filter((x: any) => Number((x as any)?.postId ?? (x as any)?.post_id) === postId);

      state.isLiked = !!exist.liked;
      state.currentLikeId = (exist.records[0] as unknown as IPostLike | undefined)?.id ?? null;
      state.likeCount = allLikes.length;

      return true;
    } catch (err: any) {
      error.value = err?.message || "Có lỗi xảy ra khi lấy trạng thái like";
      console.error("Error getting like status:", err);
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Reset state
   */
  const reset = (postId?: number) => {
    if (typeof postId === "number") {
      postLikeState[postId] = { isLiked: false, likeCount: 0, currentLikeId: null };
    } else {
      Object.keys(postLikeState).forEach((k) => {
        const key = Number(k);
        postLikeState[key] = { isLiked: false, likeCount: 0, currentLikeId: null };
      });
    }
    isLoading.value = false;
    error.value = null;
  };

  const resetAll = () => {
    currentPostId.value = null;
    Object.keys(postLikeState).forEach((k) => delete postLikeState[Number(k)]);
    isLoading.value = false;
    error.value = null;
  };

  return {
    isLiked: readonly(isLiked),
    likeCount: readonly(likeCount),
    isLoading: readonly(isLoading),
    error: readonly(error),
    setCurrent,
    toggleLike,
    getLikeStatus,
    reset,
    resetAll,
  };
};
