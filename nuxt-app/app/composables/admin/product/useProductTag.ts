import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { productTagService } from "~/services/product/tag.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProductTag, TagPayloadFilter } from "~/interfaces/product/tag.interface";
import { PRODUCTTAGINDEX } from "~/utils/constants";

export const useProductTag = (initialFilters?: TagPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IProductTag>>>();
  const filters = ref<TagPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IProductTag | null>(null);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    description: z.string().max(255, "admin.validation.descriptionMaxLength").nullable().optional(),
    color: z.string().max(7, "admin.validation.colorMaxLength").nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    isHot: z.boolean().default(false).optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TProductTagCreate = z.infer<typeof createSchema>;
  type TProductTagUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TProductTagCreate | TProductTagUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TProductTagCreate | TProductTagUpdate) => {
      isLoading.value = true;
      try {
        const response = await productTagService.store(payload as Partial<IProductTag>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(PRODUCTTAGINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          return;
        }
        console.error("Store error:", error);
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
      const response = await productTagService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TProductTagUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await productTagService.update(id, payload as Partial<IProductTag>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(PRODUCTTAGINDEX);
        }
        // keep on page
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          return;
        }
        console.error("Update error:", error);
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
      await productTagService.delete(id);
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
      await productTagService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const handleToggleIsHot = async (item: any) => {
    try {
      const res = await productTagService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const setSelectedItem = (item: IProductTag) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id);
    selectedItem.value = null;
  };

  // fetch
  const fetchData = async () => {
    isLoading.value = true;
    try {
      const response = await productTagService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
      console.error("Fetch error:", err);
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
    page,
    values,
    errors,
    config,
    slug,
    selectedIds,
    selectedItem,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    handleToggleIsHot,
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

export type TProductTagCreate = Partial<IProductTag>;
export type TProductTagUpdate = TProductTagCreate;
