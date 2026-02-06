import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { ISystem } from "~/interfaces/system/system.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/systems`,
    resource: `${API_VERSION.version1}/system`,
} as const;

export const systemService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<ISystem>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<ISystem>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<ISystem>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<ISystem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<ISystem>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<ISystem>>(`${endpoint.collection}/${id}`, {
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

    bulkStore: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkStore`, {
            method: "POST",
            body: payload,
        });
    },

    bulkUpdate: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkUpdate`, {
            method: "PUT",
            body: payload,
        });
    },

    all: async (): Promise<IApiOk<ISystem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<ISystem>>(`${endpoint.collection}?type=all`);
    },
};
