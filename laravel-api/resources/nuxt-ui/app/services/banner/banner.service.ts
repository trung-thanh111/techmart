import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/banners`,
    resource: `${API_VERSION.version1}/banner`,
} as const;

export const bannerService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBanner>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IBanner>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBanner>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IBanner>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBanner>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBanner>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IBanner>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IBanner>>(`${endpoint.collection}?type=all`);
    },
};
