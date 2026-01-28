import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAgency } from "~/interfaces/agency/agency.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/agencies`,
  resource: `${API_VERSION.version1}/agency`,
} as const;

export const agencyService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgency>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IAgency>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IAgency[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAgency[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAgency>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAgency>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },
};
