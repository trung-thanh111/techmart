import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUserCatalogue } from "~/interfaces/user/catalogue.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const userCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUserCatalogue>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IUserCatalogue>>>(`${ENDPOINT}/user_catalogues`, {
      params,
    });
  },

  store: async (payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUserCatalogue>>(`${ENDPOINT}/user_catalogues`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IUserCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUserCatalogue>>(
      `${ENDPOINT}/user_catalogues/${id}`
    );
  },

  update: async (id: number, payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUserCatalogue>>(`${ENDPOINT}/user_catalogues/${id}`, {
      method: "PATCH",
      body: payload,
    });
  },
  
  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/user_catalogues/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/user_catalogues/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },
};
