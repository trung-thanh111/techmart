import type { FetchOptions } from "ofetch";
import { TOLOGIN } from "~/utils/constants";
import { useAuthStore } from "~/stores/auth/auth";

// Kiểu response chung
export interface ApiResponse<T = any> {
    data: T;
    message?: string;
    status: number;
}

// Hàm tạo fetch với base config
const createFetch = (options: FetchOptions = {}) => {
    const config = useRuntimeConfig();

    return $fetch.create({
        baseURL: config.public.baseUrl,
        timeout: 30000, // ms
        credentials: "include", // Để gửi cookie
        ...options,
        onResponseError({ request, response }) {
            console.error("API Error:", request, response);
            if (response.status === 401) {
                const auth = useAuthStore();
                auth.clearAuth();
                const toast = useAppToast();
                if (toast) {
                    toast.warning("Phiên đăng nhập hết hạn", {
                        description: "Vui lòng đăng nhập lại để tiếp tục.",
                    });
                }
                navigateTo(TOLOGIN);
            }
            if (response.status === 422) {
                throw createError({
                    statusCode: 422,
                    statusMessage: response._data?.message,
                    data: {
                        isValidationError: true,
                        errors: response._data?.errors || {},
                    },
                });
            }
            throw createError({
                statusCode: response.status,
                statusMessage: response._data?.message || "API Error",
                data: response._data,
            });
        },
    });
};

export const useApi = () => {
    const publicFetch = createFetch();

    const privateFetch = createFetch({
        async onRequest({ options }) {
            const auth = useAuthStore();

            if (auth.accessToken && auth.expireAt && Date.now() > auth.expireAt) {
                try {
                    const { authService } = await import("~/services/auth/auth.service");
                    await authService.refresh();
                } catch (e) {
                    console.error("Token refresh failed", e);
                }
            }

            const accessToken = auth.accessToken;

            if (!accessToken) {
                throw createError({
                    statusCode: 401,
                    statusMessage: "Unauthorized",
                });
            }
            const headers = new Headers(options.headers as HeadersInit);
            headers.set("Authorization", `Bearer ${accessToken}`);
            options.headers = headers;
        },
    });

    const usePrivateApi = <T>(url: string | (() => string), options: any = {}) => {
        return useFetch<T>(url, {
            ...options,
            $fetch: privateFetch,
        });
    };

    return {
        publicFetch,
        privateFetch,
        usePrivateApi,
    };
};
