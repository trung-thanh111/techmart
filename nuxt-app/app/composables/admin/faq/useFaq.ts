import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { faqService } from "~/services/faq/faq.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IFaq, FaqPayloadFilter } from "~/interfaces/faq/faq.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { FAQINDEX } from "~/utils/constants";
import { faqCatalogueService } from "~/services/faq/catalogue.service";

export const useFaq = (initialFilters?: FaqPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IFaq>>>();
  const filters = ref<FaqPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null, faq_catalogue_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IFaq | null>(null);
  const faqCatalogueOptions = ref<Array<{ name: string; id: number }>>([]);
  const isDataLoaded = useState("faq-data-loaded", () => false);
  const isOptionsLoaded = useState("faq-options-loaded", () => false);

  const slug = computed(() => slugify(values.question || ""));

  const schema = {
    question: z.string({ required_error: "admin.validation.questionRequired" }).trim().min(1, "admin.validation.questionRequired").max(255, "admin.validation.questionMaxLength"),
    answer: z.string({ required_error: "admin.validation.answerRequired" }).trim().min(1, "admin.validation.answerRequired"),
    order: z.number().int().nonnegative().default(0),
    isHot: z.boolean().default(false).optional(),
    faqCatalogueId: z.number({ required_error: "admin.validation.faqCatalogueIdRequired" }).min(1, "admin.validation.faqCatalogueIdRequired"),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TFaqCreate = z.infer<typeof createSchema>;
  type TFaqUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TFaqCreate | TFaqUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TFaqCreate | TFaqUpdate) => {
      isLoading.value = true;
      try {
        const response = await faqService.store(payload as Partial<IFaq>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(FAQINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "question");
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
      const response = await faqService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TFaqUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await faqService.update(id, payload as Partial<IFaq>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(FAQINDEX);
        }
        // keep on page
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "question");
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

  const handleToggleIsHot = async (item: any) => {
    try {
      await faqService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await faqService.delete(id);
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
      await faqService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IFaq) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id);
    selectedItem.value = null;
  };

  // fetch
  const fetchData = async () => {
    if (isDataLoaded.value && page.value === 1) return; // Cache hit for first page
    isLoading.value = true;
    try {
      const response = await faqService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
      if (page.value === 1) isDataLoaded.value = true;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const loadFaqCatalogue = async () => {
    if (faqCatalogueOptions.value.length > 0) return; // Cache hit
    try {
      const response = await faqCatalogueService.all();
      const rows = response?.data ?? [];
      faqCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu faq catalogue:", error);
    }
  };

  const search = async (newFilters: BasePayloadFilter) => {
    filters.value = { ...filters.value, ...newFilters };
    page.value = 1;
    selectedIds.value = [];
    isDataLoaded.value = false; // Reset cache for new search
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

  // Optimized initialization with Promise.all
  const initializeData = async () => {
    // Load main data first
    await fetchData();

    // Load options in parallel
    if (!isOptionsLoaded.value) {
      await loadFaqCatalogue();
      isOptionsLoaded.value = true;
    }
  };

  onMounted(() => {
    initializeData();
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
    faqCatalogueOptions,
    slug,
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

export type TFaqCreate = Partial<IFaq>;
export type TFaqUpdate = TFaqCreate;
