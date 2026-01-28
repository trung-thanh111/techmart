import type { Ref } from "vue";
import { postCommentLikeService } from "~/services/post/comment_like.service";
import type { IPostCommentLike } from "~/interfaces/post/comment_like.interface";
import { useAuthStore } from "~/stores/auth/auth";

export const usePostCommentLike = () => {
  const auth = useAuthStore();
  const isLoading: Ref<boolean> = ref(false);
  const error: Ref<string | null> = ref(null);

  // state theo từng comment id
  const commentLikeState = reactive<Record<number, { isLiked: boolean; likeCount: number; currentLikeId: number | null }>>({});

  const ensureState = (commentId: number) => {
    if (!commentLikeState[commentId]) {
      commentLikeState[commentId] = { isLiked: false, likeCount: 0, currentLikeId: null };
    }
    return commentLikeState[commentId];
  };

  const getStatus = async (commentId: number) => {
    const state = ensureState(commentId);
    const userId = auth.user?.id;
    isLoading.value = true;
    error.value = null;
    try {
      const existPromise = auth.isAuthenticated && typeof userId === "number"
        ? postCommentLikeService.checkLiked(commentId, userId)
        : Promise.resolve({ liked: false, records: [] as IPostCommentLike[] });
      const countPromise = postCommentLikeService.listByComment(commentId);

      const [exist, countRes] = await Promise.all([existPromise, countPromise]);

      const payload = countRes?.data;
      const likes: IPostCommentLike[] = Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];

      state.isLiked = !!exist.liked;
      state.currentLikeId = exist.records[0]?.id ?? null;
      state.likeCount = likes.length;
      return true;
    } catch (e: any) {
      error.value = e?.message || "Không lấy được trạng thái like";
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  // reset toàn bộ khi chuyển bài viết khác
  const resetAll = () => {
    Object.keys(commentLikeState).forEach((k) => {
      const key = Number(k);
      commentLikeState[key] = { isLiked: false, likeCount: 0, currentLikeId: null };
    });
  };

  const resetFor = (commentId: number) => {
    commentLikeState[commentId] = { isLiked: false, likeCount: 0, currentLikeId: null };
  };

  const toggle = async (commentId: number) => {
    const state = ensureState(commentId);
    if (!auth.isAuthenticated) {
      error.value = "Vui lòng đăng nhập để thích bình luận";
      return false;
    }
    if (isLoading.value) return false;
    isLoading.value = true;
    error.value = null;
    try {
      if (state.isLiked && state.currentLikeId) {
        await postCommentLikeService.delete(state.currentLikeId);
        state.isLiked = false;
        state.likeCount = Math.max(0, state.likeCount - 1);
        state.currentLikeId = null;
      } else {
        try {
          const res = await postCommentLikeService.store(commentId);
          const data = res.data;
          if (data) {
            state.isLiked = true;
            state.likeCount += 1;
            state.currentLikeId = data.id;
          }
        } catch (err: any) {
          // Duplicate key -> đã like rồi, refresh status thay vì fail UX
          if (String(err?.message || "").includes("Duplicate entry") || String(err?.message || "").includes("23000")) {
            await getStatus(commentId);
            return true;
          }
          throw err;
        }
      }
      return true;
    } catch (e: any) {
      error.value = e?.message || "Không thể thực hiện thao tác";
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    isLoading: readonly(isLoading),
    error: readonly(error),
    commentLikeState: readonly(commentLikeState),
    getStatus,
    toggle,
    resetAll,
    resetFor,
  };
};
