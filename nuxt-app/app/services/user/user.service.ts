import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import type { IUserCatalogue } from "~/interfaces/user/catalogue.interface";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const useUserService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUser>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IUser>>>(`${ENDPOINT}/users`, {
      params,
    });
  },

  store: async (payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(`${ENDPOINT}/users`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(
      `${ENDPOINT}/users/${id}`
    );
  },

  update: async (id: number, payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(`${ENDPOINT}/users/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PUT" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/users/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/users/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  getAllUserCatalogues: async (): Promise<IApiOk<IUserCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUserCatalogue>>(`${ENDPOINT}/user_catalogues?type=all`);
  },

};
