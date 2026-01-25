import type { IApiOk } from "~/interfaces/base.interface";
import type { IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { formData } from "~/utils/formData";

const ENDPOINT = "/api/v1/profile";

export const profileService = {
  getProfile: async (): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(ENDPOINT);
  },

  updateProfile: async (payload: Partial<IUser>): Promise<IApiOk<IUser>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IUser>>(ENDPOINT, {
      method: "PUT", // Matches route definition
      body: formData(payload),
    });
  },
};
