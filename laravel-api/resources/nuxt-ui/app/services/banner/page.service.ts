import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPage } from "~/interfaces/banner/page.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/banner_pages`,
    resource: `${API_VERSION.version1}/banner_page`,
} as const;

export const pageService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPage>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPage>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: payload,
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

    all: async (): Promise<IApiOk<IPage[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage[]>>(`${endpoint.collection}`, {
            params: { type: "all" },
        });
    },
};
