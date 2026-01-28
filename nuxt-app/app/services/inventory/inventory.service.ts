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
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IInventory>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IInventory[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IInventory[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IInventory>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IInventory>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IInventory>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IInventory>): Promise<IApiOk<IInventory>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IInventory>>(`${endpoint.collection}/${id}`, {
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
};
