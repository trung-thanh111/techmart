import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TODASHBOARD, TOHOME } from "~/utils/constants";

const registerSchema = z
    .object({
        name: z.string({ required_error: "Bạn chưa nhập Họ tên" }).min(2, "Tối thiểu 2 ký tự"),
        email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
        password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(8, "Tối thiểu 8 ký tự"),
        passwordConfirmation: z.string({ required_error: "Bạn chưa xác nhận mật khẩu" }),
        rememberMe: z.boolean().optional().default(false),
    })
    .refine((data) => data.password === data.passwordConfirmation, {
        message: "Mật khẩu xác nhận không khớp",
        path: ["passwordConfirmation"],
    });

export type TRegisterRequest = z.infer<typeof registerSchema>;

export const useRegister = () => {
    const toast = useAppToast();
    const auth = useAuthStore();
    const isLoading = ref(false);
    const verifyNotice = ref<string | null>(null);

    const {
        handleSubmit,
        values: registerForm,
        resetForm,
    } = useForm<TRegisterRequest>({
        validationSchema: toTypedSchema(registerSchema),
    });

    const handleRegister = handleSubmit(async (payload: TRegisterRequest) => {
        isLoading.value = true;
        try {
            const res = await authService.register(payload);
            const needVerify = (res as any)?.data?.email_verification_required === true || (res as any)?.data?.data?.email_verification_required === true;
            if (needVerify) {
                const pendingEmail = (res as any)?.data?.user?.email || (res as any)?.data?.data?.user?.email || payload.email;
                if (pendingEmail) localStorage.setItem("pendingEmailVerification", pendingEmail);
                verifyNotice.value = res?.data?.message;
                return;
            }
        } catch (e: any) {
            verifyNotice.value = e?.response?.data?.message || "Đăng ký thất bại";
        } finally {
            isLoading.value = false;
        }
    });

    return {
        registerForm,
        handleRegister,
        resetForm,
        isLoading,
        verifyNotice,
    };
};
