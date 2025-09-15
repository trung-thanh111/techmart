import z from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { useAddressOrderService } from "~/services/user/address_order.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAddressOrder, AddressOrderPayloadFilter } from "~/interfaces/user/address_order.interface";
import { ADDRESSORDERINDEX } from "~/utils/constants";
import { useUserService } from "~/services/user/user.service";

export const useAddressOrder = (
  initialFilters?: AddressOrderPayloadFilter,
  mode: "create" | "update" = "create"
) => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IAddressOrder>>>();
  const filters = ref<AddressOrderPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null, user_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IAddressOrder | null>(null);
  const userOptions = ref<{ name: string; value: number }[]>([]);
  const slug = computed(() => slugify(values.title || ""));

  const schema = {
    title: z.string({ required_error: 'admin.validation.titleRequired' }).max(255, 'admin.validation.titleMaxLength').trim().min(1, 'admin.validation.titleRequired').max(255, 'admin.validation.titleMaxLength'),
    fullName: z.string({ required_error: 'admin.validation.fullNameRequired' }).trim().min(1, 'admin.validation.fullNameRequired').max(255, 'admin.validation.fullNameMaxLength'),
    phone: z.string({ required_error: 'admin.validation.phoneRequired' }).trim().min(1, 'admin.validation.phoneRequired').max(20, 'admin.validation.phoneMaxLength'),
    email: z.string().email('admin.validation.emailInvalid').max(255, 'admin.validation.emailMaxLength').nullable().optional(),
    address: z.string({ required_error: 'admin.validation.addressRequired' }).trim().min(1, 'admin.validation.addressRequired').max(255, 'admin.validation.addressMaxLength'),
    state: z.string().max(100, 'admin.validation.stateMaxLength').nullable().optional(),
    postalCode: z.string({ required_error: 'admin.validation.postalCodeRequired' }).trim().min(1, 'admin.validation.postalCodeRequired').max(20, 'admin.validation.postalCodeMaxLength'),
    countryCode: z.string().max(2, 'admin.validation.countryCodeMaxLength').nullable().optional(),
    isDefault: z.boolean().optional(),
    publish: z.number({ required_error: 'admin.validation.statusRequired' }).refine((val) => [0, 1, 2].includes(val), { message: 'admin.validation.statusInvalid' }),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TAddressOrderCreate = z.infer<typeof createSchema>;
  type TAddressOrderUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm } = useForm<TAddressOrderCreate | TAddressOrderUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  const handleStore = handleSubmit(async (payload: TAddressOrderCreate) => {
    isLoading.value = true;
    try {
      const response = await useAddressOrderService.store(payload as Partial<IAddressOrder>);
      toast.success(response?.message || t('admin.messages.createSuccess'));
      resetForm();
      return navigateTo(ADDRESSORDERINDEX);
    } catch (error: any) {
      console.error("Thêm mới thất bại:", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    } finally {
      isLoading.value = false;
    }
  });

  const show = async (id: number) => {
    try {
      const response = await useAddressOrderService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TAddressOrderUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await useAddressOrderService.update(id, payload as Partial<IAddressOrder>);
      toast.success(t('admin.messages.updateSuccess'));
      return navigateTo(ADDRESSORDERINDEX);
    } catch (error: any) {
      console.error("Cập nhật thất bại:", error);
      toast.error(error?.response?.data?.message || t('admin.messages.updateFailed'));
    } finally {
      isLoading.value = false;
    }
  });

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await useAddressOrderService.delete(id);
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
      await useAddressOrderService.deleteMultiple(selectedIds.value);
      toast.success(t('admin.messages.deleteMultipleSuccess', { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.deleteFailed'));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IAddressOrder) => {
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
      const response = await useAddressOrderService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t('admin.messages.loadDataError'));
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const loadUser = async () => {
    try {
      const response = await useUserService.all();
      const rows = response?.data ?? [];
      userOptions.value = (Array.isArray(rows) ? rows : [rows])
        .map((item: any) => ({ name: item.name, value: Number(item.id) }))
        .filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi! khi tải dữ liệu user all :", error);
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
    fetchData()
    loadUser()
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
    userOptions,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    setSelectedItem,
    confirmDelete,
    fetchData,
    loadUser,
    search,
    setPage,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
  };
};

export type TAddressOrderCreate = Partial<IAddressOrder>;
export type TAddressOrderUpdate = TAddressOrderCreate;

