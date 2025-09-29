import type { Ref } from "vue";
import { postLikeService } from "~/services/post/post-like.service";
import type { IPostLike } from "~/interfaces/post/post-like.interface";
import { useAuthStore } from "~/stores/auth/auth";

export const usePostLike = () => {
  const auth = useAuthStore();
  const isLiked: Ref<boolean> = ref(false);
  const likeCount: Ref<number> = ref(0);
  const isLoading: Ref<boolean> = ref(false);
  const error: Ref<string | null> = ref(null);
  const currentLikeId: Ref<number | null> = ref(null);

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
      if (isLiked.value && currentLikeId.value) {
        await postLikeService.delete(currentLikeId.value);
        isLiked.value = false;
        likeCount.value = Math.max(0, likeCount.value - 1);
        currentLikeId.value = null;
      } else {
        const response = await postLikeService.store(postId);
        const data = response.data;

        if (data) {
          isLiked.value = true;
          likeCount.value += 1;
          currentLikeId.value = data.id;
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
    if (!auth.isAuthenticated) {
      isLiked.value = false;
      likeCount.value = 0;
      currentLikeId.value = null;
      return false;
    }

    isLoading.value = true;
    error.value = null;

    try {
      const response = await postLikeService.getUserLikes(postId);
      const likes = response.data || [];

      const userLike = likes.find((like) => like.userId === auth.user?.id);

      if (userLike) {
        isLiked.value = true;
        currentLikeId.value = userLike.id;
      } else {
        isLiked.value = false;
        currentLikeId.value = null;
      }
      likeCount.value = likes.length;

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
  const reset = () => {
    isLiked.value = false;
    likeCount.value = 0;
    isLoading.value = false;
    error.value = null;
    currentLikeId.value = null;
  };

  return {
    isLiked: readonly(isLiked),
    likeCount: readonly(likeCount),
    isLoading: readonly(isLoading),
    error: readonly(error),
    toggleLike,
    getLikeStatus,
    reset,
  };
};
