import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IWishlist, IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/wishlists`,
  resource: `${API_VERSION.version1}/wishlist`,
} as const;

export const wishlistService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IWishlistGrouped>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IWishlistGrouped>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  all: async (): Promise<IApiOk<IWishlist[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IWishlist[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IWishlist>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IWishlist>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  store: async (payload: Partial<IWishlist>): Promise<IApiOk<IWishlist>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IWishlist>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<IWishlist>): Promise<IApiOk<IWishlist>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IWishlist>>(`${endpoint.collection}/${id}`, {
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
