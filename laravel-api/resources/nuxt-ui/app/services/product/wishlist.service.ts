import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IWishlist, IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1";

export const wishlistService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IWishlistGrouped>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IWishlistGrouped>>>(`${ENDPOINT}/wishlists`, {
            params,
        });
    },

    store: async (payload: Partial<IWishlist>): Promise<IApiOk<IWishlist>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IWishlist>>(`${ENDPOINT}/wishlists`, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IWishlist>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IWishlist>>(`${ENDPOINT}/wishlists/${id}`);
    },

    update: async (id: number, payload: Partial<IWishlist>): Promise<IApiOk<IWishlist>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IWishlist>>(`${ENDPOINT}/wishlists/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${ENDPOINT}/wishlists/${id}`, {
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IWishlist[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IWishlist[]>>(`${ENDPOINT}/wishlists?type=all`);
    },
};
