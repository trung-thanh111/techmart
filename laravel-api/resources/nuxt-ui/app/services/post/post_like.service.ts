import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostLike, IPostLikeResponse } from "~/interfaces/post/post_like.interface";
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

  list: async (params: Record<string, any>): Promise<IApiOk<IPaginate<IPostLike>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IPostLike>>>(`${ENDPOINT}/post_likes`, { params });
  },

  // nếu trả về số bản ghi > 0 là đã like, còn < 0 là chưa like
  checkLiked: async (postId: number, userId: number): Promise<{ liked: boolean; records: IPostLike[] }> => {
    const { privateFetch } = useApi();
    const res = await privateFetch<IApiOk<IPaginate<IPostLike>>>(`${ENDPOINT}/post_likes`, { params: { post_id: postId, user_id: userId } });
    const records: IPostLike[] = Array.isArray(res?.data?.data) ? res.data.data : [];
    return { liked: records.length > 0, records };
  },
};
