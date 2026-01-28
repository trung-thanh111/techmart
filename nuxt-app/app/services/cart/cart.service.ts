import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { ICart, CartPayload } from "~/interfaces/cart/cart.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/carts`,
  resource: `${API_VERSION.version1}/cart`,
} as const;

export const cartService = {
  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<ICart>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<ICart>>>(endpoint.collection, {
      method: "GET",
      params: {
        publish: 2,
        sort: "id,desc",
        deleted_at: null,
        ...params,
      },
    });
  },

  store: async (payload: Partial<CartPayload>): Promise<IApiOk<ICart>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICart>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },

  show: async (id: number | string): Promise<IApiOk<ICart>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICart>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },

  showByGuestId: async (guestId: string): Promise<IApiOk<ICart | null>> => {
    const { publicFetch } = useApi();
    try {
      const response = await publicFetch<IApiOk<any>>(endpoint.collection, {
        params: {
          guest_id: guestId,
          perpage: 1,
        },
      });
      let cartArray: ICart[] = [];

      if (Array.isArray(response.data)) {
        cartArray = response.data;
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        cartArray = response.data.data;
      } else if (response.data?.data?.data && Array.isArray(response.data.data.data)) {
        cartArray = response.data.data.data;
      }

      const data = cartArray.length > 0 && cartArray[0] ? cartArray[0] : null;

      return {
        ...response,
        data,
      } as IApiOk<ICart | null>;
    } catch (error: any) {
      if (error?.statusCode === 404 || error?.code === 404) {
        return { data: null, success: false, message: "Cart not found" } as any;
      }
      throw error;
    }
  },

  storeGuest: async (payload: Partial<CartPayload>): Promise<IApiOk<ICart>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<ICart>>(endpoint.collection, {
      method: "POST",
      body: payload,
    });
  },
};
