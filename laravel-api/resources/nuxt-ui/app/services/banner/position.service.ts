import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPosition } from "~/interfaces/banner/position.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/banner_positions`,
    resource: `${API_VERSION.version1}/banner_position`,
} as const;

export const positionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPosition>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPosition>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPosition>): Promise<IApiOk<IPosition>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: payload,
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

    all: async (): Promise<IApiOk<IPosition[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPosition[]>>(`${endpoint.collection}`, {
            params: { type: "all" },
        });
    },
};
