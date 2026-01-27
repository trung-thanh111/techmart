import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPromotion } from "~/interfaces/promotion/promotion.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const promotionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotion>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPromotion>>>(`${ENDPOINT}/promotions`, {
            params,
        });
    },

    store: async (payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(`${ENDPOINT}/promotions`, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string, relations?: string[]): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(`${ENDPOINT}/promotions/${id}`, {
            params: relations ? { with: relations.join(",") } : undefined,
        });
    },

    update: async (id: number, payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotion>>(`${ENDPOINT}/promotions/${id}`, {
            method: "POST",
            body: formData(payload),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/promotions/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/promotions/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
