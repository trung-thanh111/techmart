import { ref } from "vue";
import type { IProductReview, IReviewSummary, IStoreReviewPayload } from "~/interfaces/product/product_review.interface";
import { productReviewService } from "~/services/product/product_review.service";

export const useProductReview = () => {
  const loading = ref(false);
  const submitting = ref(false);
  const reviews = ref<IProductReview[]>([]);
  const summary = ref<IReviewSummary | null>(null);
  const pagination = ref({
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
  });

  const fetchReviews = async (productId: number, params?: Record<string, any>) => {
    loading.value = true;
    try {
      const res = await productReviewService.getByProduct(productId, params);
      if (res && res.data) {
        reviews.value = res.data.reviews || [];
        summary.value = res.data.summary || null;
        if (res.data.pagination) {
          pagination.value = res.data.pagination;
        }
      }
    } catch (err) {
      console.error("Error fetching product reviews:", err);
    } finally {
      loading.value = false;
    }
  };

  const submitReview = async (payload: IStoreReviewPayload) => {
    submitting.value = true;
    try {
      const res = await productReviewService.store(payload);
      if (res && res.data) {
        // Tải lại danh sách review sau khi gửi thành công
        await fetchReviews(payload.product_id);
        return { success: true, data: res.data };
      }
      return { success: false, message: "Không thể gửi đánh giá." };
    } catch (err: any) {
      console.error("Error submitting review:", err);
      const msg = err?.data?.message || err?.message || "Đã có lỗi xảy ra.";
      return { success: false, message: msg };
    } finally {
      submitting.value = false;
    }
  };

  const likeReview = async (reviewId: number) => {
    try {
      const res = await productReviewService.like(reviewId);
      if (res && res.data) {
        const item = reviews.value.find((r) => r.id === reviewId);
        if (item) {
          item.likes_count = res.data.likes_count;
        }
      }
    } catch (err) {
      console.error("Error liking review:", err);
    }
  };

  return {
    loading,
    submitting,
    reviews,
    summary,
    pagination,
    fetchReviews,
    submitReview,
    likeReview,
  };
};
