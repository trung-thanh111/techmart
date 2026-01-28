import type { IApiOk } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  profile: `${API_VERSION.version1}/user/profile`,
} as const;

export const profileService = {
  getProfile: async (): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(endpoint.profile, {
      method: "GET",
    });
  },

  updateProfile: async (payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(endpoint.profile, {
      method: "POST",
      body: formData(payload),
      params: { _method: "PATCH" },
    });
  },
};
