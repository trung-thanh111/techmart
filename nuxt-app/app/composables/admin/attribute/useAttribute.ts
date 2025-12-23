import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { attributeService } from "~/services/attribute/attribute.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAttribute, AttributePayloadFilter } from "~/interfaces/attribute/attribute.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { ATTRIBUTEINDEX } from "~/utils/constants";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";

export const useAttribute = (initialFilters?: AttributePayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IAttribute>>>();
  const filters = ref<AttributePayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null, attribute_catalogue_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IAttribute | null>(null);
  const attributeCatalogueOptions = ref<Array<{ name: string; id: number }>>([]);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    description: z.string().max(255, "admin.validation.descriptionMaxLength").nullable().optional(),
    order: z.number().int().nonnegative().default(0),
    attributeCatalogueId: z.number({ required_error: "admin.validation.attributeCatalogueIdRequired" }).min(1, "admin.validation.attributeCatalogueIdRequired"),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TAttributeCreate = z.infer<typeof createSchema>;
  type TAttributeUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TAttributeCreate | TAttributeUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TAttributeCreate | TAttributeUpdate) => {
      isLoading.value = true;
      try {
        const response = await attributeService.store(payload as Partial<IAttribute>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(ATTRIBUTEINDEX);
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
      const response = await attributeService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TAttributeUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await attributeService.update(id, payload as Partial<IAttribute>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(ATTRIBUTEINDEX);
        }
        // keep on page for edit
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
      await attributeService.delete(id);
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
      await attributeService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IAttribute) => {
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
      const response = await attributeService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const loadAttributeCatalogue = async () => {
    try {
      const response = await attributeCatalogueService.all();
      const rows = response?.data ?? [];
      attributeCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu attribute catalogue:", error);
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
    loadAttributeCatalogue();
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
    attributeCatalogueOptions,
    slug,
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

export type TAttributeCreate = Partial<IAttribute>;
export type TAttributeUpdate = TAttributeCreate;
