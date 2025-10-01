import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useRouter } from "vue-router";
import { authService } from "~/services/auth/auth.service";
import { TOFORGOTOTP } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";

const requestSchema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
});
export type TForgotRequest = z.infer<typeof requestSchema>;

export const useForgot = () => {
  const toast = useAppToast();
  const router = useRouter();
  const isLoading = ref(false);

  const { handleSubmit, defineField } = useForm<TForgotRequest>({
    validationSchema: toTypedSchema(requestSchema),
    initialValues: { email: "" },
  });

  const [emailField] = defineField("email");

  const handleVerify = handleSubmit(async (payload) => {
    isLoading.value = true;
    try {
      const res = await authService.forgotRequest(payload.email);
      toast.success(res?.data?.message || "Mã OTP đã được gửi. Kiểm tra hộp thư của bạn!");
        await router.push({ path: TOFORGOTOTP, query: { email: payload.email } });
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Gửi mã thất bại");
    } finally {
      isLoading.value = false;
    }
  });

  return { emailField, isLoading, handleVerify };
};
