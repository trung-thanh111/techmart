import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPosition } from "~/interfaces/banner/position.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/admin";

export const positionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPosition>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPosition>>>(`${ENDPOINT}/banner_positions`, {
            params,
        });
    },

    store: async (payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(`${ENDPOINT}/banner_positions`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(`${ENDPOINT}/banner_positions/${id}`);
    },

    update: async (id: number, payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(`${ENDPOINT}/banner_positions/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/banner_positions/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/banner_positions/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IPosition[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition[]>>(`${ENDPOINT}/banner_positions`, {
            params: { type: "all" },
        });
    },
};
