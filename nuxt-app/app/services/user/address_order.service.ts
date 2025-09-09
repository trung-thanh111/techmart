import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAddressOrder } from "~/interfaces/user/address_order.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const useAddressOrderService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAddressOrder>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IAddressOrder>>>(`${ENDPOINT}/address_orders`, {
      params,
    });
  },

  store: async (payload: Partial<IAddressOrder>): Promise<IApiOk<IAddressOrder>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAddressOrder>>(`${ENDPOINT}/address_orders`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAddressOrder>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAddressOrder>>(
      `${ENDPOINT}/address_orders/${id}`
    );
  },

  update: async (id: number, payload: Partial<IAddressOrder>): Promise<IApiOk<IAddressOrder>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAddressOrder>>(`${ENDPOINT}/address_orders/${id}`, {
      method: "PUT",
      body: payload,
    });
  },
  
  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/address_orders/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/address_orders/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },
  
};
