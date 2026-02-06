import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostComment } from "~/interfaces/post/comment.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/post_comments`,
    resource: `${API_VERSION.version1}/post_comment`,
} as const;

export const postCommentService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostComment>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostComment>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostComment>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPostComment>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostComment>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostComment>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IPostComment[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostComment[]>>(`${endpoint.collection}?type=all&publish=2`);
    },
};
