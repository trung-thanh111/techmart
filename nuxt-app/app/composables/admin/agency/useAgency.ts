import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { agencyService } from "~/services/agency/agency.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAgency, AgencyPayloadFilter } from "~/interfaces/agency/agency.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { AGENCYINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";
import { agencyWorkingHourService } from "~/services/agency/working_hour.service";

export const useAgency = (initialFilters?: AgencyPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IAgency>>>();
  const filters = ref<AgencyPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IAgency | null>(null);
  const agencyWorkingHourOptions = ref<Array<{ name: string; id: number }>>([]);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    email: z.string({ required_error: 'admin.validation.emailRequired' }).email('admin.validation.emailInvalid').max(255, 'admin.validation.emailMaxLength'),
    phone: z.string().max(255, 'admin.validation.phoneMaxLength').nullable().optional(),
    address: z.string().max(255, 'admin.validation.addressMaxLength').nullable().optional(),
    website: z.string().max(500, "admin.validation.websiteMaxLength").nullable().optional(),
    description: z.string().max(500, "admin.validation.descriptionMaxLength").nullable().optional(),
    iframeMap: z.string().nullable().optional(),
    logo: z.any().optional().refine((file) => !file || file.size <= MAX_FILE_SIZE, 'admin.common.maxFileSize').refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), 'admin.common.acceptedImageTypes'),
    agencyWorkingHours: z.array(z.number()).min(1, "admin.validation.agencyWorkingHourRequired"),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TAgencyCreate = z.infer<typeof createSchema>;
  type TAgencyUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, setFieldError } = useForm<TAgencyCreate | TAgencyUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = handleSubmit(async (payload: TAgencyCreate | TAgencyUpdate) => {
    isLoading.value = true;
    try {
      const response = await agencyService.store(payload as Partial<IAgency>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      return navigateTo(AGENCYINDEX);
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
      const response = await agencyService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TAgencyUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await agencyService.update(id, payload as Partial<IAgency>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(AGENCYINDEX);
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
      await agencyService.delete(id);
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
      await agencyService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IAgency) => {
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
      const response = await agencyService.pagination({
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

  const loadAgencyWorkingHour = async () => {
    try {
      const response = await agencyWorkingHourService.all();
      const rows = response?.data ?? [];
      agencyWorkingHourOptions.value = (Array.isArray(rows) ? rows : [rows])
        .map((item: any) => ({ id: Number(item.id), name: item.dayOfWeek }))
        .filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu agency working_hour:", error);
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
    loadAgencyWorkingHour();
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
    agencyWorkingHourOptions,
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

export type TAgencyCreate = Partial<IAgency>;
export type TAgencyUpdate = TAgencyCreate;
