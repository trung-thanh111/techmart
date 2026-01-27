import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPromotionCatalogue } from "~/interfaces/promotion/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const promotionCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotionCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPromotionCatalogue>>>(`${ENDPOINT}/promotion_catalogues`, {
            params,
        });
    },

    store: async (payload: Partial<IPromotionCatalogue>): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${ENDPOINT}/promotion_catalogues`, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${ENDPOINT}/promotion_catalogues/${id}`);
    },

    update: async (id: number, payload: Partial<IPromotionCatalogue>): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${ENDPOINT}/promotion_catalogues/${id}`, {
            method: "POST",
            body: formData(payload),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/promotion_catalogues/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/promotion_catalogues/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IPromotionCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPromotionCatalogue>>(`${ENDPOINT}/promotion_catalogues?type=all`);
    },
};
