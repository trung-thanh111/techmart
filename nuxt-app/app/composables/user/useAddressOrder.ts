import { ref } from "vue";
import { useForm } from "vee-validate";
import { z } from "zod";
import { toTypedSchema } from "@vee-validate/zod";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { useAppToast } from "~/composables/useAppToast";
import { addressOrderService } from "~/services/user/address_order.service";
import type { IAddressOrder } from "~/interfaces/user/address_order.interface";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";

interface UseAddressOrderOptions {
  redirectPath?: string;
}

export const useAddressOrder = (initialFilters?: any, type: "create" | "update" = "create", options: UseAddressOrderOptions = {}) => {
  const { t } = useI18n();
  const router = useRouter();
  const toast = useAppToast();
  const isLoading = ref(false);
  const data = ref<IApiOk<IPaginate<IAddressOrder>> | IApiOk<IAddressOrder[]> | null>(null);
  const selectedItem = ref<IAddressOrder | null>(null);

  // Schema Validation
  const schema = z.object({
    title: z.string().min(1, t("validate.required", { field: t("profile.titleLabel") })),
    fullName: z.string().min(1, t("validate.required", { field: t("profile.fullName") })),
    email: z.string().email(t("validate.email")).nullable().optional(),
    phone: z.string().min(1, t("validate.required", { field: t("profile.phone") })),
    address: z.string().min(1, t("validate.required", { field: t("profile.address") })),
    countryCode: z.string().nullable().optional(),
    state: z.string().nullable().optional(),
    postalCode: z.string().min(1, t("validate.required", { field: t("profile.postalCode") })),
    isDefault: z.boolean().optional(),
  });

  const { handleSubmit, setValues, values } = useForm({
    validationSchema: toTypedSchema(schema),
  });

  // Actions
  const addressUserById = async (userId: number) => {
    isLoading.value = true;
    try {
      const response = await addressOrderService.addressUserById(userId);
      data.value = response;
    } catch (error) {
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  };

  const show = async (id: number) => {
    isLoading.value = true;
    try {
      const response = await addressOrderService.show(id);
      return response;
    } catch (error) {
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  };

  const handleStore = handleSubmit(async (values) => {
    isLoading.value = true;
    try {
      const payload: Partial<IAddressOrder> = {
        title: values.title,
        fullName: values.fullName,
        email: values.email || undefined,
        phone: values.phone,
        address: values.address,
        countryCode: values.countryCode || undefined,
        state: values.state || undefined,
        postalCode: values.postalCode,
        isDefault: values.isDefault,
      };

      const response = await addressOrderService.store(payload);
      if (response && response.code === 201) {
        toast.success(t("common.success"), { description: t("profile.addSuccess") });
        if (options.redirectPath) {
          router.push(options.redirectPath);
        }
      }
    } catch (error: any) {
      toast.error(t("common.error"), { description: error?.message || t("profile.addError") });
    } finally {
      isLoading.value = false;
    }
  });

  const handleUpdate = (isRedirect: boolean = true) =>
    handleSubmit(async (values) => {
      const val = values as any;
      let id = val.id;

      if (!id) {
        const route = useRoute();
        if (route.params.id) {
          id = Number(route.params.id);
        }
      }

      if (!id) return;

      isLoading.value = true;
      try {
        const payload: Partial<IAddressOrder> = {
          title: values.title,
          fullName: values.fullName,
          email: values.email || undefined,
          phone: values.phone,
          address: values.address,
          countryCode: values.countryCode || undefined,
          state: values.state || undefined,
          postalCode: values.postalCode,
          isDefault: values.isDefault,
        };

        const response = await addressOrderService.update(Number(id), payload);
        if (response) {
          toast.success(t("common.success"), { description: t("profile.updateSuccess") });
          if (isRedirect && options.redirectPath) {
            router.push(options.redirectPath);
          }
        }
      } catch (error: any) {
        toast.error(t("common.error"), { description: error?.message || t("profile.updateError") });
      } finally {
        isLoading.value = false;
      }
    })();

  const handleToggleIsDefault = async (item: IAddressOrder) => {
    if (!item.id) return;
    try {
      await addressOrderService.update(item.id, { isDefault: item.isDefault });
      toast.success(t("common.success"));
    } catch (error) {
      toast.error(t("common.error"));
    }
  };

  const confirmDelete = async () => {
    if (!selectedItem.value?.id) return;
    isLoading.value = true;
    try {
      await addressOrderService.delete(selectedItem.value.id);
      toast.success(t("common.success"), { description: t("profile.deleteSuccess") });
    } catch (error: any) {
      toast.error(t("common.error"), { description: error?.message || t("profile.deleteError") });
    } finally {
      isLoading.value = false;
      selectedItem.value = null;
    }
  };

  const setSelectedItem = (item: IAddressOrder | null) => {
    selectedItem.value = item;
  };

  return {
    isLoading,
    data,
    selectedItem,
    values,
    addressUserById,
    show,
    handleStore,
    handleUpdate,
    handleToggleIsDefault,
    confirmDelete,
    setSelectedItem,
    setValues,
  };
};
