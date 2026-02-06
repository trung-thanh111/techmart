import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPermission } from "~/interfaces/user/permission.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/permissions`,
    resource: `${API_VERSION.version1}/permission`,
} as const;

export const permissionService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPermission>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPermission>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPermission>): Promise<IApiOk<IPermission>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IPermission[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPermission[]>>(`${endpoint.collection}`, {
            params: { type: "all" },
        });
    },
};
