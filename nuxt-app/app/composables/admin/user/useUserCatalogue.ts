// composables/useUserCatalogue.ts
import z from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { userCatalogueService } from "~/services/user/catalogue.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IUserCatalogue, PayloadFilter } from "~/interfaces/user/catalogue.interface";
import { USERCATALOGUEINDEX } from "~/utils/constants";

export const useUserCatalogue = (
  initialFilters?: PayloadFilter,
  mode: "create" | "update" = "create"
) => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();

  const data = ref<IApiOk<IPaginate<IUserCatalogue>>>();
  const filters = ref<PayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IUserCatalogue | null>(null);
  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: 'admin.validation.nameRequired'}).trim().min(1, 'admin.validation.nameRequired').max(255, 'admin.validation.nameMaxLength'),
    description: z.string().nullable().optional(),
    publish: z.number({ required_error: 'admin.validation.statusRequired'}).refine((val) => [0, 1, 2].includes(val), { message: 'admin.validation.statusInvalid'}),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TUserCatalogueCreate = z.infer<typeof createSchema>;
  type TUserCatalogueUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm } = useForm<any>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  // store
  const handleStore = handleSubmit(async (payload: TUserCatalogueCreate) => {
    isLoading.value = true;
    try {
      const response = await userCatalogueService.store(payload as Partial<IUserCatalogue>);
      toast.success(response?.message || t('admin.messages.createSuccess'));
      resetForm();
      return navigateTo(USERCATALOGUEINDEX);
    } catch (error: any) {
      console.error("Store error:", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    } finally {
      isLoading.value = false;
    }
  });

  const show = async (id: number) => {
    try {
      const response = await userCatalogueService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t('admin.messages.createFailed'));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TUserCatalogueUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await userCatalogueService.update(id, payload as Partial<IUserCatalogue>);
      toast.success(t('admin.messages.updateSuccess'));
      return navigateTo(USERCATALOGUEINDEX);

    } catch (error: any) {
      console.error("Store error:", error);
      toast.error(error?.response?.data?.message || t('admin.messages.updateFailed'));
    } finally {
      isLoading.value = false;
    }
  });

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await userCatalogueService.delete(id);
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
      await userCatalogueService.deleteMultiple(selectedIds.value);
      toast.success(t('admin.messages.deleteMultipleSuccess', { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t('admin.messages.deleteFailed'));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IUserCatalogue) => {
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
      const response = await userCatalogueService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t('admin.messages.loadDataError'));
      console.error("Fetch error:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const search = async (newFilters: PayloadFilter) => {
    filters.value = newFilters;
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

  onMounted(fetchData);

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

export type TUserCatalogueCreate = Partial<IUserCatalogue>;
export type TUserCatalogueUpdate = TUserCatalogueCreate;
