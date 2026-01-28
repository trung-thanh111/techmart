import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPromotion } from "~/interfaces/promotion/promotion.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/promotions`,
  resource: `${API_VERSION.version1}/promotion`,
} as const;

export const promotionService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotion>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPromotion>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPromotion[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string, relations?: string[]): Promise<IApiOk<IPromotion>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion>>(`${endpoint.collection}/${id}`, {
      method: "GET",
      params: relations ? { with: relations.join(",") } : undefined,
    });
  },

  store: async (payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IPromotion>): Promise<IApiOk<IPromotion>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion>>(`${endpoint.collection}/${id}`, {
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
};
