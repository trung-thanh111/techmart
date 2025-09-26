import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IFaq } from "~/interfaces/faq/faq.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const faqService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaq>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IFaq>>>(`${ENDPOINT}/faqs`, {
      params,
    });
  },

  // FE public
  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IFaq>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IFaq>>>(`${ENDPOINT}/faqs`, {
      params,
    });
  },

  store: async (payload: Partial<IFaq>): Promise<IApiOk<IFaq>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaq>>(`${ENDPOINT}/faqs`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IFaq>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaq>>(
      `${ENDPOINT}/faqs/${id}`
    );
  },

  update: async (id: number, payload: Partial<IFaq>): Promise<IApiOk<IFaq>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaq>>(`${ENDPOINT}/faqs/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/faqs/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/faqs/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  all: async (): Promise<IApiOk<IFaq>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IFaq>>(`${ENDPOINT}/faqs?type=all`);
  },

  showBySlug: async (slug: string): Promise<IApiOk<IFaq>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IFaq>>(`${ENDPOINT}/faq/${slug}`);
  },
};
