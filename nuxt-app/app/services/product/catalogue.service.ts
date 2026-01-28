import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/product_catalogues`,
  resource: `${API_VERSION.version1}/product_catalogue`,
} as const;

export const productCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IProductCatalogue>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IProductCatalogue[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IProductCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue>>(`${endpoint.collection}/${id}`, {
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
