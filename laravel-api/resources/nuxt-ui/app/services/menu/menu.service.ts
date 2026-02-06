import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IMenu } from "~/interfaces/menu/menu.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/menus`,
    resource: `${API_VERSION.version1}/menu`,
} as const;

export const menuService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IMenu>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IMenu>>>(endpoint.collection, { params });
    },

    store: async (payload: Partial<IMenu>): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(`${endpoint.collection}/${id}`, {});
    },

    update: async (id: number, payload: Partial<IMenu>): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(`${endpoint.collection}/${id}`, {
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
};
