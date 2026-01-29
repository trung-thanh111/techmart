import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPopup } from "~/interfaces/system/popup.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1/admin";

export const popupService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPopup>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPopup>>>(`${ENDPOINT}/popups`, {
            params,
        });
    },

    store: async (payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(`${ENDPOINT}/popups`, {
            method: "POST",
            body: formData(payload as Record<string, any>),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(`${ENDPOINT}/popups/${id}`);
    },

    update: async (id: number, payload: Partial<IPopup>): Promise<IApiOk<IPopup>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup>>(`${ENDPOINT}/popups/${id}`, {
            method: "POST",
            body: formData(payload as Record<string, any>),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/popups/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/popups/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },
    all: async (params?: Record<string, any>): Promise<IApiOk<IPopup[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPopup[]>>(`${ENDPOINT}/popups`, {
            params: { type: "all", ...params },
        });
    },
};
