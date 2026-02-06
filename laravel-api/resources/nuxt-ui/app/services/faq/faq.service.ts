import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IFaq } from "~/interfaces/faq/faq.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/faqs`,
    resource: `${API_VERSION.version1}/faq`,
} as const;

export const faqService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaq>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IFaq>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IFaq>): Promise<IApiOk<IFaq>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IFaq>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IFaq>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IFaq>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IFaq>): Promise<IApiOk<IFaq>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IFaq>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IFaq>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IFaq>>(`${endpoint.collection}?type=all`);
    },
};
