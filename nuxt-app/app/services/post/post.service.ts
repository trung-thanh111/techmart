import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPost } from "~/interfaces/post/post.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { buildRelationParams } from "~/utils/helpers";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/posts`,
  resource: `${API_VERSION.version1}/post`,
} as const;

export const postService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPost>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPost>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPost>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  getFeatured: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        is_hot: 1,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  getFeaturedLimit: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        is_hot: 1,
        sort: "id,asc",
        limit: 5,
        deleted_at: null,
      },
    });
  },

  getLatest: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "published_at,desc",
        deleted_at: null,
      },
    });
  },

  getByHotCatalogue: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    const relationParams = buildRelationParams({ post_catalogues: 1 }, "is_hot");
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...relationParams,
      },
    });
  },

  getByCatalogueId: async (catalogueId: number): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    const relationParams = buildRelationParams({ post_catalogues: catalogueId }, "id");
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...relationParams,
      },
    });
  },

  getMostLiked: async (): Promise<IApiOk<IPost[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPost[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "like_count,desc",
        limit: 6,
        deleted_at: null,
      },
    });
  },

  incrementViewCount: async (id: number, viewCount: number): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${endpoint.collection}/${id}/view`, {
      method: "PUT",
      body: { view_count: viewCount },
    });
  },
};
