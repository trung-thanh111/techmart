import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IMenuItem } from "~/interfaces/menu/menu.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const menuItemService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IMenuItem>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IMenuItem>>>(`${ENDPOINT}/menu_items`, { params });
    },

    store: async (payload: Partial<IMenuItem>): Promise<IApiOk<IMenuItem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem>>(`${ENDPOINT}/menu_items`, {
            method: "POST",
            body: payload,
        });
    },

    bulkStore: async (data: any[]): Promise<IApiOk<IMenuItem[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem[]>>(`${ENDPOINT}/menu_items/bulkStore`, {
            method: "POST",
            body: { data },
        });
    },

    update: async (id: number, payload: Partial<IMenuItem>): Promise<IApiOk<IMenuItem>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenuItem>>(`${ENDPOINT}/menu_items/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/menu_items/${id}`, {
            method: "DELETE",
        });
    },

    rebuildTree: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/menu_items/rebuild-tree`, {
            method: "POST",
            body: payload,
        });
    },

    dragDrop: async (payload: any): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/menu_items/drag-drop`, {
            method: "POST",
            body: payload,
        });
    },
};
