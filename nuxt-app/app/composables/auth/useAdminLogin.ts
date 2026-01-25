import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { adminAuthService } from "~/services/auth/admin.auth.service";
import { useAdminAuthStore } from "~/stores/auth/adminAuth";
import { useAppToast } from "~/composables/useAppToast";
import { TODASHBOARD } from "~/utils/constants";

const loginSchema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
  password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(6, "Tối thiểu 6 ký tự"),
  rememberMe: z.boolean().optional().default(false),
});

export type TLoginRequest = z.infer<typeof loginSchema>;

export const useAdminLogin = () => {
  const toast = useAppToast();
  const auth = useAdminAuthStore();
  const isLoading = ref(false);

  const {
    handleSubmit,
    values: loginForm,
    resetForm,
  } = useForm<TLoginRequest>({
    validationSchema: toTypedSchema(loginSchema),
  });

  const handleLogin = handleSubmit(async (payload: TLoginRequest) => {
    isLoading.value = true;
    try {
      const res = await adminAuthService.login(payload);

      if (!res || !res.data) {
        throw new Error("Response không hợp lệ");
      }

      auth.setAuth(res.data.user, {
        accessToken: res.data.accessToken,
        refreshToken: null,
        csrfToken: res.data.csrfToken,
        expireAt: res.data.expireAt,
      });

      toast.success("Đăng nhập quản trị thành công");
      await navigateTo(TODASHBOARD);
    } catch (e: any) {
      console.error("Admin Login error:", e);
      const errorMessage = e?.data?.message || e?.response?.data?.message || e?.message || "Đăng nhập thất bại";
      toast.error(errorMessage);
    } finally {
      isLoading.value = false;
    }
  });

  return {
    loginForm,
    handleLogin,
    resetForm,
    isLoading,
  };
};
