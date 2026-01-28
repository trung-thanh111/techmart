import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUserCatalogue } from "~/interfaces/user/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/user_catalogues`,
  resource: `${API_VERSION.version1}/user_catalogue`,
} as const;

export const userCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUserCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IUserCatalogue>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IUserCatalogue[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUserCatalogue[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IUserCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUserCatalogue>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUserCatalogue>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IUserCatalogue>>(`${endpoint.collection}/${id}`, {
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

  getPermissions: async (): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${API_VERSION.version1}/permissions`, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
      },
    });
  },

  attach: async (payload: any): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${endpoint.collection}/attach-permission`, {
      method: "POST",
      body: payload,
    });
  },

  detach: async (payload: any): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${endpoint.collection}/detach-permission`, {
      method: "POST",
      body: payload,
    });
  },
};
