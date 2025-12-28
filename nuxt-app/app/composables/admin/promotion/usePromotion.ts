import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { promotionService } from "~/services/promotion/promotion.service";
import { promotionCatalogueService } from "~/services/promotion/catalogue.service";
import { userCatalogueService } from "~/services/user/catalogue.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPromotion, PromotionPayloadFilter } from "~/interfaces/promotion/promotion.interface";
import { PROMOTIONINDEX, PROMOTION_TYPES, PROMOTION_PUBLISH_STATUS, PROMOTION_RULE_TYPES, PROMOTION_OPERATORS, PROMOTION_ACTION_TYPES, PROMOTION_APPLY_TO, PROMOTION_TARGET_TYPES, PROMOTION_LOGIC_OPERATORS } from "~/utils/constants";

// Schema definitions for types
const ruleSchemaForTypes = z.object({
  ruleGroup: z.number().min(0),
  logicOperator: z.enum(["AND", "OR"]),
  ruleType: z.enum(["min_order_total", "product_qty", "product_qty_by_category", "customer_group", "time_slot"]),
  operator: z.enum(["=", "!=", ">", ">=", "<", "<=", "IN", "NOT_IN", "BETWEEN"]),
  value: z.union([z.string(), z.number(), z.object({ from: z.string(), to: z.string() }), z.object({ category_id: z.number(), min_qty: z.number() })]),
  description: z.string().optional(),
});

const actionSchemaForTypes = z.object({
  executionOrder: z.number().min(0),
  actionType: z.enum(["percent", "fixed", "free_shipping", "bxgy", "tiered"]),
  actionValue: z.number().nullable().optional(),
  maxDiscount: z.number().min(0).nullable().optional(),
  minDiscount: z.number().min(0).nullable().optional(),
  applyTo: z.enum(["order", "cart_subtotal", "shipping_fee", "cheapest_item", "most_expensive_item", "specific_products"]),
  config: z.record(z.any()).nullable().optional(),
});

const targetSchemaForTypes = z.object({
  targetType: z.enum(["order", "product", "category", "brand", "customer_group", "location"]),
  targetId: z.number().nullable().optional(),
  isIncluded: z.boolean(),
  metadata: z.record(z.any()).nullable().optional(),
});

const promotionSchemaForTypes = z.object({
  name: z.string().min(1).max(255),
  code: z.string().max(50).nullable().optional(),
  description: z.string().nullable().optional(),
  image: z.string().nullable().optional(),
  type: z.enum(["order", "product", "flash_sale", "bundle", "bxgy", "free_ship", "voucher"]),
  priority: z.number().min(0).default(0),
  promotionCatalogueId: z.number().nullable().optional(),
  canCombine: z.boolean().default(false),
  combinableWith: z.array(z.number()).nullable().optional(),
  usageLimit: z.number().min(1).nullable().optional(),
  usagePerCustomer: z.number().min(1).nullable().optional(),
  startDate: z.union([z.string(), z.date()]).nullable().optional(),
  endDate: z.union([z.string(), z.date()]).nullable().optional(),
  publish: z.number().refine((val) => [0, 1, 2].includes(val)),
  rules: z.array(ruleSchemaForTypes).optional(),
  actions: z.array(actionSchemaForTypes).min(1),
  targets: z.array(targetSchemaForTypes).optional(),
});

export type TPromotionCreate = z.infer<typeof promotionSchemaForTypes>;
export type TPromotionUpdate = z.infer<typeof promotionSchemaForTypes>;

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

  // Options with caching
  const promotionCatalogueOptions = ref<Array<{ name: string; value: number }>>([]);
  const customerGroupOptions = ref<Array<{ name: string; value: number }>>([]);
  // Cache flags to avoid reloading
  const isOptionsLoaded = useState("promotion-options-loaded", () => false);

  const ruleSchema = z.object({
    ruleGroup: z.number().min(0),
    logicOperator: z.enum(["AND", "OR"]),
    ruleType: z.enum(["min_order_total", "product_qty", "product_qty_by_category", "customer_group", "time_slot"]),
    operator: z.enum(["=", "!=", ">", ">=", "<", "<=", "IN", "NOT_IN", "BETWEEN"]),
    value: z.union([z.string(), z.number(), z.object({ from: z.string(), to: z.string() }), z.object({ category_id: z.number(), min_qty: z.number() })]),
    description: z.string().optional(),
  });

  const actionSchema = z.object({
    executionOrder: z.number().min(0),
    actionType: z.enum(["percent", "fixed", "free_shipping", "bxgy", "tiered"]),
    actionValue: z.number().nullable().optional(),
    maxDiscount: z.number().min(0).nullable().optional(),
    minDiscount: z.number().min(0).nullable().optional(),
    applyTo: z.enum(["order", "cart_subtotal", "shipping_fee", "cheapest_item", "most_expensive_item", "specific_products"]),
    config: z.record(z.any()).nullable().optional(),
  });

  const targetSchema = z.object({
    targetType: z.enum(["order", "product", "category", "brand", "customer_group", "location"]),
    targetId: z.number().nullable().optional(),
    isIncluded: z.boolean(),
    metadata: z.record(z.any()).nullable().optional(),
  });

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    code: z.string().max(50).nullable().optional(),
    description: z.string().nullable().optional(),
    image: z.string().nullable().optional(),
    type: z.enum(["order", "product", "flash_sale", "bundle", "bxgy", "free_ship", "voucher"]),
    priority: z.number().min(0).default(0),
    promotionCatalogueId: z.number().nullable().optional(),
    canCombine: z.boolean().default(false),
    combinableWith: z.array(z.number()).nullable().optional(),
    usageLimit: z.number().min(1).nullable().optional(),
    usagePerCustomer: z.number().min(1).nullable().optional(),
    startDate: z.union([z.string(), z.date()]).nullable().optional(),
    endDate: z.union([z.string(), z.date()]).nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    rules: z.array(ruleSchema).optional(),
    actions: z.array(actionSchema).min(1, t("admin.validation.actionsRequired")),
    targets: z.array(targetSchema).optional(),
  };

  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });

  type TPromotionCreate = z.infer<typeof createSchema>;
  type TPromotionUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPromotionCreate | TPromotionUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
    initialValues: {
      type: "order",
      priority: 0,
      canCombine: false,
      publish: 2,
      rules: [],
      actions: [],
      targets: [],
    },
  });

  const loadPromotionCatalogueOptions = async () => {
    if (promotionCatalogueOptions.value.length > 0) return; // Cache hit
    try {
      const response = await promotionCatalogueService.all();
      const rows = response?.data ?? [];
      promotionCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu promotion catalogue:", error);
    }
  };

  const loadCustomerGroupOptions = async () => {
    if (customerGroupOptions.value.length > 0) return; // Cache hit
    try {
      const response = await userCatalogueService.all();
      const rows = response?.data ?? [];
      customerGroupOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu customer group:", error);
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

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPromotionCreate | TPromotionUpdate) => {
      isLoading.value = true;
      try {
        // prepare payload: serialize dates and ensure arrays are plain
        const payloadToSend: any = JSON.parse(JSON.stringify(payload || {}));
        const serializeDate = (d: any) => {
          if (!d) return null;
          if (typeof d === "string") return d;
          if (d instanceof Date) return d.toISOString().slice(0, 19).replace("T", " ");
          return String(d);
        };
        payloadToSend.startDate = serializeDate((payload as any).startDate);
        payloadToSend.endDate = serializeDate((payload as any).endDate);
        payloadToSend.rules = Array.isArray((payload as any).rules) ? (payload as any).rules : [];
        payloadToSend.actions = Array.isArray((payload as any).actions) ? (payload as any).actions : [];
        payloadToSend.targets = Array.isArray((payload as any).targets) ? (payload as any).targets : [];
        const response = await promotionService.store(payloadToSend as Partial<IPromotion>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(PROMOTIONINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          errorToField(setFieldError, error?.data?.errors, t, "type");
          errorToField(setFieldError, error?.data?.errors, t, "publish");
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
      const response = await promotionService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPromotionUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        const payloadToSend: any = JSON.parse(JSON.stringify(payload || {}));
        const serializeDate = (d: any) => {
          if (!d) return null;
          if (typeof d === "string") return d;
          if (d instanceof Date) return d.toISOString().slice(0, 19).replace("T", " ");
          return String(d);
        };
        payloadToSend.startDate = serializeDate((payload as any).startDate);
        payloadToSend.endDate = serializeDate((payload as any).endDate);
        payloadToSend.rules = Array.isArray((payload as any).rules) ? (payload as any).rules : [];
        payloadToSend.actions = Array.isArray((payload as any).actions) ? (payload as any).actions : [];
        payloadToSend.targets = Array.isArray((payload as any).targets) ? (payload as any).targets : [];
        await promotionService.update(id, payloadToSend as Partial<IPromotion>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(PROMOTIONINDEX);
        }
        // keep on page
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          errorToField(setFieldError, error?.data?.errors, t, "type");
          errorToField(setFieldError, error?.data?.errors, t, "publish");
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
    await handleDelete(selectedItem.value.id as number);
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

  const initializeData = async () => {
    await fetchData();
    if (!isOptionsLoaded.value) {
      await Promise.all([loadPromotionCatalogueOptions(), loadCustomerGroupOptions()]);
      isOptionsLoaded.value = true;
    }
  };

  onMounted(async () => {
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
    promotionCatalogueOptions,
    customerGroupOptions,
    PROMOTION_TYPES,
    PROMOTION_PUBLISH_STATUS,
    PROMOTION_RULE_TYPES,
    PROMOTION_OPERATORS,
    PROMOTION_ACTION_TYPES,
    PROMOTION_APPLY_TO,
    PROMOTION_TARGET_TYPES,
    PROMOTION_LOGIC_OPERATORS,
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
    loadCustomerGroupOptions,
  };
};
