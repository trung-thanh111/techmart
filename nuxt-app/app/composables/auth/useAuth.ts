import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TODASHBOARD, TOHOME, TOLOGIN } from "~/utils/constants";

const schema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
  password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(8, "Tối thiểu 8 ký tự"),
  rememberMe: z.boolean().optional().default(false),
});

export type TAuthRequest = z.infer<typeof schema>;

export const useAuth = () => {
  const toast = useAppToast();
  const auth = useAuthStore();
  const isLoading = ref(false);

  const {
    handleSubmit,
    values: form,
    resetForm,
  } = useForm<TAuthRequest>({
    validationSchema: toTypedSchema(schema),
  });

  const handleLogin = handleSubmit(async (payload) => {
    isLoading.value = true;
    try {
      const res = await authService.login(payload);
      auth.setAuth(res.data.user, {
        accessToken: res.data.accessToken,
        refreshToken: null,
        csrfToken: res.data.csrfToken,
        expireAt: res.data.expireAt,
      });
      toast.success("Đăng nhập thành công");
      return navigateTo(auth.isAdmin ? TODASHBOARD : TOHOME);
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Đăng nhập thất bại");
    } finally {
      isLoading.value = false;
    }
  });

  const getMe = async () => {
    try {
      const res = await authService.me();
      auth.setAuth(res.data.user, {
        accessToken: res.data.accessToken,
        refreshToken: null,
        csrfToken: res.data.csrfToken,
        expireAt: res.data.expireAt,
      });
      return res.data.user;
    } catch (e) {
      // nếu 401 sẽ bị interceptor trả lỗi
      console.error("getMe failed:", e);
    }
  };

  const handleLogout = async () => {
    try {
      await authService.logout();
    } catch (err) {
    } finally {
      auth.clearAuth();
      await navigateTo(TOLOGIN);
    }
  };

  return {
    form,
    isLoading,
    handleLogin,
    handleLogout,
    getMe,
    resetForm,
    isAuthenticated: computed(() => auth.isAuthenticated),
    isAdmin: computed(() => auth.isAdmin),
    user: computed(() => auth.user),
  };
};
