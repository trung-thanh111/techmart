import { useForm } from "vee-validate";
import { z } from "zod";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { profileService } from "~/services/user/profile.service";
import { useAuthStore } from "~/stores/auth/auth";

export const useProfile = () => {
  const toast = useAppToast();
  const authStore = useAuthStore();
  const { t } = useI18n();
  const isLoading = ref(false);

  const schema = z.object({
    name: z.string().min(1, t("validate.required", { field: t("profile.form.name") })),
    email: z
      .string()
      .email(t("validate.email"))
      .min(1, t("validate.required", { field: t("profile.form.email") })),
    phone: z.string().nullable().optional(),
    firstName: z.string().nullable().optional(),
    lastName: z.string().nullable().optional(),
    birthday: z.string().nullable().optional(),
    gender: z.string().nullable().optional(),
    description: z.string().nullable().optional(),
    street: z.string().nullable().optional(),
    city: z.string().nullable().optional(),
    ward: z.string().nullable().optional(),
    image: z.any().optional(),
  });

  const { handleSubmit, setValues, values, resetForm } = useForm({
    validationSchema: toTypedSchema(schema),
  });

  const genderOptions = [
    { name: t("common.male"), value: "male" },
    { name: t("common.female"), value: "female" },
    { name: t("common.other"), value: "other" },
  ];

  const fetchProfile = async () => {
    try {
      const res = await profileService.getProfile();
      if (res?.data) {
        const user = res.data;
        authStore.user = user;

        setValues({
          name: user.name,
          email: user.email,
          firstName: user.firstName,
          lastName: user.lastName,
          phone: user.phone,
          street: user.street,
          city: user.city,
          ward: user.ward,
          birthday: user.birthday,
          gender: user.gender,
          description: user.description,
          image: user.image,
        });
        return user;
      }
    } catch (e) {
      console.error(e);
    }
  };

  const updateProfile = handleSubmit(async (values) => {
    isLoading.value = true;
    try {
      if (!authStore.user?.id) {
        throw new Error(t("common.user_not_found"));
      }

      const payload = { ...values };
      if (payload.image && !(payload.image instanceof File)) {
        delete payload.image;
      }

      const response = await profileService.updateProfile(payload);
      if (response) {
        if (toast)
          toast.success(t("common.success"), {
            description: t("common.update_success"),
          });
        if (response.data) {
          authStore.user = response.data;
        }
      }
    } catch (error: any) {
      if (toast)
        toast.error(t("message.error"), {
          description: error?.message || t("common.update_error"),
        });
    } finally {
      isLoading.value = false;
    }
  });

  return {
    isLoading,
    genderOptions,
    fetchProfile,
    handleUpdateProfile: updateProfile,
    setValues,
    values,
  };
};
