import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TODASHBOARD, TOHOME, TOLOGIN } from "~/utils/constants";

const loginSchema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
  password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(8, "Tối thiểu 8 ký tự"),
  rememberMe: z.boolean().optional().default(false),
});

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

export type TAuthRequest = z.infer<typeof loginSchema>;
export type TRegisterRequest = z.infer<typeof registerSchema>;

export const useAuth = () => {
  const toast = useAppToast();
  const auth = useAuthStore();
  const isLoading = ref(false);

  const {
    handleSubmit: handleLoginSubmit,
    values: loginForm,
    resetForm: resetLoginForm,
  } = useForm<TAuthRequest>({
    validationSchema: toTypedSchema(loginSchema),
  });

  const {
    handleSubmit: handleRegisterSubmit,
    values: registerForm,
    resetForm: resetRegisterForm,
  } = useForm<TRegisterRequest>({
    validationSchema: toTypedSchema(registerSchema),
  });

  const handleLogin = handleLoginSubmit(async (payload) => {
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

  const handleRegister = handleRegisterSubmit(async (payload) => {
    isLoading.value = true;
    try {
      const res = await authService.register(payload);
      auth.setAuth(res.data.user, {
        accessToken: res.data.accessToken,
        refreshToken: null,
        csrfToken: res.data.csrfToken,
        expireAt: res.data.expireAt,
      });
      toast.success("Đăng ký thành công");
      return navigateTo(auth.isAdmin ? TODASHBOARD : TOHOME);
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Đăng ký thất bại");
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
    // Login
    loginForm,
    handleLogin,
    resetLoginForm,
    // Register
    registerForm,
    handleRegister,
    resetRegisterForm,
    // Common
    isLoading,
    handleLogout,
    getMe,
    isAuthenticated: computed(() => auth.isAuthenticated),
    isAdmin: computed(() => auth.isAdmin),
    user: computed(() => auth.user),
  };
};
