import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPromotion } from "~/interfaces/promotion/promotion.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/promotions`,
    resource: `${API_VERSION.version1}/promotion`,
} as const;

export const promotionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotion>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPromotion>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string, relations?: string[]): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(`${endpoint.collection}/${id}`, {
            params: relations ? { with: relations.join(",") } : undefined,
        });
    },

    update: async (id: number, payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: formData(payload),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
