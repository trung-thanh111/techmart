import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useRouter } from "vue-router";
import { authService } from "~/services/auth/auth.service";
import { TOFORGOTRESET } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";

const verifySchema = z.object({
  email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
  code: z.string({ required_error: "Bạn chưa nhập mã OTP" }).regex(/^\d{6}$/g, "Mã OTP phải gồm 6 chữ số"),
});
export type TForgotVerify = z.infer<typeof verifySchema>;

export const useForgotVerify = () => {
  const toast = useAppToast();
  const router = useRouter();
  const isLoading = ref(false);
  const canResendIn = ref<number>(0);
  let resendTimer: number | null = null;

  const { handleSubmit, defineField, setFieldValue, values } = useForm<TForgotVerify>({
    validationSchema: toTypedSchema(verifySchema),
    initialValues: { email: "", code: "" },
  });

  const [emailField] = defineField("email");
  const [codeField] = defineField("code");

  const startResendCountdown = (seconds = 60) => {
    canResendIn.value = seconds;
    if (resendTimer) window.clearInterval(resendTimer);
    resendTimer = window.setInterval(() => {
      if (canResendIn.value <= 0) {
        if (resendTimer) window.clearInterval(resendTimer);
        resendTimer = null;
      } else {
        canResendIn.value -= 1;
      }
    }, 1000);
  };

  const onMountedInitFromQuery = () => {
    const route = useRoute();
    const qEmail = String(route.query.email || "");
    if (qEmail) setFieldValue("email", qEmail);
    startResendCountdown(60);
  };

  const onSubmit = handleSubmit(async (payload) => {
    try {
      isLoading.value = true;
      const res = await authService.forgotVerify(payload);
      toast.success(res?.message || "Xác thực thành công");
      await router.push({ path: TOFORGOTRESET, query: { email: payload.email, code: payload.code } });
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Mã OTP không hợp lệ");
    } finally {
      isLoading.value = false;
    }
  });

  const resend = async () => {
    if (canResendIn.value > 0) return;
    try {
      const res = await authService.forgotResend({ email: values.email });
      toast.success(res?.message || "Đã gửi lại mã OTP");
      startResendCountdown(60);
    } catch (e: any) {
      toast.error(e?.response?.data?.message || "Gửi lại thất bại");
    }
  };

  onMounted(onMountedInitFromQuery);
  onBeforeUnmount(() => {
    if (resendTimer) window.clearInterval(resendTimer);
  });

  return { emailField, codeField, isLoading, onSubmit, resend, canResendIn, values };
};
