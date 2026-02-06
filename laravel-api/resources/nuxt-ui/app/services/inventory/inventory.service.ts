import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IInventory } from "~/interfaces/inventory/inventory.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/inventorys`,
    resource: `${API_VERSION.version1}/inventory`,
} as const;

export const inventoryService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IInventory>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IInventory>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(`${endpoint.collection}/${id}`, {
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
