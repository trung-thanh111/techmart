import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAttribute } from "~/interfaces/attribute/attribute.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/attributes`,
  resource: `${API_VERSION.version1}/attribute`,
} as const;

export const attributeService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAttribute>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IAttribute>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IAttribute[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAttribute[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAttribute>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAttribute>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  getAttributesByCatalogue: async (catalogueId: number): Promise<IApiOk<IAttribute[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAttribute[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        attribute_catalogue_id: catalogueId,
      },
    });
  },
};
