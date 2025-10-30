import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { inventoryService } from "~/services/inventory/inventory.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IInventory, InventoryPayloadFilter } from "~/interfaces/inventory/inventory.interface";
import { INVENTORYINDEX } from "~/utils/constants";
import { agencyService } from "~/services/agency/agency.service";
import { productService } from "~/services/product/product.service";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

export const useInventory = (initialFilters?: InventoryPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IInventory>>>();
  const filters = ref<InventoryPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IInventory | null>(null);
  const productOptions = ref<{ name: string; id: number }[]>([]);
  const agencyOptions = ref<{ name: string; id: number }[]>([]);

  const schema = {
    productVariantId: z
      .number({
        required_error: "admin.validation.productVariantRequired",
        invalid_type_error: "admin.validation.productVariantRequired",
      })
      .int()
      .positive("admin.validation.productVariantRequired"),
    agencyId: z
      .number({
        required_error: "admin.validation.agencyRequired",
        invalid_type_error: "admin.validation.agencyRequired",
      })
      .int()
      .positive("admin.validation.agencyRequired"),
    stock: z
      .number({
        required_error: "admin.validation.stockRequired",
      })
      .int()
      .nonnegative({ message: "admin.validation.minValue" })
      .default(0),
    priceOverride: z
      .number({
        invalid_type_error: "admin.validation.priceInvalid",
      })
      .nonnegative({ message: "admin.validation.minValue" })
      .nullable()
      .optional(),
    publish: z
      .number({
        required_error: "admin.validation.statusRequired",
        invalid_type_error: "admin.validation.statusInvalid",
      })
      .refine((val) => [0, 1, 2].includes(val), {
        message: "admin.validation.statusInvalid",
      })
      .default(0),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TInventoryCreate = z.infer<typeof createSchema>;
  type TInventoryUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TInventoryCreate | TInventoryUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = handleSubmit(async (payload: TInventoryCreate | TInventoryUpdate) => {
    console.info("payload:", payload);
    isLoading.value = true;
    try {
      const response = await inventoryService.store(payload as Partial<IInventory>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      resetForm();
      return navigateTo(INVENTORYINDEX);
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
      const response = await inventoryService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TInventoryUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await inventoryService.update(id, payload as Partial<IInventory>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(INVENTORYINDEX);
    } catch (error: any) {
      if (error?.data?.isValidationError) {
        errorToField(setFieldError, error?.data?.errors, t, "name");
        return;
      }
      console.error("Store error:", error);
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
    } finally {
      isLoading.value = false;
    }
  });

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await inventoryService.delete(id);
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
      await inventoryService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IInventory) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id as any);
    selectedItem.value = null;
  };

  // fetch
  const fetchData = async () => {
    isLoading.value = true;
    try {
      const response = await inventoryService.pagination({
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

  const loadProduct = async () => {
    if (productOptions.value.length > 0) return; // Cache hit
    try {
      const response = await productService.paginationPublic({
        type: "all",
        publish: 2,
      });
      const rows = response?.data ?? [];
      productOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu post:", error);
    }
  };

  const loadAgency = async () => {
    if (agencyOptions.value.length > 0) return; // Cache hit
    try {
      const response = await agencyService.paginationPublic({
        type: "all",
        public: 2,
      });
      const rows = response?.data ?? [];
      agencyOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu post:", error);
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

  const handleChangeProduct = async (productId: number | null, setValues: (val: Record<string, any>) => void, productVariantOptions: { value: { id: number; name: string }[] }, disableProductVariant: { value: boolean }, priceOverrideRef?: { value: number | null }) => {
    if (!productId) {
      productVariantOptions.value = [];
      disableProductVariant.value = true;
      setValues && setValues({ productVariantId: undefined });
      if (priceOverrideRef) priceOverrideRef.value = null;
      return;
    }
    const resp = await productService.show(productId);
    let variants: IProductVariant[] = resp?.data?.productVariants || [];
    productVariantOptions.value = variants.map((v) => ({ id: v.id!, name: v.name! }));
    disableProductVariant.value = false;
    setValues && setValues({ productVariantId: undefined });
  };

  onMounted(() => {
    fetchData();
    loadAgency();
    loadProduct();
  });

  return {
    data,
    isLoading,
    filters,
    page,
    values,
    errors,
    config,
    selectedIds,
    selectedItem,
    agencyOptions,
    productOptions,
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
    handleChangeProduct,
  };
};

export type TInventoryCreate = Partial<IInventory>;
export type TInventoryUpdate = TInventoryCreate;
