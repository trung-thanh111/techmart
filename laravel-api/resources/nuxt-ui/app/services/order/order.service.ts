import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IOrder, OrderUpdatePayload } from "~/interfaces/order/order.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/orders`,
    resource: `${API_VERSION.version1}/order`,
} as const;

export const orderService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IOrder>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IOrder>>>(endpoint.collection, {
            params,
        });
    },

    show: async (id: number | string, params?: Record<string, any>): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${id}`, {
            params,
        });
    },

    update: async (id: number, payload: OrderUpdatePayload): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    updateStatus: async (id: number, status: string, note?: string): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${id}/status`, {
            method: "POST",
            body: { status, note },
        });
    },

    updateItemStatus: async (orderId: number, itemId: number, status: string): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${orderId}/items/${itemId}/status`, {
            method: "POST",
            body: { status },
        });
    },

    updateRefundStatus: async (orderId: number, refundId: number, status: string): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${orderId}/refunds/${refundId}/status`, {
            method: "POST",
            body: { status },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    bulkDelete: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
