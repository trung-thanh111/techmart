import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductTag } from "~/interfaces/product/tag.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const productTagService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductTag>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IProductTag>>>(`${ENDPOINT}/product_tags`, {
      params,
    });
  },

  store: async (payload: Partial<IProductTag>): Promise<IApiOk<IProductTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductTag>>(`${ENDPOINT}/product_tags`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IProductTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductTag>>(`${ENDPOINT}/product_tags/${id}`);
  },

  update: async (id: number, payload: Partial<IProductTag>): Promise<IApiOk<IProductTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductTag>>(`${ENDPOINT}/product_tags/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/product_tags/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/product_tags/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IProductTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductTag>>(`${ENDPOINT}/product_tags?type=all`);
  },
};
