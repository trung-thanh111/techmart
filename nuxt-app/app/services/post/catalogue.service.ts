import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostCatalogue } from "~/interfaces/post/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/post_catalogues`,
  resource: `${API_VERSION.version1}/post_catalogue`,
} as const;

export const postCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostCatalogue>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPostCatalogue[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue>>(`${endpoint.collection}/${id}`, {
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
};
