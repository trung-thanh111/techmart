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
        const url = typeof request === "string" ? request : (request as any)?.url || "";
        // Nếu chính request login hoặc refresh bị 401 (sai thông tin xác thực), không trigger logout session
        if (url.includes("/auth/login") || url.includes("/auth/refresh")) {
          throw createError({
            statusCode: 401,
            statusMessage: response._data?.message || "Unauthorized",
            data: response._data,
          });
        }

        const auth = useAuthStore();
        auth.clearAuth();
        const toast = useAppToast();
        if (toast) {
          const nuxtApp = useNuxtApp();
          const t = (key: string, fallback: string) => {
            return (nuxtApp as any).$i18n ? (nuxtApp as any).$i18n.t(key) : fallback;
          };
          toast.warning(t("auth.sessionExpired", "Phiên đăng nhập hết hạn"), {
            description: t("auth.sessionExpiredDesc", "Vui lòng đăng nhập lại để tiếp tục."),
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

      // Check for token expiry
      if (auth.accessToken && auth.expireAt && Date.now() > auth.expireAt) {
        try {
          // Dynamic import to avoid circular dependency
          const { authService } = await import("~/services/auth/auth.service");
          const refreshRes = await authService.refresh();
          // Update auth store with new tokens
          if (refreshRes?.data) {
            auth.setAuth(refreshRes.data.user, {
              accessToken: refreshRes.data.accessToken,
              refreshToken: null,
              csrfToken: refreshRes.data.csrfToken,
              expireAt: refreshRes.data.expireAt,
            });
          }
        } catch (e) {
          // If refresh fails, let the request proceed (it will 401) or logout
          // We let it proceed to fail gracefully or let onResponseError handle it
          console.error("Token refresh failed", e);
        }
      }

      const accessToken = auth.accessToken;
      const csrfToken = auth.csrfToken;

      if (!accessToken) {
        throw createError({
          statusCode: 401,
          statusMessage: "Unauthorized",
        });
      }

      const headers = new Headers(options.headers as HeadersInit);
      headers.set("Authorization", `Bearer ${accessToken}`);

      // Add CSRF token for POST/PUT/PATCH/DELETE requests
      if (csrfToken && ["POST", "PUT", "PATCH", "DELETE"].includes(options.method?.toUpperCase() || "")) {
        headers.set("X-CSRF-TOKEN", csrfToken);
      }

      options.headers = headers;
    },
  });

  return {
    publicFetch,
    privateFetch,
  };
};
