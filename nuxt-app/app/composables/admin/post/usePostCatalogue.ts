import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { postCatalogueService } from "~/services/post/catalogue.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPostCatalogue, CataloguePayloadFilter } from "~/interfaces/post/catalogue.interface";
import { POSTCATALOGUEINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";

export const usePostCatalogue = (
  initialFilters?: CataloguePayloadFilter,
  mode: "create" | "update" = "create"
) => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IPostCatalogue>>>();
  const filters = ref<CataloguePayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IPostCatalogue | null>(null);
  const slug = computed(() => slugify(values.name || ""));
  const postCatalogueOptions = ref<{ name: string; value: number }[]>([]);

  const schema = {
    name: z.string({ required_error: 'admin.validation.nameRequired' }).trim().min(1, 'admin.validation.nameRequired').max(255, 'admin.validation.nameMaxLength'),
    description: z.string().max(255, 'admin.validation.descriptionMaxLength').nullable().optional(),
    order: z.number().min(0, 'admin.validation.orderMin').nullable().optional(),
    image: z.any().optional().refine((file) => !file || file.size <= MAX_FILE_SIZE, 'admin.common.maxFileSize').refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), 'admin.common.acceptedImageTypes'),
    publish: z.number({ required_error: 'admin.validation.statusRequired' }).refine((val) => [0, 1, 2].includes(val), { message: 'admin.validation.statusInvalid' }),
    metaTitle: z.string().max(255, 'admin.validation.metaTitleMaxLength').nullable().optional(),
    metaDescription: z.string().max(255, 'admin.validation.metaDescriptionMaxLength').nullable().optional(),
    metaKeyword: z.string().max(255, 'admin.validation.metaKeywordMaxLength').nullable().optional(),
    parentId: z.number().nullable().optional(),
    isHot: z.boolean().default(false).optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TPostCatalogueCreate = z.infer<typeof createSchema>;
  type TPostCatalogueUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPostCatalogueCreate | TPostCatalogueUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPostCatalogueCreate | TPostCatalogueUpdate) => {
      isLoading.value = true;
      try {
        const response = await postCatalogueService.store(payload as Partial<IPostCatalogue>);
        toast.success(response?.message || t('admin.messages.createSuccess'));
        if (shouldClose) {
          return navigateTo(POSTCATALOGUEINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, 'name');
          return;
        }
        console.error("Store error:", error);
        toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
        throw error;
      } finally {
        isLoading.value = false;
      }
    });

    return wrapped();
  };

  const show = async (id: number) => {
    try {
      const response = await postCatalogueService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPostCatalogueUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await postCatalogueService.update(id, payload as Partial<IPostCatalogue>);
        toast.success(t('admin.messages.updateSuccess'));
        if (shouldClose) {
          return navigateTo(POSTCATALOGUEINDEX);
        }
        // keep on page
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, 'name'); return;
        }
        console.error("Store error:", error);
        toast.error(error?.response?.data?.message || t('admin.messages.updateFailed'));
      } finally {
        isLoading.value = false;
      }
    });

    return wrapped();
  };

  const handleToggleIsHot = async (item: any) => {
    try {
      await postCatalogueService.update(item.id, { isHot: item.isHot });
      toast.success(t('admin.messages.updateSuccess'));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.updateFailed'));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await postCatalogueService.delete(id);
      toast.success(t('admin.messages.deleteSuccess'));
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.deleteFailed'));
    } finally {
      isLoading.value = false;
    }
  };

  const handleDeleteMultiple = async () => {
    if (selectedIds.value.length === 0) return;
    isLoading.value = true;
    try {
      await postCatalogueService.deleteMultiple(selectedIds.value);
      toast.success(t('admin.messages.deleteMultipleSuccess', { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.deleteFailed'));
    } finally {
      isLoading.value = false;
    }
  };

  const loadpostCatalogue = async () => {
    try {
      const response = await postCatalogueService.all();
      const rows = response?.data ?? [];
      postCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows])
        .map((item: any) => ({ name: item.name, value: Number(item.id) }))
        .filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi! khi tải dữ liệu post catalogue all :", error);
    }
  };

  const setSelectedItem = (item: IPostCatalogue) => {
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
      const response = await postCatalogueService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t('admin.messages.loadDataError'));
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
    loadpostCatalogue();
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
    postCatalogueOptions,
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
    loadpostCatalogue,
    search,
    setPage,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
  };
};

export type TPostCatalogueCreate = Partial<IPostCatalogue>;
export type TPostCatalogueUpdate = TPostCatalogueCreate;
