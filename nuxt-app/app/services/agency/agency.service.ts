import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAgency } from "~/interfaces/agency/agency.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const agencyService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgency>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IAgency>>>(`${ENDPOINT}/agencies`, {
      params,
    });
  },

  store: async (payload: Partial<IAgency>): Promise<IApiOk<IAgency>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAgency>>(`${ENDPOINT}/agencies`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAgency>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAgency>>(
      `${ENDPOINT}/agencies/${id}`
    );
  },

  update: async (id: number, payload: Partial<IAgency>): Promise<IApiOk<IAgency>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAgency>>(`${ENDPOINT}/agencies/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/agencies/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/agencies/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  all: async (): Promise<IApiOk<IAgency>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAgency>>(`${ENDPOINT}/agencies?type=all`);
  },

  // FE
  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgency>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IAgency>>>(`${ENDPOINT}/agencies`, {
      params,
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IAgency>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAgency>>(`${ENDPOINT}/agency/${slug}`);
  },
};
