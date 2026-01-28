import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPermission } from "~/interfaces/user/permission.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/permissions`,
  resource: `${API_VERSION.version1}/permission`,
} as const;

export const permissionService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPermission>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPermission>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPermission[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPermission[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPermission>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPermission>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPermission>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPermission>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: payload,
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
