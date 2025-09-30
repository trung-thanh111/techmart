import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { TLoginRequest } from "~/composables/auth/useLogin";
import type { TRegisterRequest } from "~/composables/auth/useRegister";
import type { IAuthSuccessResponse } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/auth";
const VERIFY_ENDPOINT = "/api/v1/email-verification";

export const authService = {
  me: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/me`);
  },

  login: async (payload: TLoginRequest): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/login`, {
      method: "POST",
      body: payload,
    });
  },

  // Kiểu trả về khi register có thể không chứa token nếu cần xác thực email
  register: async (payload: TRegisterRequest): Promise<any> => {
    const { publicFetch } = useApi();
    return await publicFetch<any>(`${ENDPOINT}/register`, {
      method: "POST",
      body: payload,
    });
  },

  logout: async (): Promise<IApiMessage> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiMessage>(`${ENDPOINT}/logout`, {
      method: "POST",
    });
  },
  
  // Status verification by email
  verifyStatus: async (email: string): Promise<{ data: { verified: boolean } }> => {
    const { publicFetch } = useApi();
    return await publicFetch<any>(`${VERIFY_ENDPOINT}/status?email=${encodeURIComponent(email)}`);
  },
};
