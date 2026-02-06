import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAttributeCatalogue } from "~/interfaces/attribute/catalogue.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/attribute_catalogues`,
    resource: `${API_VERSION.version1}/attribute_catalogue`,
} as const;

export const attributeCatalogueService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAttributeCatalogue>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAttributeCatalogue>>>(endpoint.collection, {
            params,
        });
    },

    store: async (payload: Partial<IAttributeCatalogue>): Promise<IApiOk<IAttributeCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttributeCatalogue>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IAttributeCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttributeCatalogue>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IAttributeCatalogue>): Promise<IApiOk<IAttributeCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttributeCatalogue>>(`${endpoint.collection}/${id}`, {
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

    all: async (): Promise<IApiOk<IAttributeCatalogue>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttributeCatalogue>>(`${endpoint.collection}?type=all`);
    },
};
