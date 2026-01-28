import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPosition } from "~/interfaces/banner/position.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/banner_positions`,
  resource: `${API_VERSION.version1}/banner_position`,
} as const;

export const positionService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPosition>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPosition>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPosition[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPosition[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPosition>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPosition>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPosition>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPosition>>(`${endpoint.collection}/${id}`, {
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
