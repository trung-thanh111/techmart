import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/users`,
    resource: `${API_VERSION.version1}/user`,
} as const;

export const useUserService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUser>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IUser>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(`${endpoint.collection}?type=all`);
    },
};
