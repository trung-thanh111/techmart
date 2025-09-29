import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { IPostLike, IPostLikeResponse } from "~/interfaces/post/post-like.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const postLikeService = {
  store: async (postId: number): Promise<IApiOk<IPostLike>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostLike>>(`${ENDPOINT}/post_likes`, {
      method: "POST",
      body: { post_id: postId },
    });
  },

  update: async (id: number, postId: number): Promise<IApiOk<IPostLike>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostLike>>(`${ENDPOINT}/post_likes/${id}`, {
      method: "PATCH",
      body: { post_id: postId },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_likes/${id}`, {
      method: "DELETE",
    });
  },

  // Helper method để lấy danh sách likes của user cho một post
  getUserLikes: async (postId: number): Promise<IApiOk<IPostLike[]>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostLike[]>>(`${ENDPOINT}/post_likes`, {
      params: { post_id: postId },
    });
  },
};
