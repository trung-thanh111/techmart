import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IBanner } from "~/interfaces/banner/banner.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const bannerService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IBanner>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IBanner>>>(`${ENDPOINT}/banners`, {
      params,
    });
  },

  store: async (payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBanner>>(`${ENDPOINT}/banners`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IBanner>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBanner>>(
      `${ENDPOINT}/banners/${id}`
    );
  },

  update: async (id: number, payload: Partial<IBanner>): Promise<IApiOk<IBanner>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBanner>>(`${ENDPOINT}/banners/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/banners/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/banners/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  all: async (): Promise<IApiOk<IBanner>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IBanner>>(`${ENDPOINT}/banners?type=all`);
  },
};
