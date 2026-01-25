import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBrand } from "~/interfaces/brand/brand.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const brandService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBrand>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IBrand>>>(`${ENDPOINT}/brands`, {
      params,
    });
  },

  store: async (payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBrand>>(`${ENDPOINT}/brands`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IBrand>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBrand>>(`${ENDPOINT}/brands/${id}`);
  },

  update: async (id: number, payload: Partial<IBrand>): Promise<IApiOk<IBrand>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBrand>>(`${ENDPOINT}/brands/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/brands/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/brands/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IBrand>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand>>(`${ENDPOINT}/brands?type=all`);
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBrand>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IBrand>>>(`${ENDPOINT}/brands`, {
      params,
    });
  },

  allPublic: async (): Promise<IApiOk<IBrand>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IBrand>>(`${ENDPOINT}/brands?type=all`);
  },
};
