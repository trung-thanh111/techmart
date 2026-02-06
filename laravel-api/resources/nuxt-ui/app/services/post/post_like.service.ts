import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostLike, IPostLikeResponse } from "~/interfaces/post/post_like.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/post_likes`,
    resource: `${API_VERSION.version1}/post_like`,
} as const;

export const postLikeService = {
    store: async (postId: number): Promise<IApiOk<IPostLike>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostLike>>(endpoint.collection, {
            method: "POST",
            body: { post_id: postId },
        });
    },

    update: async (id: number, postId: number): Promise<IApiOk<IPostLike>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostLike>>(`${endpoint.collection}/${id}`, {
            method: "PATCH",
            body: { post_id: postId },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    pagination: async (params: Record<string, any>): Promise<IApiOk<IPaginate<IPostLike>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostLike>>>(endpoint.collection, { params });
    },

    // nếu trả về số bản ghi > 0 là đã like, còn < 0 là chưa like
    checkLiked: async (postId: number, userId: number): Promise<{ liked: boolean; records: IPostLike[] }> => {
        const { privateFetch } = useApi();
        const res = await privateFetch<IApiOk<IPaginate<IPostLike>>>(endpoint.collection, { params: { post_id: postId, user_id: userId } });
        const records: IPostLike[] = Array.isArray(res?.data?.data) ? res.data.data : [];
        return { liked: records.length > 0, records };
    },
};
