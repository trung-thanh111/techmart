import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IOrder } from "~/interfaces/order/order.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

import { useAuthStore } from "~/stores/auth/auth";

const endpoint = {
  collection: `${API_VERSION.version1}/orders`,
} as const;

export const orderService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IOrder>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IOrder>>>(endpoint.collection, {
      method: "GET",
      params: {
        sort: "id,desc",
        ...params,
      },
    });
  },

  store: async (payload: Partial<IOrder>): Promise<IApiOk<IOrder>> => {
    const auth = useAuthStore();
    const { publicFetch, privateFetch } = useApi();
    const fetcher = auth.accessToken ? privateFetch : publicFetch;
    return await fetcher<IApiOk<IOrder>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string, params?: any): Promise<IApiOk<IOrder>> => {
    const auth = useAuthStore();
    const { publicFetch, privateFetch } = useApi();
    const fetcher = auth.accessToken ? privateFetch : publicFetch;
    return await fetcher<IApiOk<IOrder>>(`${API_VERSION.version1}/order/${id}`, {
      method: "GET",
      params,
    });
  },

  cancel: async (id: number, reason?: string): Promise<IApiOk<IOrder>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${id}/cancel`, {
      method: "POST",
      body: { reason },
    });
  },

  updateNote: async (id: number, customerNote: string): Promise<IApiOk<IOrder>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IOrder>>(`${endpoint.collection}/${id}/note`, {
      method: "PATCH",
      body: { customerNote },
    });
  },

  track: async (payload: { code: string; phone?: string; email?: string }): Promise<IApiOk<IOrder>> => {
    const auth = useAuthStore();
    const { publicFetch, privateFetch } = useApi();
    const fetcher = auth.accessToken ? privateFetch : publicFetch;
    return await fetcher<IApiOk<IOrder>>(`${endpoint.collection}/track`, {
      method: "POST",
      body: payload,
    });
  },
};

