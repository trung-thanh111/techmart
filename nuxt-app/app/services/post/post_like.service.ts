import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { IPostLike } from "~/interfaces/post/post_like.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/post_likes`,
} as const;

export const postLikeService = {
  store: async (payload: Partial<IPostLike>): Promise<IApiOk<IPostLike>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostLike>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IPostLike>): Promise<IApiOk<IPostLike>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostLike>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },

  list: async (params?: any): Promise<IApiOk<IPostLike[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostLike[]>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  checkLiked: async (postId: number, userId: number): Promise<IApiOk<boolean>> => {
    const { publicFetch } = useApi();
    const response = await publicFetch<IApiOk<IPostLike[]>>(endpoint.collection, {
      method: "GET",
      params: {
        post_id: postId,
        user_id: userId,
      },
    });
    return {
      ...response,
      data: response.data.length > 0,
    };
  },
};
