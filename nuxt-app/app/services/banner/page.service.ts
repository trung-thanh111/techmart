import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPage } from "~/interfaces/banner/page.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/banner_pages`,
  resource: `${API_VERSION.version1}/banner_page`,
} as const;

export const pageService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPage>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPage>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPage[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPage[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPage>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPage>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPage>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPage>>(`${endpoint.collection}/${id}`, {
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
