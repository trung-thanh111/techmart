import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { ICartItem, CartItemPayload } from "~/interfaces/cart/cartItem.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/cart_items`,
  resource: `${API_VERSION.version1}/cart_item`,
} as const;

export const cartItemService = {
  store: async (payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(`${endpoint.collection}/${id}`, {
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

  storeGuest: async (payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  updateGuest: async (id: number, payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(`${endpoint.collection}/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  deleteGuest: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
      method: "DELETE",
    });
  },
};
