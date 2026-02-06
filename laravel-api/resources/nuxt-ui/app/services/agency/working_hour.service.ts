import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAgencyWorkingHour } from "~/interfaces/agency/working_hour.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/agency_working_hours`,
    resource: `${API_VERSION.version1}/agency_working_hour`,
} as const;

export const agencyWorkingHourService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgencyWorkingHour>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAgencyWorkingHour>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IAgencyWorkingHour>): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IAgencyWorkingHour>): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${endpoint.collection}?type=all`);
    },
};
