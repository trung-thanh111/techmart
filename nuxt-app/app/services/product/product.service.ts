import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProduct } from "~/interfaces/product/product.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/products`,
  resource: `${API_VERSION.version1}/product`,
} as const;

export const productService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProduct>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IProduct>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string, relations?: string[]): Promise<IApiOk<IProduct>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct>>(`${endpoint.collection}/${id}`, {
      method: "GET",
      params: relations ? { with: relations } : undefined,
    });
  },

  getSummerProducts: async (limit = 3): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        season: "summer",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        perpage: limit,
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

  getFeatured: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        is_hot: 1,
        sort: "id,desc",
      },
    });
  },

  getFeaturedLimit: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        is_hot: 1,
        sort: "id,asc",
        limit: 5,
      },
    });
  },

  getLatest: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        sort: "created_at,desc",
      },
    });
  },

  getByHotCatalogue: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        sort: "id,desc",
        is_hot: 1,
      },
    });
  },

  getByCatalogueId: async (catalogueId: number): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        sort: "id,desc",
        product_catalogue_id: catalogueId,
      },
    });
  },

  getMostLiked: async (): Promise<IApiOk<IProduct[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IProduct[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        deleted_at: null,
        sort: "wishlist_count,desc",
        limit: 6,
      },
    });
  },
};
