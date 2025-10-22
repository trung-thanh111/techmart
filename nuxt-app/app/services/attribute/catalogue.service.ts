import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAttributeCatalogue } from "~/interfaces/attribute/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const attributeCatalogueService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAttributeCatalogue>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IAttributeCatalogue>>>(`${ENDPOINT}/attribute_catalogues`, {
      params,
    });
  },

  store: async (payload: Partial<IAttributeCatalogue>): Promise<IApiOk<IAttributeCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttributeCatalogue>>(`${ENDPOINT}/attribute_catalogues`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAttributeCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttributeCatalogue>>(
      `${ENDPOINT}/attribute_catalogues/${id}`
    );
  },

  update: async (id: number, payload: Partial<IAttributeCatalogue>): Promise<IApiOk<IAttributeCatalogue>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttributeCatalogue>>(`${ENDPOINT}/attribute_catalogues/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/attribute_catalogues/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/attribute_catalogues/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  all: async (): Promise<IApiOk<IAttributeCatalogue>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAttributeCatalogue>>(
      `${ENDPOINT}/attribute_catalogues?type=all`
    );
  },
};
