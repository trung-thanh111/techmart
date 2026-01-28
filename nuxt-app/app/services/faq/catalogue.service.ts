import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IFaqCatalogue } from "~/interfaces/faq/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/faq_catalogues`,
  resource: `${API_VERSION.version1}/faq_catalogue`,
} as const;

export const faqCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaqCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IFaqCatalogue>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IFaqCatalogue[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaqCatalogue[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IFaqCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaqCatalogue>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IFaqCatalogue>): Promise<IApiOk<IFaqCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaqCatalogue>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IFaqCatalogue>): Promise<IApiOk<IFaqCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaqCatalogue>>(`${endpoint.collection}/${id}`, {
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
