import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { useUserService } from "~/services/user/user.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IUser, UserPayloadFilter } from "~/interfaces/user/user.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { USERINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";
import { userCatalogueService } from "~/services/user/catalogue.service";
import { useAuthStore } from "~/stores/auth/auth";

export const useUser = (initialFilters?: UserPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IUser>>>();
  const filters = ref<UserPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null, user_catalogue_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IUser | null>(null);
  const userCatalogueOptions = ref<{ name: string; value: number }[]>([]);
  const showPassword = ref(false);

  // Cache flags for optimization using useState
  const isDataLoaded = useState("user-data-loaded", () => false);
  const isOptionsLoaded = useState("user-options-loaded", () => false);

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    email: z.string({ required_error: "admin.validation.emailRequired" }).email("admin.validation.emailInvalid").max(255, "admin.validation.emailMaxLength"),
    lastName: z.string().max(255, "admin.validation.lastNameMaxLength").nullable().optional(),
    firstName: z.string().max(255, "admin.validation.firstNameMaxLength").nullable().optional(),
    description: z.string().max(255, "admin.validation.descriptionMaxLength").nullable().optional(),
    birthday: z.string().nullable().optional(),
    gender: z
      .string()
      .refine((val) => !val || ["male", "female", "other"].includes(val), { message: "admin.validation.genderInvalid" })
      .nullable()
      .optional(),
    phone: z.string().max(255, "admin.validation.phoneMaxLength").nullable().optional(),
    address: z.string().max(255, "admin.validation.addressMaxLength").nullable().optional(),
    password: z.string().min(8, "admin.validation.passwordMinLength").optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    userCatalogueId: z.number({ required_error: "admin.validation.userCatalogueRequired" }).optional(),
    image: z
      .any()
      .optional()
      .refine((file) => !file || file.size <= MAX_FILE_SIZE, "admin.common.maxFileSize")
      .refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), "admin.common.acceptedImageTypes"),
  };
  const createSchema = z.object({
    ...schema,
    password: z.string({ required_error: "admin.validation.passwordRequired" }).min(8, "admin.validation.passwordMinLength"),
    userCatalogueId: z.number({ required_error: "admin.validation.userCatalogueRequired" }),
  });
  const updateSchema = z.object({
    ...schema,
    password: z.string().min(8, "admin.validation.passwordMinLength").optional(),
    userCatalogueId: z.number({ required_error: "admin.validation.userCatalogueRequired" }).optional(),
  });
  type TUserCreate = z.infer<typeof createSchema>;
  type TUserUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm } = useForm<TUserCreate | TUserUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
  });

  const handleStore = handleSubmit(async (payload: TUserCreate | TUserUpdate) => {
    isLoading.value = true;
    try {
      const response = await useUserService.store(payload as Partial<IUser>);
      toast.success(response?.message || t("admin.messages.createSuccess"));
      resetForm();
      return navigateTo(USERINDEX);
    } catch (error: any) {
      console.error("Thêm mới thất bại:", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    } finally {
      isLoading.value = false;
    }
  });

  const show = async (id: number) => {
    try {
      const response = await useUserService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = handleSubmit(async (payload: TUserUpdate) => {
    isLoading.value = true;
    try {
      const id = Number(useRoute().params.id);
      await useUserService.update(id, payload as Partial<IUser>);
      toast.success(t("admin.messages.updateSuccess"));
      return navigateTo(USERINDEX);
    } catch (error: any) {
      console.error("Cập nhật thất bại:", error);
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
    } finally {
      isLoading.value = false;
    }
  });

  const handleUpdateProfile = handleSubmit(async (payload: TUserUpdate) => {
    isLoading.value = true;
    try {
      const authStore = useAuthStore();
      const id = authStore.user?.id;
      if (!id) return false;
      const response = await useUserService.update(id, payload as Partial<IUser>);
      if (response?.data) {
        authStore.user = { ...authStore.user, ...response.data };
      }

      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      console.error("Cập nhật thất bại:", error);
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
    } finally {
      isLoading.value = false;
    }
  });

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await useUserService.delete(id);
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
      await useUserService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IUser) => {
    selectedItem.value = item;
  };

  const confirmDelete = async () => {
    if (!selectedItem.value) return;
    await handleDelete(selectedItem.value.id);
    selectedItem.value = null;
  };

  // fetch
  const fetchData = async () => {
    if (isDataLoaded.value && page.value === 1) return; // Cache hit for first page
    isLoading.value = true;
    try {
      const response = await useUserService.pagination({
        ...filters.value,
        page: page.value,
      });
      data.value = response;
      if (page.value === 1) isDataLoaded.value = true;
    } catch (err: any) {
      toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const loadUserCatalogue = async () => {
    if (userCatalogueOptions.value.length > 0) return; // Cache hit
    try {
      const response = await userCatalogueService.all();
      const rows = response?.data ?? [];
      userCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ name: item.name, value: Number(item.id) })).filter((opt: any) => opt.name && !Number.isNaN(opt.value));
    } catch (error) {
      console.error("Lỗi! khi tải dữ liệu user catalogue all :", error);
    }
  };

  const search = async (newFilters: BasePayloadFilter) => {
    filters.value = { ...filters.value, ...newFilters };
    page.value = 1;
    selectedIds.value = [];
    isDataLoaded.value = false; // Reset cache for new search
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

  const genderOptions = computed(() => [
    { name: t("admin.common.form.genderMale"), value: "male" },
    { name: t("admin.common.form.genderFemale"), value: "female" },
    { name: t("admin.common.form.genderOther"), value: "other" },
  ]);

  // Optimized initialization with Promise.all
  const initializeData = async () => {
    // Load main data first
    await fetchData();

    // Load options in parallel
    if (!isOptionsLoaded.value) {
      await loadUserCatalogue();
      isOptionsLoaded.value = true;
    }
  };

  onMounted(() => {
    initializeData();
  });

  return {
    data,
    isLoading,
    showPassword,
    filters,
    page,
    values,
    errors,
    selectedIds,
    selectedItem,
    genderOptions,
    userCatalogueOptions,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleUpdateProfile,
    handleDelete,
    handleDeleteMultiple,
    loadUserCatalogue,
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

export type TUserCreate = Partial<IUser>;
export type TUserUpdate = TUserCreate;
