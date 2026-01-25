import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { TLoginRequest } from "~/composables/auth/useLogin";
import type { IAuthSuccessResponse } from "~/interfaces/user/user.interface";
import { useAdminApi } from "~/composables/useAdminApi";
import { useAdminAuthStore } from "~/stores/auth/adminAuth";

const ENDPOINT = "/api/v1/admin/auth";

export const adminAuthService = {
  me: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { privateFetch } = useAdminApi();
    return await privateFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/me`);
  },

  login: async (payload: TLoginRequest): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useAdminApi();
    return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/login`, {
      method: "POST",
      body: payload,
    });
  },

  logout: async (): Promise<IApiMessage> => {
    const { privateFetch } = useAdminApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/logout`, {
      method: "POST",
    });
  },

  refresh: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useAdminApi(); // Use public fetch for refresh
    const auth = useAdminAuthStore();
    return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/refresh`, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": auth.csrfToken || "",
      },
      credentials: "include",
    });
  },
};
