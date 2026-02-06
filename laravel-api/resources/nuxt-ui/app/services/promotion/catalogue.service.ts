import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPromotionCatalogue } from "~/interfaces/promotion/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/promotion_catalogues`,
    resource: `${API_VERSION.version1}/promotion_catalogue`,
} as const;

export const promotionCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotionCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPromotionCatalogue>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPromotionCatalogue>): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPromotionCatalogue>): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${endpoint.collection}?type=all`);
    },
};
