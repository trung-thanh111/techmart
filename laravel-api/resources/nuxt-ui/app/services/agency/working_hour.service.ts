import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAgencyWorkingHour } from "~/interfaces/agency/working_hour.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1";

export const agencyWorkingHourService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAgencyWorkingHour>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAgencyWorkingHour>>>(`${ENDPOINT}/agency_working_hours`, {
            params,
        });
    },

    store: async (payload: Partial<IAgencyWorkingHour>): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${ENDPOINT}/agency_working_hours`, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${ENDPOINT}/agency_working_hours/${id}`);
    },

    update: async (id: number, payload: Partial<IAgencyWorkingHour>): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${ENDPOINT}/agency_working_hours/${id}`, {
            method: "POST",
            body: formData(payload),
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/agency_working_hours/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/agency_working_hours/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IAgencyWorkingHour>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAgencyWorkingHour>>(`${ENDPOINT}/agency_working_hours?type=all`);
    },
};
