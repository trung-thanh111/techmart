import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostTag } from "~/interfaces/post/tag.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/post_tags`,
    resource: `${API_VERSION.version1}/post_tag`,
} as const;

export const postTagService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostTag>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostTag>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostTag>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPostTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostTag>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostTag>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IPostTag[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostTag[]>>(`${endpoint.collection}?type=all&publish=2`);
    },
};
