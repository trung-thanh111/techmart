import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAgencyWorkingHour } from "~/interfaces/agency/working_hour.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/agency_working_hours`,
  resource: `${API_VERSION.version1}/agency_working_hour`,
} as const;

export const agencyWorkingHourService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgencyWorkingHour>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IAgencyWorkingHour>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IAgencyWorkingHour[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAgencyWorkingHour[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAgencyWorkingHour>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAgencyWorkingHour>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },
};
