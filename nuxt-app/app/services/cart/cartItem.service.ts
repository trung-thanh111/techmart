import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { ICartItem, CartItemPayload } from "~/interfaces/cart/cartItem.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const cartItemService = {
  store: async (payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ICartItem>>(`${ENDPOINT}/cart_items`, {
      method: "POST",
      body: payload,
    });
  },

  update: async (id: number, payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ICartItem>>(`${ENDPOINT}/cart_items/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/cart_items/${id}`, {
      method: "DELETE",
    });
  },

  storeGuest: async (payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(`${ENDPOINT}/cart_items`, {
      method: "POST",
      body: payload,
    });
  },

  updateGuest: async (id: number, payload: Partial<CartItemPayload>): Promise<IApiOk<ICartItem>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICartItem>>(`${ENDPOINT}/cart_items/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  deleteGuest: async (id: number): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(`${ENDPOINT}/cart_items/${id}`, {
      method: "DELETE",
    });
  },
};
