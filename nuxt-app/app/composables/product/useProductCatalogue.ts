import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { productCatalogueService } from "~/services/product/catalogue.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProductCatalogue, CataloguePayloadFilter } from "~/interfaces/product/catalogue.interface";
import { PRODUCTCATALOGUEINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";
import { productService } from "~/services/product/product.service";

export const useProductCatalogue = (initialFilters?: CataloguePayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IProductCatalogue>>>();
  const filters = ref<CataloguePayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IProductCatalogue | null>(null);
  const slug = computed(() => slugify(values.name || ""));
  const productCatalogueOptions = ref<{ name: string; value: number }[]>([]);
  const categoryDetail = ref<IProductCatalogue | null>(null);

  // state: products by category (client)
  const categoryProducts = ref<any[]>([]);
  const categoryProductsPage = ref<number>(1);
  const categoryProductsLastPage = ref<number>(1);
  const categoryProductsLoading = ref<boolean>(false);

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    description: z.string().max(255, "admin.validation.descriptionMaxLength").nullable().optional(),
    order: z.number().min(0, "admin.validation.orderMin").nullable().optional(),
    image: z
      .any()
      .optional()
      .refine((file) => !file || file.size <= MAX_FILE_SIZE, "admin.common.maxFileSize")
      .refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), "admin.common.acceptedImageTypes"),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    metaTitle: z.string().max(255, "admin.validation.metaTitleMaxLength").nullable().optional(),
    metaDescription: z.string().max(255, "admin.validation.metaDescriptionMaxLength").nullable().optional(),
    metaKeyword: z.string().max(255, "admin.validation.metaKeywordMaxLength").nullable().optional(),
    parentId: z.number().nullable().optional(),
    isHot: z.boolean().default(false).optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TProductCatalogueCreate = z.infer<typeof createSchema>;
  type TProductCatalogueUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TProductCatalogueCreate | TProductCatalogueUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = handleSubmit(async (payload: TProductCatalogueCreate | TProductCatalogueUpdate) => {
    isLoading.value = true;
    try {
      const response = await productCatalogueService.store(payload as Partial<IProductCatalogue>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      resetForm();
      return navigateTo(PRODUCTCATALOGUEINDEX);
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
      const response = await productCatalogueService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TProductCatalogueUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await productCatalogueService.update(id, payload as Partial<IProductCatalogue>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(PRODUCTCATALOGUEINDEX);
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

  const handleToggleIsHot = async (item: any) => {
    try {
      await productCatalogueService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await productCatalogueService.delete(id);
      toast.success(t("admin.messages.deleteSuccess"));
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const loadproductCatalogue = async () => {
    try {
      const response = await productCatalogueService.all();
      const rows = response?.data ?? [];
      productCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi! khi tải dữ liệu product catalogue all :", error);
    }
  };

  const setSelectedItem = (item: IProductCatalogue) => {
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
      const response = await productCatalogueService.pagination({
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

  const fetchById = async (slug: string): Promise<IProductCatalogue | null> => {
    if (!slug) return null;
    isLoading.value = true;
    try {
      const res = await productCatalogueService.show(slug);
      categoryDetail.value = (res as any)?.data ?? null;
      return categoryDetail.value;
    } catch (e: any) {
      categoryDetail.value = null;
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  // Load products by category id (append 5 each call)
  const resetCategoryProducts = () => {
    categoryProducts.value = [];
    categoryProductsPage.value = 1;
    categoryProductsLastPage.value = 1;
  };

  const loadCategoryProductsById = async (catalogueId: number, perpage: number = 5) => {
    if (!catalogueId || categoryProductsLoading.value) return;
    categoryProductsLoading.value = true;
    try {
      const res = await productService.pagination({
        perpage,
        page: categoryProductsPage.value,
        publish: 2,
        sort: "id,desc",
        product_catalogue_id: catalogueId,
      });
      const pg: any = res?.data;
      const rows = Array.isArray(pg?.data) ? pg.data : [];
      categoryProducts.value = [...categoryProducts.value, ...rows];
      categoryProductsLastPage.value = pg?.last_page ?? 1;
      categoryProductsPage.value = categoryProductsPage.value + 1;
    } finally {
      categoryProductsLoading.value = false;
    }
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
    loadproductCatalogue();
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
    categoryDetail,
    categoryProducts,
    categoryProductsPage,
    categoryProductsLastPage,
    categoryProductsLoading,
    selectedItem,
    productCatalogueOptions,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,

    handleToggleIsHot,
    setSelectedItem,
    confirmDelete,
    fetchData,
    fetchById,
    loadproductCatalogue,
    search,
    setPage,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
    resetCategoryProducts,
    loadCategoryProductsById,
  };
};

export type TProductCatalogueCreate = Partial<IProductCatalogue>;
export type TProductCatalogueUpdate = TProductCatalogueCreate;
