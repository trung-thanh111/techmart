import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPermission } from "~/interfaces/user/permission.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const permissionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPermission>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPermission>>>(`${ENDPOINT}/permissions`, {
            params,
        });
    },

    store: async (payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(`${ENDPOINT}/permissions`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(`${ENDPOINT}/permissions/${id}`);
    },

    update: async (id: number, payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(`${ENDPOINT}/permissions/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/permissions/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/permissions/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IPermission[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission[]>>(`${ENDPOINT}/permissions`, {
            params: { type: "all" },
        });
    },
};
