import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPost } from "~/interfaces/post/post.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { buildRelationParams } from "~/utils/helpers";

const ENDPOINT = "/api/v1";

export const postService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPost>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<IPost>>>(`${ENDPOINT}/posts`, {
      params,
    });
  },

  store: async (payload: Partial<IPost>): Promise<IApiOk<IPost>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPost>>(`${ENDPOINT}/posts`, {
      method: "POST",
      body: formData(payload),
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPost>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPost>>(`${ENDPOINT}/posts/${id}`);
  },

  update: async (id: number, payload: Partial<IPost>): Promise<IApiOk<IPost>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPost>>(`${ENDPOINT}/posts/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/posts/${id}`, {
      method: "DELETE",
    });
  },

  deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/posts/bulkDelete`, {
      body: { ids },
      method: "DELETE",
    });
  },

  all: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts?type=all&publish=2`);
  },

  getFeatured: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts?type=all&publish=2&is_hot=1&sort=id,desc`);
  },

  getFeaturedLimit: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts?type=all&publish=2&is_hot=1&sort=id,asc&limit=5`);
  },

  getLatest: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts?type=all&publish=2&sort=published_at,desc`);
  },

  getByHotCatalogue: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    const relationParams = buildRelationParams({ post_catalogues: 1 }, "is_hot");
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts`, {
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        ...relationParams,
      },
    });
  },

  getByCatalogueId: async (catalogueId: number): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    const relationParams = buildRelationParams({ post_catalogues: catalogueId }, "id");
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts`, {
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        ...relationParams,
      },
    });
  },

  paginationPublic: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPost>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPost>>>(`${ENDPOINT}/posts`, {
      params,
    });
  },

  showBySlug: async (slug: string): Promise<IApiOk<IPost>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost>>(`${ENDPOINT}/post/${slug}`);
  },

  getMostLiked: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(`${ENDPOINT}/posts?type=all&publish=2&sort=like_count,desc&limit=6`);
  },

  showPublic: async (id: number | string): Promise<IApiOk<IPost>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost>>(`${ENDPOINT}/posts/${id}`);
  },

  incrementViewCount: async (id: number, viewCount: number): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${ENDPOINT}/posts/${id}/view`, {
      method: "PUT",
      body: { view_count: viewCount },
    });
  },
};
