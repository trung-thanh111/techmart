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
    timeout: 10000, // ms
    ...options,
    onResponseError({ request, response }) {
      console.error("API Error:", request, response);
      if (response.status === 401) {
        navigateTo(TOLOGIN);
      }
      throw createError({
        statusCode: response.status,
        statusMessage: response._data?.message || "API Error",
      });
    },
  });
};

export const useApi = () => {
  const publicFetch = createFetch();

  const privateFetch = createFetch({
    onRequest({ options }) {
      const auth = useAuthStore();
      const accessToken = auth.accessToken;

      if (!accessToken) {
        console.warn("Không tìm thấy accessToken");
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
