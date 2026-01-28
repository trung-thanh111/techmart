import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { ISystem } from "~/interfaces/system/system.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/systems`,
  resource: `${API_VERSION.version1}/system`,
} as const;

export const systemService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<ISystem>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<ISystem>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<ISystem[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<ISystem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem>>(`${endpoint.collection}/${id}`, {
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

  bulkStore: async (payload: any): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${endpoint.collection}/bulkStore`, {
      method: "POST",
      body: payload,
    });
  },

  bulkUpdate: async (payload: any): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${endpoint.collection}/bulkUpdate`, {
      method: "POST",
      body: payload, // Usually bulkUpdate implies list of updates
      params: { _method: "PATCH" },
    });
  },

  findByKeyword: async (keyword: string): Promise<IApiOk<ISystem[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem[]>>(endpoint.collection, {
      method: "GET",
      params: {
        keyword,
        type: "all",
        publish: 2,
        deleted_at: null,
      },
    });
  },
};
