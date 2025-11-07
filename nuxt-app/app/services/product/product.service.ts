import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const productService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProduct>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IProduct>>>(`${ENDPOINT}/products`, {
      params,
    });
  },

  store: async (payload: Partial<IProduct>): Promise<IApiOk<IProduct>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProduct>>(`${ENDPOINT}/products`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IProduct>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProduct>>(`${ENDPOINT}/products/${id}`);
  },

  update: async (id: number, payload: Partial<IProduct>): Promise<IApiOk<IProduct>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProduct>>(`${ENDPOINT}/products/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/products/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/products/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IProduct>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProduct>>(`${ENDPOINT}/products?type=all`);
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProduct>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IProduct>>>(`${ENDPOINT}/products`, {
      params,
    });
  },

  getSummerProducts: async (limit = 3): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(`${ENDPOINT}/products`, {
      params: {
        season: "summer",
        publish: 2,
        sort: "id,desc",
        perpage: limit,
      },
    });
  },

  showBySlug: async (slug: string, relations?: string[]): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(`${ENDPOINT}/product/${slug}`, {
      params: relations ? { with: relations } : undefined,
    });
  },

  showPublic: async (id: number | string, relations?: string[]): Promise<IApiOk<IProduct>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct>>(`${ENDPOINT}/products/${id}`, {
      params: relations ? { with: relations } : undefined,
    });
  },
};
