import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPost } from "~/interfaces/post/post.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/posts`,
    resource: `${API_VERSION.version1}/post`,
} as const;

export const postService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPost>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPost>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPost>): Promise<IApiOk<IPost>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPost>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPost>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPost>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPost>): Promise<IApiOk<IPost>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPost>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IPost[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPost[]>>(`${endpoint.collection}?type=all&publish=2`);
    },
};
