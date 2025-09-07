import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { TAuthRequest } from "~/composables/auth/useAuth";
import type { IAuthSuccessResponse } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";

const ENDPOINT = "/api/v1/auth";

export const authService = {
  me: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/me`);
  },

  login: async (payload: TAuthRequest): Promise<IApiOk<IAuthSuccessResponse>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/login`, {
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
};
