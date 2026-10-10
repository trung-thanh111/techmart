import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductReview } from "~/interfaces/product/review.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/product_reviews`,
} as const;

export const productReviewService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductReview>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IProductReview>>>(endpoint.collection, {
            params,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IProductReview>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductReview>>(`${endpoint.collection}/${id}`);
    },

    updateStatus: async (id: number | string, publish: number): Promise<IApiOk<IProductReview>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductReview>>(`${endpoint.collection}/${id}/status`, {
            method: "PUT",
            body: { publish },
        });
    },

    bulkStatus: async (ids: number[], publish: number): Promise<IApiOk<{ updated_count: number; publish: number }>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<{ updated_count: number; publish: number }>>(`${endpoint.collection}/bulkStatus`, {
            method: "POST",
            body: { ids, publish },
        });
    },

    delete: async (id: number | string): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
            method: "DELETE",
            body: { ids },
        });
    },
};
