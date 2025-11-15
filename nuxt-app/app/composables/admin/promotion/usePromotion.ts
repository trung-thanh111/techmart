import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { promotionService } from "~/services/promotion/promotion.service";
import { promotionCatalogueService } from "~/services/promotion/catalogue.service";
import { productService } from "~/services/product/product.service";
import { productCatalogueService } from "~/services/product/catalogue.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPromotion, PromotionPayloadFilter } from "~/interfaces/promotion/promotion.interface";
import { PROMOTIONINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";

export const usePromotion = (initialFilters?: PromotionPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IPromotion>>>();
  const filters = ref<PromotionPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IPromotion | null>(null);

  // Options for selects
  const promotionCatalogueOptions = ref<{ name: string; value: number }[]>([]);
  const productOptions = ref<{ name: string; value: number }[]>([]);
  const productCatalogueOptions = ref<{ name: string; value: number }[]>([]);
  const productVariantOptions = ref<Record<number, { name: string; value: number }[]>>({});

  const promotionTargetSchema = z
    .object({
      productId: z.number().nullable().optional(),
      productVariantId: z.number().nullable().optional(),
      productCatalogueId: z.number().nullable().optional(),
      isGift: z.boolean().default(false),
      // minQuantity: z.number().min(1, "admin.validation.minQuantityRequired").default(1),
      // maxQuantity: z.number().nullable().optional(),
      minValue: z.number().nullable().optional(),
      maxValue: z.number().nullable().optional(),
    })
    .refine(
      (data) => {
        // Chỉ cho phép 1 trong 3 field được set (theo constraint database)
        const count = [data.productId, data.productVariantId, data.productCatalogueId].filter((v) => v !== null && v !== undefined).length;
        return count === 1;
      },
      { message: "admin.validation.promotionTargetRequired", path: ["productId"] }
    );

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    couponCode: z.string().max(50, "admin.validation.couponCodeMaxLength").nullable().optional(),
    description: z.string().max(1000, "admin.validation.descriptionMaxLength").nullable().optional(),
    promotionCatalogueId: z.number({ required_error: "admin.validation.promotionCatalogueRequired" }).min(1, "admin.validation.promotionCatalogueRequired"),
    discountValue: z.number({ required_error: "admin.validation.discountValueRequired" }).min(0, "admin.validation.discountValueMin"),
    discountType: z.enum(["percent", "amount"], { required_error: "admin.validation.discountTypeRequired" }),
    maxDiscountValue: z.number().nullable().optional(),
    startDate: z.string().nullable().optional(),
    endDate: z.string().nullable().optional(),
    priority: z.number().min(0, "admin.validation.priorityMin").default(0).nullable().optional(),
    canCombine: z.boolean().default(false),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    promotionTargets: z.array(promotionTargetSchema).min(1, "admin.validation.promotionTargetsRequired"),
  };

  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TPromotionCreate = z.infer<typeof createSchema>;
  type TPromotionUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPromotionCreate | TPromotionUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    initialValues: {
      promotionTargets: [],
      canCombine: false,
      priority: 0,
      discountType: "percent",
    } as any,
  });

  // Load options
  const loadPromotionCatalogueOptions = async () => {
    try {
      const response = await promotionCatalogueService.all();
      const rows = response?.data ?? [];
      promotionCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Error loading promotion catalogue options:", error);
    }
  };

  const loadProductOptions = async () => {
    try {
      const response = await productService.all();
      const rows = response?.data ?? [];
      productOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Error loading product options:", error);
    }
  };

  const loadProductCatalogueOptions = async () => {
    try {
      const response = await productCatalogueService.all();
      const rows = response?.data ?? [];
      productCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Error loading product catalogue options:", error);
    }
  };

  // Load product variants by product ID
  const loadProductVariants = async (productId: number) => {
    if (!productId || productVariantOptions.value[productId]) {
      return productVariantOptions.value[productId] || [];
    }
    try {
      const response = await productService.show(productId, ["product_variants"]);
      const product = response?.data;
      const variants = product?.productVariants || [];
      productVariantOptions.value[productId] = variants.map((variant: any) => ({
        name: variant.name || variant.code || `Variant #${variant.id}`,
        value: Number(variant.id),
      }));
      return productVariantOptions.value[productId];
    } catch (error) {
      console.error("Error loading product variants:", error);
      return [];
    }
  };

  // Generate coupon code: 4 uppercase letters + 4 random numbers
  const generateCouponCode = (): string => {
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const numbers = "0123456789";
    let code = "";
    // 4 uppercase letters
    for (let i = 0; i < 4; i++) {
      code += letters.charAt(Math.floor(Math.random() * letters.length));
    }
    // 4 random numbers
    for (let i = 0; i < 4; i++) {
      code += numbers.charAt(Math.floor(Math.random() * numbers.length));
    }
    return code;
  };

  // store
  const handleStore = handleSubmit(async (payload: TPromotionCreate | TPromotionUpdate) => {
    isLoading.value = true;
    try {
      const response = await promotionService.store(payload as Partial<IPromotion>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      resetForm();
      return navigateTo(PROMOTIONINDEX);
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
      const response = await promotionService.show(id, ["promotion_targets", "promotion_catalogues"]);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TPromotionUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await promotionService.update(id, payload as Partial<IPromotion>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(PROMOTIONINDEX);
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
      await promotionService.delete(id);
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
      await promotionService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IPromotion) => {
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
      const response = await promotionService.pagination({
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
    if (mode === "create" || mode === "update") {
      loadPromotionCatalogueOptions();
      loadProductOptions();
      loadProductCatalogueOptions();
    } else {
      fetchData();
    }
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
    promotionCatalogueOptions,
    productOptions,
    productCatalogueOptions,
    productVariantOptions,
    loadProductVariants,
    generateCouponCode,
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
    loadPromotionCatalogueOptions,
    loadProductOptions,
    loadProductCatalogueOptions,
  };
};

export type TPromotionCreate = Partial<IPromotion>;
export type TPromotionUpdate = TPromotionCreate;
