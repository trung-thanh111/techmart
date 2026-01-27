import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useRouter } from "vue-router";
import { authService } from "~/services/auth/auth.service";
import { TOLOGIN } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";

const resetSchema = z
    .object({
        email: z.string({ required_error: "Bạn chưa nhập Email" }).email("Email không hợp lệ"),
        code: z.string({ required_error: "Thiếu mã OTP" }).regex(/^\d{6}$/g, "Mã OTP phải gồm 6 chữ số"),
        password: z.string({ required_error: "Bạn chưa nhập Mật khẩu" }).min(8, "Tối thiểu 8 ký tự").regex(/[a-z]/, "Phải có ít nhất 1 chữ thường (a-z)").regex(/[A-Z]/, "Phải có ít nhất 1 chữ hoa (A-Z)").regex(/\d/, "Phải có ít nhất 1 số (0-9)"),
        passwordConfirmation: z.string({ required_error: "Bạn chưa xác nhận mật khẩu" }),
    })
    .refine((d) => d.password === d.passwordConfirmation, {
        message: "Mật khẩu xác nhận không khớp",
        path: ["passwordConfirmation"],
    });
export type TForgotReset = z.infer<typeof resetSchema>;

export const useForgotReset = () => {
    const toast = useAppToast();
    const router = useRouter();
    const isLoading = ref(false);

    const { handleSubmit, defineField, setFieldValue, values } = useForm<TForgotReset>({
        validationSchema: toTypedSchema(resetSchema),
        initialValues: { email: "", code: "", password: "", passwordConfirmation: "" },
    });

    const [emailField] = defineField("email");
    const [codeField] = defineField("code");
    const [passwordField] = defineField("password");
    const [passwordConfirmationField] = defineField("passwordConfirmation");

    const onMountedInitFromQuery = () => {
        const route = useRoute();
        const qEmail = String(route.query.email || "");
        const qCode = String(route.query.code || "");
        if (qEmail) setFieldValue("email", qEmail);
        if (qCode) setFieldValue("code", qCode);
    };

    const onSubmit = handleSubmit(async (payload) => {
        try {
            isLoading.value = true;
            const res = await authService.forgotReset(payload);
            toast.success(res?.data?.message || "Đặt lại mật khẩu thành công");
            await router.push({ path: TOLOGIN });
        } catch (e: any) {
            toast.error(e?.response?.data?.message || "Đặt lại mật khẩu thất bại");
        } finally {
            isLoading.value = false;
        }
    });

    onMounted(onMountedInitFromQuery);

    return {
        emailField,
        codeField,
        passwordField,
        passwordConfirmationField,
        isLoading,
        onSubmit,
        values,
    };
};
