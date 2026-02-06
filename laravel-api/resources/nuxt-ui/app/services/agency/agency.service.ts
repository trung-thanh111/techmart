import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAgency } from "~/interfaces/agency/agency.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/agencies`,
    resource: `${API_VERSION.version1}/agency`,
} as const;

export const agencyService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgency>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAgency>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IAgency>): Promise<IApiOk<IAgency>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgency>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IAgency>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgency>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IAgency>): Promise<IApiOk<IAgency>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgency>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: formData(payload),
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

    all: async (): Promise<IApiOk<IAgency>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgency>>(`${endpoint.collection}?type=all`);
    },
};
