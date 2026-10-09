import type { IApiOk } from "~/interfaces/base.interface";
import type { IAuthSuccessResponse } from "~/interfaces/user/user.interface";
import { useAdminApi } from "~/composables/useAdminApi";
import { useAuthStore } from "~/stores/auth/auth";
import { ADMIN_AUTH_PREFIX } from "~/utils/apiConfig";

const endpoint = {
    auth: `${ADMIN_AUTH_PREFIX}/auth`,
} as const;

export const adminAuthService = {
    refresh: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
        const { publicFetch } = useAdminApi();
        const auth = useAuthStore();
        return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${endpoint.auth}/refresh`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": auth.csrfToken || "",
            },
            credentials: "include",
        });
    },
};
