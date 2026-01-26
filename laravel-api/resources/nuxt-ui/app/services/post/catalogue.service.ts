import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostCatalogue } from "~/interfaces/post/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const postCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostCatalogue>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IPostCatalogue>>>(`${ENDPOINT}/post_catalogues`, {
      params,
    });
  },

  store: async (payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostCatalogue>>(`${ENDPOINT}/post_catalogues`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostCatalogue>>(`${ENDPOINT}/post_catalogues/${id}`);
  },

  update: async (id: number, payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostCatalogue>>(`${ENDPOINT}/post_catalogues/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_catalogues/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_catalogues/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IPostCatalogue[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue[]>>(`${ENDPOINT}/post_catalogues?type=all&publish=2`);
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostCatalogue>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostCatalogue>>>(`${ENDPOINT}/post_catalogues`, {
      params,
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IPostCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCatalogue>>(`${ENDPOINT}/post/${slug}`);
  },
};
