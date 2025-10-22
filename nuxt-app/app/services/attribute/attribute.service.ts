import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAttribute } from "~/interfaces/attribute/attribute.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const attributeService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAttribute>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IAttribute>>>(`${ENDPOINT}/attributes`, {
      params,
    });
  },

  store: async (payload: Partial<IAttribute>): Promise<IApiOk<IAttribute>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttribute>>(`${ENDPOINT}/attributes`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IAttribute>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttribute>>(
      `${ENDPOINT}/attributes/${id}`
    );
  },

  update: async (id: number, payload: Partial<IAttribute>): Promise<IApiOk<IAttribute>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttribute>>(`${ENDPOINT}/attributes/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/attributes/${id}`, {
      method: "DELETE"
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/attributes/bulkDelete`, {
      body: { ids },
      method: "DELETE"
    });
  },

  all: async (): Promise<IApiOk<IAttribute>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAttribute>>(`${ENDPOINT}/attributes?type=all`);
  },

  getAttributesByCatalogue: async (catalogueId: number): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAttribute>>(`${ENDPOINT}/attributes?type=all&attribute_catalogue_id=${catalogueId}`);

  },
};
