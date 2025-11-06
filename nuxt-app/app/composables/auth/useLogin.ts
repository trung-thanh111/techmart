import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { useCart } from "~/composables/client/cart/useCart";
import { TODASHBOARD, TOHOME } from "~/utils/constants";

const loginSchema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
  password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(8, "Tối thiểu 8 ký tự"),
  rememberMe: z.boolean().optional().default(false),
});

export type TLoginRequest = z.infer<typeof loginSchema>;

export const useLogin = () => {
  const toast = useAppToast();
  const auth = useAuthStore();
  const isLoading = ref(false);

  const {
    handleSubmit,
    values: loginForm,
    resetForm,
  } = useForm<TLoginRequest>({
    validationSchema: toTypedSchema(loginSchema),
  });

  const handleLogin = handleSubmit(async (payload: TLoginRequest) => {
    console.log("Login form submitted with payload:", payload);
    isLoading.value = true;
    try {
      const res = await authService.login(payload);
      auth.setAuth(res.data.user, {
        accessToken: res.data.accessToken,
        refreshToken: null,
        csrfToken: res.data.csrfToken,
        expireAt: res.data.expireAt,
      });

      // Đồng bộ cart sau khi login
      const { syncCartAfterLogin } = useCart();
      await syncCartAfterLogin();

      toast.success("Đăng nhập thành công");
      return navigateTo(auth.isAdmin ? TODASHBOARD : TOHOME);
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Đăng nhập thất bại");
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
