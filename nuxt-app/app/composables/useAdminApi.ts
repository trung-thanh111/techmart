import type { FetchOptions } from "ofetch";
import { useAdminAuthStore } from "~/stores/auth/adminAuth";

// Path to Admin Login
const TO_ADMIN_LOGIN = "/auth/login";

// Hàm tạo fetch với base config
const createFetch = (options: FetchOptions = {}) => {
  const config = useRuntimeConfig();

  return $fetch.create({
    baseURL: config.public.baseUrl,
    timeout: 10000,
    credentials: "include",
    ...options,
    onResponseError({ request, response }) {
      console.error("Admin API Error:", request, response);
      if (response.status === 401) {
        navigateTo(TO_ADMIN_LOGIN);
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
    async onRequestError({ error }) {
      console.error("Request Error", error);
    },
  });
};

export const useAdminApi = () => {
  const publicFetch = createFetch();

  const privateFetch = createFetch({
    async onRequest({ options }) {
      const auth = useAdminAuthStore();

      // Check for token expiry
      if (auth.accessToken && auth.expireAt && Date.now() > auth.expireAt) {
        try {
          const { adminAuthService } = await import("~/services/auth/admin.auth.service");
          await adminAuthService.refresh();
        } catch (e) {
          console.error("Admin Token refresh failed", e);
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

  return {
    publicFetch,
    privateFetch,
  };
};
