import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IPostCatalogue } from "~/interfaces/post/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/post_catalogues`,
    resource: `${API_VERSION.version1}/post_catalogue`,
} as const;

export const postCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPostCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IPostCatalogue>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostCatalogue>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IPostCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostCatalogue>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IPostCatalogue>): Promise<IApiOk<IPostCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostCatalogue>>(`${endpoint.collection}/${id}`, {
            method: "POST",
            body: formData(payload),
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

    all: async (): Promise<IApiOk<IPostCatalogue[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPostCatalogue[]>>(`${endpoint.collection}?type=all&publish=2`);
    },
};
