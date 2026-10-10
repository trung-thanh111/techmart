import type { IApiOk } from "~/interfaces/base.interface";
import type { IProductReview, IProductReviewResponse, IStoreReviewPayload } from "~/interfaces/product/product_review.interface";
import { useApi } from "~/composables/useApi";
import { useAuthStore } from "~/stores/auth/auth";
import { API_VERSION } from "~/utils/apiConfig";

const getFetcher = () => {
  const { publicFetch, privateFetch } = useApi();
  const auth = useAuthStore();
  return auth.accessToken ? privateFetch : publicFetch;
};

export const productReviewService = {
  getByProduct: async (productId: number, params?: Record<string, any>): Promise<IApiOk<IProductReviewResponse>> => {
    const fetcher = getFetcher();
    return await fetcher<IApiOk<IProductReviewResponse>>(`${API_VERSION.version1}/products/${productId}/reviews`, {
      method: "GET",
      params,
    });
  },

  store: async (payload: IStoreReviewPayload): Promise<IApiOk<IProductReview>> => {
    const fetcher = getFetcher();
    return await fetcher<IApiOk<IProductReview>>(`${API_VERSION.version1}/products/${payload.product_id}/reviews`, {
      method: "POST",
      body: payload,
    });
  },

  like: async (reviewId: number): Promise<IApiOk<{ id: number; likes_count: number }>> => {
    const fetcher = getFetcher();
    return await fetcher<IApiOk<{ id: number; likes_count: number }>>(`${API_VERSION.version1}/product-reviews/${reviewId}/like`, {
      method: "POST",
    });
  },
};
