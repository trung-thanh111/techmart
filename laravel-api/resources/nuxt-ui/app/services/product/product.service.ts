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
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IProduct>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IProduct>): Promise<IApiOk<IProduct>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProduct>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IProduct>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProduct>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IProduct>): Promise<IApiOk<IProduct>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProduct>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: payload,
            params: { _method: "PATCH" },
        });
    },

    delete: async (id: number): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/${id}`, {
            method: "DELETE",
        });
    },

    deleteMultiple: async (ids: number[]): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.collection}/bulkDelete`, {
            body: { ids },
            method: "DELETE",
        });
    },

    all: async (): Promise<IApiOk<IProduct>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProduct>>(`${endpoint.collection}?type=all`);
    },
};
