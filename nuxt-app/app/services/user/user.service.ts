import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/users`,
  resource: `${API_VERSION.version1}/user`,
} as const;

export const userService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUser>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IUser>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IUser[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUser[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUser>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUser>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
      method: "DELETE",
      body: { ids },
    });
  },
};

export const useUserService = userService;
