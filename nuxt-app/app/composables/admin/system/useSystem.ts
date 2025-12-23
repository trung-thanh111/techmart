import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { systemService } from "~/services/system/system.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { ISystem, SystemPayloadFilter } from "~/interfaces/system/system.interface";
import { SYSTEMINDEX } from "~/utils/constants";

export const useSystem = (initialFilters?: SystemPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<ISystem>>>();
  const filters = ref<SystemPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<ISystem | null>(null);

  const schema = {
    keyword: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    content: z.string().nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };

  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TCreate = z.infer<typeof createSchema>;
  type TUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, setFieldError, resetForm } = useForm<TCreate | TUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = (shouldClose: boolean = false) => {
    const wrapped = handleSubmit(async (payload: TCreate | TUpdate) => {
      isLoading.value = true;
      try {
        const response = await systemService.store(payload as Partial<ISystem>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(SYSTEMINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          return;
        }
        toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
        throw error;
      } finally {
        isLoading.value = false;
      }
    });

    return wrapped();
  };

  const show = async (id: number) => {
    try {
      const response = await systemService.show(id);
      return response.data;
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.loadDataError"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose: boolean = false) => {
    const wrapped = handleSubmit(async (payload: TUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await systemService.update(id, payload as Partial<ISystem>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(SYSTEMINDEX);
        }
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          return;
        }
        toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      } finally {
        isLoading.value = false;
      }
    });

    return wrapped();
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await systemService.delete(id);
      toast.success(t("admin.messages.deleteSuccess"));
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const handleDeleteMultiple = async () => {
    if (selectedIds.value.length === 0) return;
    isLoading.value = true;
    try {
      await systemService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: ISystem) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id as number);
    selectedItem.value = null;
  };

  const fetchData = async () => {
    isLoading.value = true;
    try {
      const response = await systemService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
    } finally {
      isLoading.value = false;
    }
  };

  const search = async (newFilters: any) => {
    filters.value = { ...filters.value, ...newFilters };
    page.value = 1;
    selectedIds.value = [];
    await fetchData();
  };

  const setPage = async (newPage: number) => {
    page.value = newPage;
    selectedIds.value = [];
    await fetchData();
  };

  const updateSelectedIds = (ids: number[]) => {
    selectedIds.value = ids;
  };

  const getTotalItems = () => {
    return data.value?.data?.data?.length || 0;
  };

  const getAllItemIds = () => {
    return data.value?.data?.data?.map((item) => item.id) || [];
  };

  onMounted(() => {
    fetchData();
  });

  return {
    data,
    isLoading,
    filters,
    values,
    errors,
    selectedIds,
    selectedItem,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    setSelectedItem,
    confirmDelete,
    fetchData,
    search,
    setPage,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
  };
};

export type TSystemCreate = Partial<ISystem>;
export type TSystemUpdate = TSystemCreate;
