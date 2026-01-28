import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBrand } from "~/interfaces/brand/brand.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/brands`,
  resource: `${API_VERSION.version1}/brand`,
} as const;

export const brandService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBrand>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IBrand>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  store: async (payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IBrand>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  update: async (id: number, payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: formData(payload),
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

  all: async (): Promise<IApiOk<IBrand[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  allPublic: async (limit = 10): Promise<IApiOk<IBrand[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand[]>>(endpoint.collection, {
      method: "GET",
      params: {
        perpage: limit,
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },
};
