import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify, buildRelationParams } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { productService } from "~/services/product/product.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProduct, ProductPayloadFilter } from "~/interfaces/product/product.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { PRODUCTINDEX } from "~/utils/constants";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { productTagService } from "~/services/product/tag.service";
import { brandService } from "~/services/brand/brand.service";
import { faqService } from "~/services/faq/faq.service";

export const useProduct = (initialFilters?: ProductPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IProduct>>>();
  const filters = ref<ProductPayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null, product_catalogue_id: null, tag_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IProduct | null>(null);
  // Options with caching
  const faqOptions = ref<Array<{ name: string; id: number }>>([]);
  const tagOptions = ref<Array<{ name: string; id: number }>>([]);
  const brandOptions = ref<Array<{ name: string; id: number }>>([]);
  const productOptions = ref<Array<{ name: string; id: number }>>([]);
  const productCatalogueOptions = ref<Array<{ name: string; id: number }>>([]);
  // Cache flags to avoid reloading using useState
  const isOptionsLoaded = useState("product-options-loaded", () => false);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    sku: z.string({ required_error: "admin.validation.skuRequired" }).trim().min(1, "admin.validation.skuRequired").max(100, "admin.validation.skuMaxLength"),
    description: z.string().nullable().optional(),
    content: z.string().nullable().optional(),
    priceOrigin: z.number({ required_error: "admin.validation.priceOriginRequired" }).nonnegative({ message: "admin.validation.minValue" }),
    album: z
      .array(z.object({ path: z.string() }))
      .nullable()
      .optional(),
    season: z.enum(["spring", "summer", "autumn", "winter", "none"]).nullable().optional(),
    warranty: z.number().nullable().optional(),
    sold: z.number().nullable().optional(),
    sortOrder: z.number().int().nonnegative().nullable().optional(),
    productCatalogues: z.array(z.number(), { required_error: "admin.validation.productCatalogueRequired" }).min(1, "admin.validation.productCatalogueRequired"),
    brandId: z.number({ required_error: "admin.validation.brandRequired", invalid_type_error: "admin.validation.brandRequired" }).int().positive("admin.validation.brandRequired"),
    productTags: z.array(z.number()).nullable().optional(),
    productFaqs: z.array(z.number()).nullable().optional(),
    relateds: z.array(z.number()).nullable().optional(),
    isHot: z.boolean().nullable().optional(),
    likeCount: z.number().int().nonnegative().nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    metaTitle: z.string().max(255, "admin.validation.metaTitleMaxLength").nullable().optional(),
    metaKeyword: z.string().max(255, "admin.validation.metaKeywordMaxLength").nullable().optional(),
    metaDescription: z.string().max(255, "admin.validation.metaDescriptionMaxLength").nullable().optional(),
    productVariants: z
      .array(
        z.object({
          code: z.string().nullable().optional(),
          url: z.string().nullable().optional(),
          name: z.string(),
          stock: z.number().nullable().optional(),
          sku: z.string(),
          price: z.number().nullable().optional(),
          barcode: z.string().nullable().optional(),
          publish: z.number().nullable().optional(),
          attributeData: z.record(z.any()).nullable().optional(),
          attributes: z.array(z.number()).optional(),
        })
      )
      .nullable()
      .optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TProductCreate = z.infer<typeof createSchema>;
  type TProductUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TProductCreate | TProductUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
    initialValues: {
      productVariants: [],
    },
  });

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TProductCreate | TProductUpdate) => {
      isLoading.value = true;
      try {
        console.info("payload cretea:", payload);
        const response = await productService.store(payload as Partial<IProduct>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(PRODUCTINDEX);
        }
        resetForm();
      } catch (error: any) {
        if (error?.data?.isValidationError) {
          errorToField(setFieldError, error?.data?.errors, t, "name");
          errorToField(setFieldError, error?.data?.errors, t, "sku");
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
      const response = await productService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TProductUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);

        // Luôn gửi productVariants để xử lý cả trường hợp clear
        const cleanPayload = { ...payload };

        await productService.update(id, cleanPayload as Partial<IProduct>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(PRODUCTINDEX);
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

  const handleToggleIsHot = async (item: any) => {
    try {
      await productService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await productService.delete(id);
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
      await productService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IProduct) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value || !selectedItem.value.id) return;
    await handleDelete(selectedItem.value.id);
    selectedItem.value = null;
  };

  // fetch
  const fetchData = async () => {
    isLoading.value = true;
    try {
      const { product_catalogue_id, tag_id, brand_id, ...rest } = (filters.value as any) || {};
      const response = await productService.pagination({
        ...rest,
        ...buildRelationParams({
          product_catalogues: product_catalogue_id,
          product_tags: tag_id,
          brands: brand_id,
        }),
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

  const loadProductCatalogue = async () => {
    if (productCatalogueOptions.value.length > 0) return; // Cache hit
    try {
      const response = await productCatalogueService.all();
      const rows = response?.data ?? [];
      productCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu product catalogue:", error);
    }
  };

  const loadTag = async () => {
    if (tagOptions.value.length > 0) return; // Cache hit
    try {
      const response = await productTagService.all();
      const rows = response?.data ?? [];
      tagOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu tag:", error);
    }
  };

  const loadBrand = async () => {
    if (brandOptions.value.length > 0) return; // Cache hit
    try {
      const response = await brandService.all();
      const rows = response?.data ?? [];
      brandOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu brand:", error);
    }
  };

  const loadProduct = async () => {
    if (productOptions.value.length > 0) return; // Cache hit
    try {
      const response = await productService.all();
      const rows = response?.data ?? [];
      productOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu product:", error);
    }
  };

  const loadFaqOptions = async () => {
    if (faqOptions.value.length > 0) return; // Cache hit
    try {
      const response = await faqService.pagination({
        type: "all",
        publish: 2,
      });
      // Response có thể là IPaginate hoặc array trực tiếp
      let rows: any[] = [];
      if (response?.data) {
        if (Array.isArray(response.data)) {
          rows = response.data;
        } else if (response.data?.data && Array.isArray(response.data.data)) {
          rows = response.data.data;
        } else if (typeof response.data === "object") {
          rows = [response.data];
        }
      }
      faqOptions.value = rows
        .map((item: any) => ({
          id: Number(item.id),
          name: item.question || item.name || "",
        }))
        .filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu faqs:", error);
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

  const seasonOptions = [
    { name: t("admin.common.form.none"), value: "none" },
    { name: t("admin.common.form.spring"), value: "spring" },
    { name: t("admin.common.form.summer"), value: "summer" },
    { name: t("admin.common.form.autumn"), value: "autumn" },
    { name: t("admin.common.form.winter"), value: "winter" },
  ];

  const initializeData = async () => {
    await fetchData();
    if (!isOptionsLoaded.value) {
      await Promise.all([loadProductCatalogue(), loadTag(), loadBrand(), loadProduct(), loadFaqOptions()]);
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
    productCatalogueOptions,
    tagOptions,
    brandOptions,
    productOptions,
    faqOptions,
    seasonOptions,
    slug,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    handleToggleIsHot,
    loadProductCatalogue,
    loadTag,
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

export type TProductCreate = Partial<IProduct>;
export type TProductUpdate = TProductCreate;
