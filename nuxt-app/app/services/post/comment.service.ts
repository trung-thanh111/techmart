import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostComment } from "~/interfaces/post/comment.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/post_comments`,
  resource: `${API_VERSION.version1}/post_comment`,
  slugResource: `${API_VERSION.version1}/post`,
} as const;

export const postCommentService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostComment>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostComment>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPostComment[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostComment>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment>>(endpoint.collection, {
      method: "POST",
      body: formData(payload),
    });
  },

  update: async (id: number, payload: Partial<IPostComment>): Promise<IApiOk<IPostComment>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
      method: "DELETE",
      body: { ids },
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IPostComment>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment>>(`${endpoint.slugResource}/${slug}`, {
      method: "GET",
    });
  },

  allByPostId: async (postId: number): Promise<IApiOk<IPostComment[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostComment[]>>(endpoint.collection, {
      method: "GET",
      params: {
        post_id: postId,
        publish: 2,
        type: "all",
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },
};
