import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAddressOrder } from "~/interfaces/user/address_order.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/address_deliveries`,
  resource: `${API_VERSION.version1}/address_delivery`,
} as const;

export const addressOrderService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAddressOrder>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IAddressOrder>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  store: async (payload: Partial<IAddressOrder>): Promise<IApiOk<IAddressOrder>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAddressOrder>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAddressOrder>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAddressOrder>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  update: async (id: number, payload: Partial<IAddressOrder>): Promise<IApiOk<IAddressOrder>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAddressOrder>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
      method: "DELETE",
      body: { ids },
    });
  },

  addressUserById: async (userId: number): Promise<IApiOk<IAddressOrder[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAddressOrder[]>>(endpoint.collection, {
      method: "GET",
      params: {
        user_id: userId,
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },
};
