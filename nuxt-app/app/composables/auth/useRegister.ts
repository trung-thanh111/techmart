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

  const {
    handleSubmit,
    values: registerForm,
    resetForm,
  } = useForm<TRegisterRequest>({
    validationSchema: toTypedSchema(registerSchema),
  });

  const handleRegister = handleSubmit(async (payload: TRegisterRequest) => {
    console.log('Register form submitted with payload:', payload);
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

  return {
    registerForm,
    handleRegister,
    resetForm,
    isLoading,
  };
};
