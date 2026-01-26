import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { permissionService } from "~/services/user/permission.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPermission, PermissionPayloadFilter } from "~/interfaces/user/permission.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { PERMISSIONINDEX } from "~/utils/constants";

export const usePermission = (initialFilters?: PermissionPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IPermission>>>();
  const filters = ref<PermissionPayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null, module: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IPermission | null>(null);
  const moduleOptions = ref<string[]>([]);

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    module: z.string({ required_error: "admin.validation.moduleRequired" }).trim().min(1, "admin.validation.moduleRequired").max(255, "admin.validation.moduleMaxLength"),
    value: z.number({ required_error: "admin.validation.valueRequired" }).int().min(0, "admin.validation.valueMin"),
    title: z.string().max(255, "admin.validation.titleMaxLength").nullable().optional(),
    userId: z.number().int().positive().nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };

  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TPermissionCreate = z.infer<typeof createSchema>;
  type TPermissionUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPermissionCreate | TPermissionUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPermissionCreate | TPermissionUpdate) => {
      isLoading.value = true;
      try {
        const response = await permissionService.store(payload as Partial<IPermission>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(PERMISSIONINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          return;
        }
        console.error("Thêm mới thất bại:", error);
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
      const response = await permissionService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPermissionUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await permissionService.update(id, payload as Partial<IPermission>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(PERMISSIONINDEX);
        }
        // keep on page
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          return;
        }
        console.error("Cập nhật thất bại:", error);
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
      await permissionService.delete(id);
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
      await permissionService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IPermission) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id);
    selectedItem.value = null;
  };

  const fetchData = async () => {
    isLoading.value = true;
    try {
      const response = await permissionService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
      // Extract unique modules for filter
      const modules = new Set<string>();
      response?.data?.data?.forEach((item) => {
        if (item.module) modules.add(item.module);
      });
      moduleOptions.value = Array.from(modules).sort();
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const search = async (newFilters: BasePayloadFilter) => {
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
    config,
    page,
    values,
    errors,
    selectedIds,
    selectedItem,
    moduleOptions,
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

export type TPermissionCreate = Partial<IPermission>;
export type TPermissionUpdate = TPermissionCreate;
