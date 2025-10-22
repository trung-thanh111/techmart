import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IFaqCatalogue } from "~/interfaces/faq/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const faqCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaqCatalogue>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IFaqCatalogue>>>(`${ENDPOINT}/faq_catalogues`, {
      params,
    });
  },

  store: async (payload: Partial<IFaqCatalogue>): Promise<IApiOk<IFaqCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaqCatalogue>>(`${ENDPOINT}/faq_catalogues`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IFaqCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaqCatalogue>>(`${ENDPOINT}/faq_catalogues/${id}`);
  },

  update: async (id: number, payload: Partial<IFaqCatalogue>): Promise<IApiOk<IFaqCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaqCatalogue>>(`${ENDPOINT}/faq_catalogues/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/faq_catalogues/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/faq_catalogues/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IFaqCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaqCatalogue>>(`${ENDPOINT}/faq_catalogues?type=all`);
  },

  // FE
  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaqCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IFaqCatalogue>>>(`${ENDPOINT}/faq_catalogues`, {
      params,
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IFaqCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaqCatalogue>>(`${ENDPOINT}/faq-catalogue/${slug}`);
  },
};
