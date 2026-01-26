import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify, buildRelationParams } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { postService } from "~/services/post/post.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPost, PostPayloadFilter } from "~/interfaces/post/post.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { POSTINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";
import { postCatalogueService } from "~/services/post/catalogue.service";
import { postTagService } from "~/services/post/tag.service";

export const usePost = (initialFilters?: PostPayloadFilter, mode: "create" | "update" = "create") => {
  const { t } = useI18n();
  const config = useRuntimeConfig();
  const toast = useAppToast();
  const data = ref<IApiOk<IPaginate<IPost>>>();
  const filters = ref<PostPayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null, post_catalogue_id: null, tag_id: null });
  const page = ref(1);
  const isLoading = ref(false);
  const selectedIds = ref<number[]>([]);
  const selectedItem = ref<IPost | null>(null);
  const tagOptions = ref<Array<{ name: string; id: number }>>([]);
  const postOptions = ref<Array<{ name: string; id: number }>>([]);
  const postCatalogueOptions = ref<Array<{ name: string; id: number }>>([]);

  // Cache flags for optimization using useState
  const isDataLoaded = useState("post-data-loaded", () => false);
  const isOptionsLoaded = useState("post-options-loaded", () => false);

  const slug = computed(() => slugify(values.name || ""));

  const schema = {
    name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
    description: z.string().max(500, "admin.validation.descriptionMaxLength").nullable().optional(),
    content: z.string({ required_error: "admin.validation.contentRequired" }).trim().min(1, "admin.validation.contentRequired"),
    image: z
      .any()
      .optional()
      .refine((file) => !file || file.size <= MAX_FILE_SIZE, "admin.common.maxFileSize")
      .refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), "admin.common.acceptedImageTypes"),
    tableOfContent: z.boolean().default(false).optional(),
    order: z.number().int().nonnegative().default(0),
    isHot: z.boolean().default(false).optional(),
    viewCount: z.number().int().nonnegative().default(0),
    postCatalogues: z.array(z.number()).min(1, "admin.validation.postCatalogueRequired"),
    postTags: z.array(z.number()).nullable().optional(),
    relateds: z.array(z.number()).nullable().optional(),
    publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    metaTitle: z.string().max(255, "admin.validation.metaTitleMaxLength").nullable().optional(),
    metaKeyword: z.string().max(255, "admin.validation.metaKeywordMaxLength").nullable().optional(),
    metaDescription: z.string().max(255, "admin.validation.metaDescriptionMaxLength").nullable().optional(),
    publishedAt: z.any().nullable().optional(),
  };
  const createSchema = z.object({ ...schema });
  const updateSchema = z.object({ ...schema });
  type TPostCreate = z.infer<typeof createSchema>;
  type TPostUpdate = z.infer<typeof updateSchema>;

  const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPostCreate | TPostUpdate>({
    validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    keepValuesOnUnmount: true,
  });

  const handleStore = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPostCreate | TPostUpdate) => {
      isLoading.value = true;
      try {
        const response = await postService.store(payload as Partial<IPost>);
        toast.success(response?.message || t("admin.messages.createSuccess"));
        if (shouldClose) {
          return navigateTo(POSTINDEX);
        }
        resetForm();
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

    return wrapped();
  };

  const show = async (id: number) => {
    try {
      const response = await postService.show(id);
      return response;
    } catch (error: any) {
      console.error("Lỗi! Không tìm thấy bản ghi: ", error);
      toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
      throw error;
    }
  };

  const handleUpdate = (shouldClose = false) => {
    const wrapped = handleSubmit(async (payload: TPostUpdate) => {
      isLoading.value = true;
      try {
        const id = Number(useRoute().params.id);
        await postService.update(id, payload as Partial<IPost>);
        toast.success(t("admin.messages.updateSuccess"));
        if (shouldClose) {
          return navigateTo(POSTINDEX);
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
      await postService.update(item.id, { isHot: item.isHot });
      toast.success(t("admin.messages.updateSuccess"));
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
      item.isHot = !item.isHot;
    }
  };

  const handleDelete = async (id: number) => {
    isLoading.value = true;
    try {
      await postService.delete(id);
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
      await postService.deleteMultiple(selectedIds.value);
      toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
      selectedIds.value = [];
      await fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
    } finally {
      isLoading.value = false;
    }
  };

  const setSelectedItem = (item: IPost) => {
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
      const { post_catalogue_id, tag_id, ...rest } = (filters.value as any) || {};
      const response = await postService.pagination({
        ...rest,
        ...buildRelationParams({
          post_catalogues: post_catalogue_id,
          post_tags: tag_id,
        }),
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

  const loadPostCatalogue = async () => {
    if (postCatalogueOptions.value.length > 0) return; // Cache hit
    try {
      const response = await postCatalogueService.all();
      const rows = response?.data ?? [];
      postCatalogueOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu post catalogue:", error);
    }
  };

  const loadTag = async () => {
    if (tagOptions.value.length > 0) return; // Cache hit
    try {
      const response = await postTagService.all();
      const rows = response?.data ?? [];
      tagOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu tag:", error);
    }
  };

  const loadPost = async () => {
    if (postOptions.value.length > 0) return; // Cache hit
    try {
      const response = await postService.all();
      const rows = response?.data ?? [];
      postOptions.value = (Array.isArray(rows) ? rows : [rows]).map((item: any) => ({ id: Number(item.id), name: item.name })).filter((opt: any) => opt.name && !Number.isNaN(opt.id));
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu post:", error);
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

  // Optimized initialization with Promise.all
  const initializeData = async () => {
    // Load main data first
    await fetchData();

    // Load options in parallel
    if (!isOptionsLoaded.value) {
      await Promise.all([loadPostCatalogue(), loadTag(), loadPost()]);
      isOptionsLoaded.value = true;
    }
  };

  onMounted(() => {
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
    postCatalogueOptions,
    tagOptions,
    postOptions,
    slug,
    setValues,
    show,
    handleStore,
    handleUpdate,
    handleDelete,
    handleDeleteMultiple,
    handleToggleIsHot,
    loadPostCatalogue,
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

export type TPostCreate = Partial<IPost>;
export type TPostUpdate = TPostCreate;
