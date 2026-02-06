import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IMenuItem } from "~/interfaces/menu/menu.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/menu_items`,
    resource: `${API_VERSION.version1}/menu_item`,
} as const;

export const menuItemService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IMenuItem>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IMenuItem>>>(endpoint.collection, { params });
    },

    store: async (payload: Partial<IMenuItem>): Promise<IApiOk<IMenuItem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    bulkStore: async (data: any[]): Promise<IApiOk<IMenuItem[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem[]>>(`${endpoint.collection}/bulkStore`, {
            method: "POST",
            body: { data },
        });
    },

    update: async (id: number, payload: Partial<IMenuItem>): Promise<IApiOk<IMenuItem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem>>(`${endpoint.collection}/${id}`, {
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

    rebuildTree: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/rebuild-tree`, {
            method: "POST",
            body: payload,
        });
    },

    dragDrop: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/drag-drop`, {
            method: "POST",
            body: payload,
        });
    },
};
