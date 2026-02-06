import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostCommentLike } from "~/interfaces/post/comment_like.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/post_comment_likes`,
    resource: `${API_VERSION.version1}/post_comment_like`,
} as const;

export const postCommentLikeService = {
    store: async (commentId: number): Promise<IApiOk<IPostCommentLike>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostCommentLike>>(endpoint.collection, {
            method: "POST",
            body: { comment_id: commentId },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    listByComment: async (commentId: number, extraParams: Record<string, any> = {}): Promise<IApiOk<IPaginate<IPostCommentLike>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostCommentLike>>>(endpoint.collection, {
            params: { comment_id: commentId, type: "all", ...extraParams },
        });
    },

    list: async (params: Record<string, any>): Promise<IApiOk<IPaginate<IPostCommentLike>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostCommentLike>>>(endpoint.collection, { params });
    },

    // nếu trả về số bản ghi > 0 là đã like, còn < 0 là chưa like
    checkLiked: async (commentId: number, userId: number): Promise<{ liked: boolean; records: IPostCommentLike[] }> => {
        const { privateFetch } = useApi();
        const res = await privateFetch<IApiOk<IPaginate<IPostCommentLike>>>(endpoint.collection, { params: { comment_id: commentId, user_id: userId } });
        const records: IPostCommentLike[] = Array.isArray(res?.data?.data) ? res.data.data : [];
        return { liked: records.length > 0, records };
    },
};
