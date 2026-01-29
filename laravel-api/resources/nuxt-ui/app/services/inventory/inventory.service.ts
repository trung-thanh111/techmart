import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IInventory } from "~/interfaces/inventory/inventory.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/admin";

export const inventoryService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IInventory>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IInventory>>>(`${ENDPOINT}/inventorys`, {
            params,
        });
    },

    store: async (payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(`${ENDPOINT}/inventorys`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(`${ENDPOINT}/inventorys/${id}`);
    },

    update: async (id: number, payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IInventory>>(`${ENDPOINT}/inventorys/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/inventorys/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/inventorys/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
