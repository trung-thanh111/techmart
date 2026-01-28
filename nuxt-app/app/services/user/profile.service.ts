import type { IApiOk } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  me: `${API_VERSION.version1}/auth/me`,
  users: `${API_VERSION.version1}/users`,
} as const;

export const profileService = {
  getProfile: async (): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(endpoint.me, {
      method: "GET",
    });
  },

  updateProfile: async (id: number, payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(`${endpoint.users}/${id}`, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },
};
