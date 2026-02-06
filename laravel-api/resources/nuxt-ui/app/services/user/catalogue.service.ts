import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IUserCatalogue } from "~/interfaces/user/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/user_catalogues`,
    resource: `${API_VERSION.version1}/user_catalogue`,
} as const;

export const userCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IUserCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IUserCatalogue>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUserCatalogue>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IUserCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUserCatalogue>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IUserCatalogue>): Promise<IApiOk<IUserCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUserCatalogue>>(`${endpoint.collection}/${id}`, {
            method: "PATCH",
            body: payload,
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

    all: async (): Promise<IApiOk<IUserCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUserCatalogue>>(`${endpoint.collection}?type=all`);
    },

    getPermissions: async (id: number): Promise<IApiOk<any>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<any>>(`${endpoint.collection}/${id}/permissions`);
    },

    attach: async (id: number, ids: number[], withRelation: string = "permissions"): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}/attach`, {
            method: "POST",
            body: { ids, with: withRelation },
        });
    },

    detach: async (id: number, ids: number[], withRelation: string = "permissions"): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}/detach`, {
            method: "DELETE",
            body: { ids, with: withRelation },
        });
    },
};
