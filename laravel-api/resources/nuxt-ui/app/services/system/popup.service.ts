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
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPopup>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(endpoint.collection, {
            method: "POST",
            body: formData(payload as Record<string, any>),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: formData(payload as Record<string, any>),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
    all: async (params?: Record<string, any>): Promise<IApiOk<IPopup[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup[]>>(endpoint.collection, {
            params: { type: "all", ...params },
        });
    },
};
