import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { ICart, CartPayload } from "~/interfaces/cart/cart.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const cartService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<ICart>>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IPaginate<ICart>>>(`${ENDPOINT}/carts`, {
      params,
    });
  },

  store: async (payload: Partial<CartPayload>): Promise<IApiOk<ICart>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ICart>>(`${ENDPOINT}/carts`, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<ICart>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ICart>>(`${ENDPOINT}/carts/${id}`);
  },

  update: async (id: number, payload: Partial<CartPayload>): Promise<IApiOk<ICart>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<ICart>>(`${ENDPOINT}/carts/${id}`, {
      method: "POST",
      body: payload,
      params: { _method: "PATCH" },
    });
  },

  delete: async (id: number): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/carts/${id}`, {
      method: "DELETE",
    });
  },

  showByGuestId: async (guestId: string): Promise<IApiOk<ICart | null>> => {
    const { publicFetch } = useApi();
    try {
      const response = await publicFetch<IApiOk<any>>(`${ENDPOINT}/carts`, {
        params: {
          guest_id: guestId,
          perpage: 1,
        },
      });
      let cartArray: ICart[] = [];

      // Handle different response structures
      if (Array.isArray(response.data)) {
        cartArray = response.data;
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        cartArray = response.data.data;
      } else if (response.data?.data?.data && Array.isArray(response.data.data.data)) {
        cartArray = response.data.data.data;
      }

      if (cartArray.length > 0 && cartArray[0]) {
        return { ...response, data: cartArray[0] };
      }
      return { ...response, data: null };
    } catch (error: any) {
      // Nếu lỗi 404, trả về null thay vì throw
      if (error?.statusCode === 404 || error?.code === 404) {
        return { data: null, success: false, message: "Cart not found" };
      }
      throw error;
    }
  },

  storeGuest: async (payload: Partial<CartPayload>): Promise<IApiOk<ICart>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICart>>(`${ENDPOINT}/carts`, {
      method: "POST",
      body: payload,
    });
  },
};
