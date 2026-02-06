import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBrand } from "~/interfaces/brand/brand.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/brands`,
    resource: `${API_VERSION.version1}/brand`,
} as const;

export const brandService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBrand>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IBrand>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBrand>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IBrand>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBrand>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBrand>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IBrand>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBrand>>(`${endpoint.collection}?type=all`);
    },
};
