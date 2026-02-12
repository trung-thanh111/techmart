import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { IUser, IAuthSuccessResponse, ILoginPayload, IRegisterPayload, IForgotRequest, IForgotVerify, IForgotReset, IChangePasswordPayload } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";
import { useAuthStore } from "~/stores/auth/auth";

const endpoint = {
  login: `${API_VERSION.version1}/auth/login`,
  logout: `${API_VERSION.version1}/auth/logout`,
  refresh: `${API_VERSION.version1}/auth/refresh`,
  me: `${API_VERSION.version1}/auth/me`,
  register: `${API_VERSION.version1}/auth/register`,
  forgotRequest: `${API_VERSION.version1}/password/forgot/request`,
  forgotVerify: `${API_VERSION.version1}/password/forgot/verify`,
  forgotResend: `${API_VERSION.version1}/password/forgot/resend`,
  forgotReset: `${API_VERSION.version1}/password/forgot/reset`,
  changePassword: `${API_VERSION.version1}/password/change`,
} as const;

export const authService = {
  me: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAuthSuccessResponse>>(endpoint.me, {
      method: "GET",
    });
  },

  login: async (payload: ILoginPayload): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAuthSuccessResponse>>(endpoint.login, {
      method: "POST",
      body: payload,
    });
  },

  refresh: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useApi();
    const auth = useAuthStore();

    // Include CSRF token in refresh request
    const headers: HeadersInit = {};
    if (auth.csrfToken) {
      headers["X-CSRF-TOKEN"] = auth.csrfToken;
    }

    return await publicFetch<IApiOk<IAuthSuccessResponse>>(endpoint.refresh, {
      method: "POST",
      headers,
    });
  },

  logout: async (): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(endpoint.logout, {
      method: "POST",
    });
  },

  register: async (payload: IRegisterPayload): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(endpoint.register, {
      method: "POST",
      body: payload,
    });
  },

  verifyStatus: async (): Promise<IApiOk<any>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<any>>(`${API_VERSION.version1}/auth/status`, {
      method: "GET",
    });
  },

  forgotRequest: async (payload: IForgotRequest): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(endpoint.forgotRequest, {
      method: "POST",
      body: payload,
    });
  },

  forgotResend: async (payload: IForgotRequest): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(endpoint.forgotResend, {
      method: "POST",
      body: payload,
    });
  },

  forgotVerify: async (payload: IForgotVerify): Promise<IApiMessage> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiMessage>(endpoint.forgotVerify, {
      method: "POST",
      body: payload,
    });
  },

  forgotReset: async (payload: IForgotReset): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(endpoint.forgotReset, {
      method: "POST",
      body: payload,
    });
  },

  changePassword: async (payload: IChangePasswordPayload): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(endpoint.changePassword, {
      method: "POST",
      body: payload,
    });
  },
};
