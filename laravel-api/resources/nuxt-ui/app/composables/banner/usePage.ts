import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { pageService } from "~/services/banner/page.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPage, PagePayloadFilter } from "~/interfaces/banner/page.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { BANNERPAGEINDEX } from "~/utils/constants";

export const usePage = (initialFilters?: PagePayloadFilter, mode: "create" | "update" = "create") => {
    const { t } = useI18n();
    const config = useRuntimeConfig();
    const toast = useAppToast();
    const data = ref<IApiOk<IPaginate<IPage>>>();
    const filters = ref<PagePayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null });
    const page = ref(1);
    const isLoading = ref(false);
    const selectedIds = ref<number[]>([]);
    const selectedItem = ref<IPage | null>(null);
    const slug = computed(() => slugify(values.name || ""));

    const schema = {
        name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
        publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    };

    const createSchema = z.object({ ...schema });
    const updateSchema = z.object({ ...schema });
    type TPageCreate = z.infer<typeof createSchema>;
    type TPageUpdate = z.infer<typeof updateSchema>;

    const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPageCreate | TPageUpdate>({
        validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
        keepValuesOnUnmount: true,
    });

    const handleStore = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TPageCreate | TPageUpdate) => {
            isLoading.value = true;
            try {
                const response = await pageService.store(payload as Partial<IPage>);
                toast.success(response?.message || t("admin.messages.createSuccess"));
                if (shouldClose) {
                    return navigateTo(BANNERPAGEINDEX);
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
            const response = await pageService.show(id);
            return response;
        } catch (error: any) {
            console.error("Lỗi! Không tìm thấy bản ghi: ", error);
            toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
            throw error;
        }
    };

    const handleUpdate = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TPageUpdate) => {
            isLoading.value = true;
            try {
                const id = Number(useRoute().params.id);
                await pageService.update(id, payload as Partial<IPage>);
                toast.success(t("admin.messages.updateSuccess"));
                if (shouldClose) {
                    return navigateTo(BANNERPAGEINDEX);
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

    const handleDelete = async (id: number) => {
        isLoading.value = true;
        try {
            await pageService.delete(id);
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
            await pageService.deleteMultiple(selectedIds.value);
            toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
            selectedIds.value = [];
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
        } finally {
            isLoading.value = false;
        }
    };

    const setSelectedItem = (item: IPage) => {
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
            const response = await pageService.pagination({
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

export type TPageCreate = Partial<IPage>;
export type TPageUpdate = TPageCreate;
