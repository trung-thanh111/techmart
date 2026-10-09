import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { TLoginRequest } from "~/composables/auth/useLogin";
import type { TRegisterRequest } from "~/composables/auth/useRegister";
import type { IAuthSuccessResponse, IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { useAuthStore } from "~/stores/auth/auth";
import { API_VERSION, ADMIN_AUTH_PREFIX } from "~/utils/apiConfig";

const endpoint = {
    auth: `${ADMIN_AUTH_PREFIX}/auth`,
    verification: `${API_VERSION.version1}/email-verification`,
    forgotPassword: `${API_VERSION.version1}/password/forgot`,
    changePassword: `${API_VERSION.version1}/password/change`,
} as const;

export const authService = {
    me: async (): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(`${endpoint.auth}/me`);
    },

    login: async (payload: TLoginRequest): Promise<IApiOk<IAuthSuccessResponse>> => {
        const { publicFetch } = useApi();
        return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${endpoint.auth}/login`, {
            method: "POST",
            body: payload,
        });
    },

    // Kiểu trả về khi register có thể không chứa token nếu cần xác thực email
    register: async (payload: TRegisterRequest): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.auth}/register`, {
            method: "POST",
            body: payload,
        });
    },

    logout: async (): Promise<IApiMessage> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiMessage>(`${endpoint.auth}/logout`, {
            method: "POST",
        });
    },

    // Status verification by email
    verifyStatus: async (email: string): Promise<{ data: { verified: boolean } }> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.verification}/status?email=${encodeURIComponent(email)}`);
    },

    // Forgot password flow
    forgotRequest: async (email: string): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.forgotPassword}/request`, {
            method: "POST",
            body: { email },
        });
    },

    forgotResend: async (email: string): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.forgotPassword}/resend`, {
            method: "POST",
            body: { email },
        });
    },

    forgotVerify: async (payload: { email: string; code: string }): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.forgotPassword}/verify`, {
            method: "POST",
            body: payload,
        });
    },

    forgotReset: async (payload: { email: string; code: string; password: string; passwordConfirmation: string }): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${endpoint.forgotPassword}/reset`, {
            method: "POST",
            body: payload,
        });
    },

    // Change password
    changePassword: async (payload: { passwordOld: string; password: string; passwordConfirmation: string }): Promise<any> => {
        const { privateFetch } = useApi();
        return await privateFetch<any>(`${endpoint.changePassword}`, {
            method: "POST",
            body: payload,
        });
    },

    refresh: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
        const { publicFetch } = useApi();
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
