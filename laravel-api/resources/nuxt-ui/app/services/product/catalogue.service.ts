import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const productCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductCatalogue>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IProductCatalogue>>>(`${ENDPOINT}/product_catalogues`, {
      params,
    });
  },

  store: async (payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductCatalogue>>(`${ENDPOINT}/product_catalogues`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IProductCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductCatalogue>>(`${ENDPOINT}/product_catalogues/${id}`);
  },

  update: async (id: number, payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IProductCatalogue>>(`${ENDPOINT}/product_catalogues/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/product_catalogues/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/product_catalogues/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IProductCatalogue>>>(`${ENDPOINT}/product_catalogues`, {
      params,
    });
  },

  all: async (): Promise<IApiOk<IProductCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue>>(`${ENDPOINT}/product_catalogues?type=all`);
  },

  showBySlug: async (slug: string): Promise<IApiOk<IProductCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProductCatalogue>>(`${ENDPOINT}/product/category/${slug}`);
  },
};
