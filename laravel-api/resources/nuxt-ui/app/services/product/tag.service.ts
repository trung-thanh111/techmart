import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductTag } from "~/interfaces/product/tag.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/product_tags`,
    resource: `${API_VERSION.version1}/product_tag`,
} as const;

export const productTagService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductTag>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IProductTag>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IProductTag>): Promise<IApiOk<IProductTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductTag>>(endpoint.collection, {
            method: "POST",
            body: payload,
        });
    },

    show: async (id: number | string): Promise<IApiOk<IProductTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductTag>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IProductTag>): Promise<IApiOk<IProductTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductTag>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IProductTag>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductTag>>(`${endpoint.collection}?type=all`);
    },
};
