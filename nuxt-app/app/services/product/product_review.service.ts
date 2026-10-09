import type { IApiOk } from "~/interfaces/base.interface";
import type { IProductReview, IProductReviewResponse, IStoreReviewPayload } from "~/interfaces/product/product_review.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

export const productReviewService = {
  getByProduct: async (productId: number, params?: Record<string, any>): Promise<IApiOk<IProductReviewResponse>> => {
    const { fetcher } = useApi();
    return await fetcher<IApiOk<IProductReviewResponse>>(`${API_VERSION.version1}/products/${productId}/reviews`, {
      method: "GET",
      params,
    });
  },

  store: async (payload: IStoreReviewPayload): Promise<IApiOk<IProductReview>> => {
    const { fetcher } = useApi();
    return await fetcher<IApiOk<IProductReview>>(`${API_VERSION.version1}/products/${payload.product_id}/reviews`, {
      method: "POST",
      body: payload,
    });
  },

  like: async (reviewId: number): Promise<IApiOk<{ id: number; likes_count: number }>> => {
    const { fetcher } = useApi();
    return await fetcher<IApiOk<{ id: number; likes_count: number }>>(`${API_VERSION.version1}/product-reviews/${reviewId}/like`, {
      method: "POST",
    });
  },
};
