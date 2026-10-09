import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/banners`,
  resource: `${API_VERSION.version1}/banner`,
} as const;

export const bannerService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBanner>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IBanner>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (params?: Record<string, any>): Promise<IApiOk<IBanner[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBanner[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IBanner>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBanner>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBanner>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBanner>>(`${endpoint.collection}/${id}`, {
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

  getByType: async (bannerType: string): Promise<IApiOk<IBanner[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBanner[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        banner_type: bannerType,
        position: "main_slider",
        publish: 2,
        deleted_at: null,
      },
    });
  },

  fetch: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBanner>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IBanner>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },
};
