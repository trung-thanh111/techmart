import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IMenu } from "~/interfaces/menu/menu.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/admin";

export const menuService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IMenu>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IMenu>>>(`${ENDPOINT}/menus`, { params });
    },

    store: async (payload: Partial<IMenu>): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(`${ENDPOINT}/menus`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(`${ENDPOINT}/menus/${id}`, {});
    },

    update: async (id: number, payload: Partial<IMenu>): Promise<IApiOk<IMenu>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IMenu>>(`${ENDPOINT}/menus/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/menus/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/menus/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
