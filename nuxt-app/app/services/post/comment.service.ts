import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostComment } from "~/interfaces/post/comment.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { buildRelationParams } from "~/utils/helpers";

const ENDPOINT = "/api/v1";

export const postCommentService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostComment>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IPostComment>>>(`${ENDPOINT}/post_comments`, {
      params,
    });
  },

  store: async (payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostComment>>(`${ENDPOINT}/post_comments`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostComment>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostComment>>(`${ENDPOINT}/post_comments/${id}`);
  },

  update: async (id: number, payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostComment>>(`${ENDPOINT}/post_comments/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_comments/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_comments/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IPostComment[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment[]>>(`${ENDPOINT}/post_comments?type=all&publish=2`);
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostComment>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostComment>>>(`${ENDPOINT}/post_comments?type=all&publish=2`, {
      params,
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IPostComment>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment>>(`${ENDPOINT}/post/${slug}`);
  },

  ///

  allByPostId: async (postId: number): Promise<IApiOk<IPostComment[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment[]>>(`${ENDPOINT}/post_comments`, {
      params: {
        post_id: postId,
        publish: 2,
        type: 'all',
        sort: 'id,desc',
      }
    });
  },

};
