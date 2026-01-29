import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPage } from "~/interfaces/banner/page.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/admin";

export const pageService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPage>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPage>>>(`${ENDPOINT}/banner_pages`, {
            params,
        });
    },

    store: async (payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(`${ENDPOINT}/banner_pages`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(`${ENDPOINT}/banner_pages/${id}`);
    },

    update: async (id: number, payload: Partial<IPage>): Promise<IApiOk<IPage>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage>>(`${ENDPOINT}/banner_pages/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/banner_pages/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/banner_pages/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IPage[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPage[]>>(`${ENDPOINT}/banner_pages`, {
            params: { type: "all" },
        });
    },
};
