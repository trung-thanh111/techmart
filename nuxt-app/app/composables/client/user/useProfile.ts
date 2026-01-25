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

  // Validation Schema
  const schema = z.object({
    name: z.string().min(1, t("validate.required", { field: t("client.profile.form.name") })),
    email: z
      .string()
      .email(t("validate.email"))
      .min(1, t("validate.required", { field: t("client.profile.form.email") })),
    phone: z.string().nullable().optional(),
    firstName: z.string().nullable().optional(),
    lastName: z.string().nullable().optional(),
    birthday: z.string().nullable().optional(),
    gender: z.string().nullable().optional(),
    description: z.string().nullable().optional(),
    address: z.string().nullable().optional(),
    image: z.any().optional(),
  });

  const { handleSubmit, setValues, values, resetForm } = useForm({
    validationSchema: toTypedSchema(schema),
  });

  const genderOptions = [
    { name: t("client.common.male"), value: "male" },
    { name: t("client.common.female"), value: "female" },
    { name: t("client.common.other"), value: "other" },
  ];

  const fetchProfile = async () => {
    try {
      const res = await profileService.getProfile();
      if (res?.data) {
        const user = res.data; // Response wrapper might vary, check api resource
        // ApiResource::ok(new UserResource($user)) -> { data: user, message: ... }
        // So res.data IS the user object (UserResource transforms it).

        authStore.user = user; // Sync store

        setValues({
          name: user.name,
          email: user.email,
          firstName: user.firstName,
          lastName: user.lastName,
          phone: user.phone,
          address: user.address,
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
      const response = await profileService.updateProfile(values);
      // Check response code or status. ApiResource::ok uses 200.
      if (response) {
        if (toast)
          toast.success(t("admin.common.success"), {
            description: t("admin.common.update_success"),
          });
        if (response.data) {
          authStore.user = response.data;
        }
      }
    } catch (error: any) {
      if (toast)
        toast.error(t("admin.common.error"), {
          description: error?.message || t("admin.common.update_error"),
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
