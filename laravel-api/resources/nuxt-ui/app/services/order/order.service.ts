import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IOrder, OrderUpdatePayload } from "~/interfaces/order/order.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/admin";

export const orderService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IOrder>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IOrder>>>(`${ENDPOINT}/orders`, {
            params,
        });
    },

    show: async (id: number | string, params?: Record<string, any>): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${ENDPOINT}/orders/${id}`, {
            params,
        });
    },

    update: async (id: number, payload: OrderUpdatePayload): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${ENDPOINT}/orders/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    updateStatus: async (id: number, status: string, note?: string): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${ENDPOINT}/orders/${id}/status`, {
            method: "POST",
            body: { status, note },
        });
    },

    updateItemStatus: async (orderId: number, itemId: number, status: string): Promise<IApiOk<IOrder>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IOrder>>(`${ENDPOINT}/orders/${orderId}/items/${itemId}/status`, {
            method: "POST",
            body: { status },
        });
    },

    updateRefundStatus: async (orderId: number, refundId: number, status: string): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/orders/${orderId}/refunds/${refundId}/status`, {
            method: "POST",
            body: { status },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/orders/${id}`, {
            method: "DELETE",
        });
    },

    bulkDelete: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/orders/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
};
