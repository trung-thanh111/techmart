import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { postTagService } from "~/services/post/tag.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPostTag, TagPayloadFilter } from "~/interfaces/post/tag.interface";
import { POSTTAGINDEX } from "~/utils/constants";

export const usePostTag = (initialFilters?: TagPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IPostTag>>>();
  const filters = ref<TagPayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IPostTag | null>(null);
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
  type TPostTagCreate = z.infer<typeof createSchema>;
  type TPostTagUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPostTagCreate | TPostTagUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = handleSubmit(async (payload: TPostTagCreate | TPostTagUpdate) => {
    isLoading.value = true;
    try {
      const response = await postTagService.store(payload as Partial<IPostTag>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      resetForm();
      return navigateTo(POSTTAGINDEX);
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

  const show = async (id: number) => {
    try {
      const response = await postTagService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TPostTagUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await postTagService.update(id, payload as Partial<IPostTag>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(POSTTAGINDEX);
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

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await postTagService.delete(id);
      toast.success(t("admin.messages.deleteSuccess"));
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const handleToggleIsHot = async (item: any) => {
    try {
      const res = await postTagService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const setSelectedItem = (item: IPostTag) => {
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
      const response = await postTagService.pagination({
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

export type TPostTagCreate = Partial<IPostTag>;
export type TPostTagUpdate = TPostTagCreate;
