import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostTag } from "~/interfaces/post/tag.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const postTagService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostTag>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IPostTag>>>(`${ENDPOINT}/post_tags`, {
      params,
    });
  },

  store: async (payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostTag>>(`${ENDPOINT}/post_tags`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPostTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostTag>>(`${ENDPOINT}/post_tags/${id}`);
  },

  update: async (id: number, payload: Partial<IPostTag>): Promise<IApiOk<IPostTag>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPostTag>>(`${ENDPOINT}/post_tags/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_tags/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/post_tags/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IPostTag[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPostTag[]>>(`${ENDPOINT}/post_tags?type=all&publish=2`);
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostTag>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPostTag>>>(`${ENDPOINT}/post_tags`, {
      params,
    });
  },
};
