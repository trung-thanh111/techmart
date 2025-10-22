import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { bannerService } from "~/services/banner/banner.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IBanner, BannerPayloadFilter } from "~/interfaces/banner/banner.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { BANNERINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";

export const useBanner = (initialFilters?: BannerPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IBanner>>>();
  const filters = ref<BannerPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IBanner | null>(null);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    album: z.array(z.object({ path: z.string() })).nullable().optional(),
    type: z.enum(["homepage", "category", "flash_sale", "popup", "custom", "shop"], { errorMap: () => ({ message: "admin.validation.typeInvalid" }) }),
    position: z.enum(["main_slider", "homepage_top_ads", "homepage_middle_ads", "homepage_bottom_ads", "category_top_banner", "category_sidebar_banner", "category_bottom_banner", "flash_sale_header", "flash_sale_footer", "popup_center", "popup_bottom_right", "custom_html", "custom_slot"], { errorMap: () => ({ message: "admin.validation.positionInvalid" }) }),
    startAt: z.string().nullable().optional(),
    endAt: z.string().nullable().optional(),
    order: z.number().nullable().optional(),
    linkUrl: z.string().nullable().optional(),
    isHot: z.boolean().nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };

  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TBannerCreate = z.infer<typeof createSchema>;
  type TBannerUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, setFieldError } = useForm<TBannerCreate | TBannerUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = handleSubmit(async (payload: TBannerCreate | TBannerUpdate) => {
    isLoading.value = true;
    try {
      const response = await bannerService.store(payload as Partial<IBanner>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      return navigateTo(BANNERINDEX);
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

  const show = async (id: number) => {
    try {
      const response = await bannerService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TBannerUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await bannerService.update(id, payload as Partial<IBanner>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(BANNERINDEX);
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

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await bannerService.delete(id);
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
      await bannerService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IBanner) => {
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
      const response = await bannerService.pagination({
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

  const handleToggleIsHot = async (item: any) => {
    try {
      await bannerService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
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

  const typeOptions = computed(() => [
    { name: t("admin.common.homepage"), value: "homepage" },
    { name: t("admin.common.category"), value: "category" },
    { name: t("admin.common.flash_sale"), value: "flash_sale" },
    { name: t("admin.common.popup"), value: "popup" },
    { name: t("admin.common.custom"), value: "custom" },
    { name: t("admin.common.shop"), value: "shop" },
  ]);

  const positionOptions = computed(() => [
    { name: t("admin.common.main_slider"), value: "main_slider" },
    { name: t("admin.common.homepage_top_ads"), value: "homepage_top_ads" },
    { name: t("admin.common.homepage_middle_ads"), value: "homepage_middle_ads" },
    { name: t("admin.common.homepage_bottom_ads"), value: "homepage_bottom_ads" },
    { name: t("admin.common.category_top_banner"), value: "category_top_banner" },
    { name: t("admin.common.category_sidebar_banner"), value: "category_sidebar_banner" },
    { name: t("admin.common.category_bottom_banner"), value: "category_bottom_banner" },
    { name: t("admin.common.flash_sale_header"), value: "flash_sale_header" },
    { name: t("admin.common.flash_sale_footer"), value: "flash_sale_footer" },
    { name: t("admin.common.popup_center"), value: "popup_center" },
    { name: t("admin.common.popup_bottom_right"), value: "popup_bottom_right" },
    { name: t("admin.common.custom_html"), value: "custom_html" },
    { name: t("admin.common.custom_slot"), value: "custom_slot" },
  ]);

  onMounted(() => {
    fetchData();
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
    slug,
    typeOptions,
    positionOptions,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    setSelectedItem,
    confirmDelete,
    handleToggleIsHot,
    fetchData,
    search,
    setPage,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
  };
};

export type TBannerCreate = Partial<IBanner>;
export type TBannerUpdate = TBannerCreate;
