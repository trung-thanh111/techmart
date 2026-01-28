import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostTag } from "~/interfaces/post/tag.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/post_tags`,
  resource: `${API_VERSION.version1}/post_tag`,
} as const;

export const postTagService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostTag>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostTag>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPostTag[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostTag[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostTag>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostTag>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostTag>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostTag>>(`${endpoint.collection}/${id}`, {
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
};
