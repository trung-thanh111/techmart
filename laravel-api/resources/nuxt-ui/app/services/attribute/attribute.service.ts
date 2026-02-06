import type { IApiOk, IApiMessage, IPaginate } from "~/interfaces/base.interface";
import type { IAttribute } from "~/interfaces/attribute/attribute.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    collection: `${API_VERSION.version1}/attributes`,
    resource: `${API_VERSION.version1}/attribute`,
} as const;

export const attributeService = {
    pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IAttribute>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAttribute>>>(endpoint.collection, {
            params,
        });
    },

    getAttributesByCatalogue: async (catalogueId: number): Promise<IApiOk<IPaginate<IAttribute>>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IPaginate<IAttribute>>>(endpoint.collection, {
            params: {
                attribute_catalogue_id: catalogueId,
                type: "all",
            },
        });
    },

    store: async (payload: Partial<IAttribute>): Promise<IApiOk<IAttribute>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttribute>>(endpoint.collection, {
            method: "POST",
            body: formData(payload),
        });
    },

    show: async (id: number | string): Promise<IApiOk<IAttribute>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttribute>>(`${endpoint.collection}/${id}`);
    },

    update: async (id: number, payload: Partial<IAttribute>): Promise<IApiOk<IAttribute>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IAttribute>>(`${endpoint.collection}/${id}`, {
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
};
