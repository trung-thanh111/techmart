import type { IApiOk, IApiMessage } from "~/interfaces/base.interface";
import type { TLoginRequest } from "~/composables/auth/useLogin";
import type { TRegisterRequest } from "~/composables/auth/useRegister";
import type { IAuthSuccessResponse, IUser } from "~/interfaces/user/user.interface";
import { useApi } from "~/composables/useApi";
import { useAuthStore } from "~/stores/auth/auth";

const ENDPOINT = "/api/v1/admin/auth";
const VERIFY_ENDPOINT = "/api/v1/email-verification";
const FORGOT_ENDPOINT = "/api/v1/password/forgot";
const CHANGE_PASSWORD_ENDPOINT = "/api/v1/password/change";

export const authService = {
    me: async (): Promise<IApiOk<IUser>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IUser>>(`${ENDPOINT}/me`);
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

    // Forgot password flow
    forgotRequest: async (email: string): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${FORGOT_ENDPOINT}/request`, {
            method: "POST",
            body: { email },
        });
    },

    forgotResend: async (email: string): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${FORGOT_ENDPOINT}/resend`, {
            method: "POST",
            body: { email },
        });
    },

    forgotVerify: async (payload: { email: string; code: string }): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${FORGOT_ENDPOINT}/verify`, {
            method: "POST",
            body: payload,
        });
    },

    forgotReset: async (payload: { email: string; code: string; password: string; passwordConfirmation: string }): Promise<any> => {
        const { publicFetch } = useApi();
        return await publicFetch<any>(`${FORGOT_ENDPOINT}/reset`, {
            method: "POST",
            body: payload,
        });
    },

    // Change password
    changePassword: async (payload: { passwordOld: string; password: string; passwordConfirmation: string }): Promise<any> => {
        const { privateFetch } = useApi();
        return await privateFetch<any>(`${CHANGE_PASSWORD_ENDPOINT}`, {
            method: "POST",
            body: payload,
        });
    },

    refresh: async (): Promise<IApiOk<IAuthSuccessResponse>> => {
        const { publicFetch } = useApi();
        const auth = useAuthStore();
        return await publicFetch<IApiOk<IAuthSuccessResponse>>(`${ENDPOINT}/refresh`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": auth.csrfToken || "",
            },
            credentials: "include",
        });
    },
};
