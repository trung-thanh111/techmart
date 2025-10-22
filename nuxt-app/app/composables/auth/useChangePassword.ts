import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { authService } from "~/services/auth/auth.service";
import { useAppToast } from "~/composables/useAppToast";
import { TOLOGIN } from "~/utils/constants";

const schema = z
  .object({
    passwordOld: z.string({ required_error: "Bạn chưa nhập mật khẩu cũ" }),
    password: z.string({ required_error: "Bạn chưa nhập mật khẩu mới" }).min(8, "Tối thiểu 8 ký tự").regex(/[a-z]/, "Phải có ít nhất 1 chữ thường (a-z)").regex(/[A-Z]/, "Phải có ít nhất 1 chữ hoa (A-Z)").regex(/\d/, "Phải có ít nhất 1 số (0-9)"),
    passwordConfirmation: z.string({ required_error: "Bạn chưa xác nhận mật khẩu" }),
  })
  .refine((data) => data.password === data.passwordConfirmation, {
    message: "Xác nhận mật khẩu không khớp",
    path: ["passwordConfirmation"],
  });

export const useChangePassword = () => {
  const toast = useAppToast();
  const router = useRouter();

  const { defineField, handleSubmit, errors, values, isSubmitting } = useForm({
    validationSchema: toTypedSchema(schema),
  });

  const [passwordOld, passwordOldAttrs] = defineField("passwordOld");
  const [password, passwordAttrs] = defineField("password");
  const [passwordConfirmation, passwordConfirmationAttrs] = defineField("passwordConfirmation");

  const onSubmit = handleSubmit(async (values) => {
    try {
      const response = await authService.changePassword({
        passwordOld: values.passwordOld,
        password: values.password,
        passwordConfirmation: values.passwordConfirmation,
      });

      toast.success(response.message || "Đổi mật khẩu thành công!");
      await router.push(TOLOGIN);
    } catch (error: any) {
      console.error("Change password error:", error);
      
      // Sử dụng error handling có sẵn từ useApi
      const errorMessage = error?.data?.message || error?.message || "Có lỗi xảy ra khi đổi mật khẩu";
      toast.error(errorMessage);
    }
  });

  return {
    // Form fields
    passwordOld,
    passwordOldAttrs,
    password,
    passwordAttrs,
    passwordConfirmation,
    passwordConfirmationAttrs,

    // Form state
    errors,
    values,
    isSubmitting,

    // Methods
    onSubmit,
  };
};
