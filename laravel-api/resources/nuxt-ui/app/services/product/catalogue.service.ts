import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/product_catalogues`,
    resource: `${API_VERSION.version1}/product_catalogue`,
} as const;

export const productCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IProductCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IProductCatalogue>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductCatalogue>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IProductCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductCatalogue>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IProductCatalogue>): Promise<IApiOk<IProductCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductCatalogue>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IProductCatalogue[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IProductCatalogue[]>>(`${endpoint.collection}?type=all`);
    },
};
