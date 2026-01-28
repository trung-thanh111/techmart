import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { IPostCommentLike } from "~/interfaces/post/comment_like.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/post_comment_likes`,
} as const;

export const postCommentLikeService = {
  store: async (payload: Partial<IPostCommentLike>): Promise<IApiOk<IPostCommentLike>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCommentLike>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },

  listByComment: async (commentId: number): Promise<IApiOk<IPostCommentLike[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCommentLike[]>>(endpoint.collection, {
      method: "GET",
      params: {
        post_comment_id: commentId,
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  list: async (params?: any): Promise<IApiOk<IPostCommentLike[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostCommentLike[]>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  checkLiked: async (commentId: number, userId: number): Promise<IApiOk<boolean>> => {
    const { publicFetch } = useApi();
    const response = await publicFetch<IApiOk<IPostCommentLike[]>>(endpoint.collection, {
      method: "GET",
      params: {
        post_comment_id: commentId,
        user_id: userId,
      },
    });
    return {
      ...response,
      data: response.data.length > 0,
    };
  },
};
