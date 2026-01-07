import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { ISystem } from "~/interfaces/system/system.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const systemService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<ISystem>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<ISystem>>>(`${ENDPOINT}/systems`, {
      params,
    });
  },

  store: async (payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ISystem>>(`${ENDPOINT}/systems`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<ISystem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ISystem>>(`${ENDPOINT}/systems/${id}`);
  },

  update: async (id: number, payload: Partial<ISystem>): Promise<IApiOk<ISystem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ISystem>>(`${ENDPOINT}/systems/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/systems/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/systems/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  bulkStore: async (payload: any): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/systems/bulkStore`, {
      method: "POST",
      body: payload,
    });
  },

  bulkUpdate: async (payload: any): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/systems/bulkUpdate`, {
      method: "PUT",
      body: payload,
    });
  },

  all: async (): Promise<IApiOk<ISystem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ISystem>>(`${ENDPOINT}/systems?type=all`);
  },

  findByKeyword: async (keyword: string): Promise<IApiOk<ISystem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ISystem>>(`${ENDPOINT}/policy/${keyword}`);
  },
};
