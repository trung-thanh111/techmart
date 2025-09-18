// composables/useUserCatalogue.ts
import z from "zod";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { faqCatalogueService } from "~/services/faq/catalogue.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IFaqCatalogue, CataloguePayloadFilter } from "~/interfaces/faq/catalogue.interface";
import { FAQCATALOGUEINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";
import { useI18n } from "#imports";

export const useFaqCatalogue = (
  initialFilters?: CataloguePayloadFilter,
  mode: "create" | "update" = "create"
) => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IFaqCatalogue>>>();
  const filters = ref<CataloguePayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IFaqCatalogue | null>(null);
  const slug = computed(() => slugify(values.name || ""));
  const faqCatalogueOptions = ref<{ name: string; id: number }[]>([]);


  const schema = {
    name: z.string({ required_error: 'admin.validation.nameRequired' }).trim().min(1, 'admin.validation.nameRequired').max(255, 'admin.validation.nameMaxLength'),
    description: z.string().max(255, 'admin.validation.descriptionMaxLength').nullable().optional(),
    order: z.number().min(0, 'admin.validation.orderMin').nullable().optional(),
    image: z.any().optional().refine((file) => !file || file.size <= MAX_FILE_SIZE, 'admin.common.maxFileSize').refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), 'admin.common.acceptedImageTypes'),
    publish: z.number({ required_error: 'admin.validation.statusRequired' }).refine((val) => [0, 1, 2].includes(val), { message: 'admin.validation.statusInvalid' }),
    isHot: z.boolean().default(false).optional(),
    faqCatalogues: z.array(z.number()).nullable().optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TFaqCatalogueCreate = z.infer<typeof createSchema>;
  type TFaqCatalogueUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TFaqCatalogueCreate | TFaqCatalogueUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = handleSubmit(async (payload: TFaqCatalogueCreate | TFaqCatalogueUpdate) => {
    isLoading.value = true;
    try {
      const response = await faqCatalogueService.store(payload as Partial<IFaqCatalogue>);
      toast.success(response?.message || t('admin.messages.createSuccess'));
      return navigateTo(FAQCATALOGUEINDEX);
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

  const show = async (id: number) => {
    try {
      const response = await faqCatalogueService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TFaqCatalogueUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await faqCatalogueService.update(id, payload as Partial<IFaqCatalogue>);
      toast.success(t('admin.messages.updateSuccess'));
      return navigateTo(FAQCATALOGUEINDEX);
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

  const handleToggleIsHot = async (item: any) => {
    try {
      await faqCatalogueService.update(item.id, { isHot: item.isHot });
      toast.success(t('admin.messages.updateSuccess'));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.updateFailed'));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await faqCatalogueService.delete(id);
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
      await faqCatalogueService.deleteMultiple(selectedIds.value);
      toast.success(t('admin.messages.deleteMultipleSuccess', { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.deleteFailed'));
    } finally {
      isLoading.value = false;
    }
  };

  const loadFaqCatalogue = async () => {
    try {
      const response = await faqCatalogueService.all();
      const rows = response?.data ?? [];
      const currentId = Number(useRoute().params.id || 0);

      faqCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows])
        .filter((item: any) => !currentId || item.id !== currentId)
        .map((item: any) => ({
          name: item.name,
          id: Number(item.id),
        }))
        .filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi! khi tải dữ liệu faq catalogue all:", error);
    }
  };

  const setSelectedItem = (item: IFaqCatalogue) => {
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
      const response = await faqCatalogueService.pagination({
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
    loadFaqCatalogue();
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
    faqCatalogueOptions,
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

export type TFaqCatalogueCreate = Partial<IFaqCatalogue>;
export type TFaqCatalogueUpdate = TFaqCatalogueCreate;
