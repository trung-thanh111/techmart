import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPopup } from "~/interfaces/system/popup.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/popups`,
  resource: `${API_VERSION.version1}/popup`,
} as const;

export const popupService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPopup>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPopup>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPopup[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPopup[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPopup>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPopup>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPopup>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPopup>>(`${endpoint.collection}/${id}`, {
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
