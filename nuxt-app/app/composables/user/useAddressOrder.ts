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

export const useAddressValidation = () => {
  const { t } = useI18n();
  return z.object({
    title: z.string({ required_error: t("validation.required", { field: t("profile.titleLabel") }) }).min(1, t("validation.required", { field: t("profile.titleLabel") })),
    fullName: z.string({ required_error: t("validation.required", { field: t("profile.fullName") }) }).min(1, t("validation.required", { field: t("profile.fullName") })),
    email: z.string().email(t("validate.email")).nullable().optional().or(z.literal("")),
    phone: z.string({ required_error: t("validation.required", { field: t("profile.phone") }) }).min(1, t("validation.required", { field: t("profile.phone") })),
    address: z.string({ required_error: t("validation.required", { field: t("profile.address") }) }).min(1, t("validation.required", { field: t("profile.address") })),
    countryCode: z.string({ required_error: t("validation.required", { field: t("profile.countryCode") }) }).min(1, t("validation.required", { field: t("profile.countryCode") })),
    street: z.string({ required_error: t("validation.required", { field: t("profile.street") }) }).min(1, t("validation.required", { field: t("profile.street") })),
    city: z.string({ required_error: t("validation.required", { field: t("profile.city") }) }).min(1, t("validation.required", { field: t("profile.city") })),
    ward: z.string({ required_error: t("validation.required", { field: t("profile.ward") }) }).min(1, t("validation.required", { field: t("profile.ward") })),
    postalCode: z.string({ required_error: t("validation.required", { field: t("profile.postalCode") }) }).min(1, t("validation.required", { field: t("profile.postalCode") })),
    isDefault: z.boolean().optional(),
  });
};

export const useAddressOrder = (initialFilters?: any, type: "create" | "update" = "create", options: UseAddressOrderOptions = {}) => {
  const { t } = useI18n();
  const router = useRouter();
  const toast = useAppToast();
  const isLoading = ref(false);
  const data = ref<IApiOk<IPaginate<IAddressOrder>> | IApiOk<IAddressOrder[]> | null>(null);
  const selectedItem = ref<IAddressOrder | null>(null);

  const schema = useAddressValidation();

  const { handleSubmit, setValues, values } = useForm({
    validationSchema: toTypedSchema(schema),
  });

  const getAddressesList = (): IAddressOrder[] => {
    const rawData = data.value?.data as any;
    if (Array.isArray(rawData)) return rawData;
    if (rawData && Array.isArray(rawData.data)) return rawData.data;
    return [];
  };

  const updateAddressesList = (newAddresses: IAddressOrder[]) => {
    if (!data.value) {
      return;
    }
    const rawData = data.value.data as any;

    if (Array.isArray(rawData)) {
      data.value = {
        ...data.value,
        data: newAddresses as any,
      };
      return;
    }

    if (rawData && typeof rawData === "object") {
      data.value = {
        ...data.value,
        data: {
          ...rawData,
          data: newAddresses,
        } as any,
      };
    }
  };

  const getAddressesByUserId = async (userId: number) => {
    isLoading.value = true;
    try {
      const response = await addressOrderService.addressUserById(userId);
      data.value = response;
      return response;
    } catch (error) {
      toast.error(t("message.error"));
      throw error;
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
      toast.error(t("message.error"));
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const handleStore = handleSubmit(async (values) => {
    isLoading.value = true;

    try {
      const payload: any = {
        title: values.title,
        fullName: values.fullName,
        email: values.email || "",
        phone: values.phone,
        address: values.address,
        countryCode: values.countryCode || "",
        street: values.street || "",
        city: values.city || "",
        ward: values.ward || "",
        postalCode: values.postalCode,
        isDefault: values.isDefault || false,
      };

      const response = await addressOrderService.store(payload);
      if (response && (response.code === 201 || response.code === 200)) {
        toast.success(t("common.success"));
        if (response.data) {
          const newAddress = response.data as IAddressOrder;
          const currentAddresses = getAddressesList();
          if (newAddress.isDefault) {
            currentAddresses.forEach((addr) => {
              addr.isDefault = false;
            });
          }

          const updatedAddresses = [newAddress, ...currentAddresses];
          updateAddressesList(updatedAddresses);
        }

        if (options.redirectPath) {
          navigateTo(options.redirectPath);
        }

        return response;
      } else {
        toast.error(t("message.error"));
      }
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("message.error"));
      throw error;
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

      if (!id) {
        toast.error(t("message.error"));
        return;
      }
      isLoading.value = true;
      try {
        const payload: any = {
          title: values.title,
          fullName: values.fullName,
          email: values.email || "",
          phone: values.phone,
          address: values.address,
          countryCode: values.countryCode || "",
          street: values.street || "",
          city: values.city || "",
          ward: values.ward || "",
          postalCode: values.postalCode,
          isDefault: values.isDefault || false,
        };
        const response = await addressOrderService.update(Number(id), payload);
        if (response && (response.code === 200 || response.code === 201)) {
          toast.success(t("common.success"));

          if (response.data) {
            const updatedAddress = response.data as IAddressOrder;
            const currentAddresses = getAddressesList();
            const addressIndex = currentAddresses.findIndex((addr) => addr.id === Number(id));

            if (addressIndex !== -1) {
              if (updatedAddress.isDefault) {
                currentAddresses.forEach((addr, idx) => {
                  if (idx !== addressIndex) {
                    addr.isDefault = false;
                  }
                });
              }

              currentAddresses[addressIndex] = updatedAddress;
              updateAddressesList([...currentAddresses]);
            } else {
              const updatedAddresses = [updatedAddress, ...currentAddresses];
              updateAddressesList(updatedAddresses);
            }
          }
          if (options.redirectPath) {
            navigateTo(options.redirectPath);
          }

          return response;
        } else {
          toast.error(t("message.error"));
        }
      } catch (error: any) {
        toast.error(error?.response?.data?.message || t("message.error"));
        throw error;
      } finally {
        isLoading.value = false;
      }
    })();

  const handleToggleIsDefault = async (item: IAddressOrder) => {
    if (!item.id) {
      toast.error(t("message.error"));
      return;
    }

    const originalValue = item.isDefault;
    const currentAddresses = getAddressesList();
    const backupAddresses = currentAddresses.map((addr) => ({ ...addr }));

    try {
      if (item.isDefault) {
        currentAddresses.forEach((address) => {
          address.isDefault = address.id === item.id;
        });
      }
      updateAddressesList([...currentAddresses]);

      await addressOrderService.update(item.id, { isDefault: item.isDefault });

      toast.success(t("common.success"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("common.error"));
      item.isDefault = originalValue;
      updateAddressesList(backupAddresses);

      throw error;
    }
  };

  const confirmDelete = async () => {
    if (!selectedItem.value?.id) {
      toast.error(t("message.error"));
      return;
    }

    const itemToDelete = selectedItem.value;
    isLoading.value = true;
    try {
      await addressOrderService.delete(itemToDelete.id);
      const currentAddresses = getAddressesList();
      const updatedAddresses = currentAddresses.filter((addr) => addr.id !== itemToDelete.id);
      updateAddressesList(updatedAddresses);

      toast.success(t("common.success"), { description: t("profile.deleteSuccess") });
    } catch (error: any) {
      toast.error(t("message.error"), {
        description: error?.response?.data?.message || t("profile.deleteError"),
      });
      throw error;
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
    getAddressesByUserId,
    show,
    handleStore,
    handleUpdate,
    handleToggleIsDefault,
    confirmDelete,
    setSelectedItem,
    setValues,
    getAddressesList,
    updateAddressesList,
  };
};
