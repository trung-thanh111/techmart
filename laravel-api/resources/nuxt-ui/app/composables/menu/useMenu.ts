import z from "zod";
import { useI18n } from "vue-i18n";
import { useForm } from "vee-validate";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { menuService } from "~/services/menu/menu.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IMenu, MenuPayloadFilter } from "~/interfaces/menu/menu.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";
import { MENUINDEX } from "~/utils/constants";

export const useMenu = (initialFilters?: MenuPayloadFilter, mode: "create" | "update" = "create") => {
    const { t } = useI18n();
    const config = useRuntimeConfig();
    const toast = useAppToast();
    const data = ref<IApiOk<IPaginate<IMenu>>>();
    const filters = ref<MenuPayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: null });
    const page = ref(1);
    const isLoading = ref(false);
    const selectedIds = ref<number[]>([]);
    const selectedItem = ref<IMenu | null>(null);
    const slug = computed(() => slugify(values.name || ""));

    const schema = {
        name: z.string({ required_error: "admin.validation.nameRequired" }).trim().min(1, "admin.validation.nameRequired").max(255, "admin.validation.nameMaxLength"),
        slug: z.string().nullable().optional(),
        position: z.enum(["header", "footer", "sidebar", "profile", "mobile"], {
            required_error: "admin.validation.positionRequired",
        }),
        publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), {
            message: "admin.validation.statusInvalid",
        }),
        menuItems: z.any().optional(),
    };

    const createSchema = z.object({ ...schema });
    const updateSchema = z.object({ ...schema });
    type TMenuCreate = z.infer<typeof createSchema>;
    type TMenuUpdate = z.infer<typeof updateSchema>;

    const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TMenuCreate | TMenuUpdate>({
        validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
        keepValuesOnUnmount: true,
        initialValues: {
            menuItems: [],
        },
    });

    const handleStore = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TMenuCreate | TMenuUpdate) => {
            isLoading.value = true;
            try {
                const finalPayload = {
                    name: payload.name,
                    slug: payload.slug || null,
                    position: payload.position,
                    publish: payload.publish,
                    menu_items: payload.menuItems || [],
                };

                const response = await menuService.store(finalPayload as Partial<IMenu>);
                toast.success(response?.message || t("admin.messages.createSuccess"));
                if (shouldClose) {
                    return navigateTo(MENUINDEX);
                }
                resetForm();
            } catch (error: any) {
                if (error?.data?.isValidationError) {
                    errorToField(setFieldError, error?.data?.errors, t, "name");
                    errorToField(setFieldError, error?.data?.errors, t, "slug");
                    return;
                }

                console.error(t("admin.messages.createFailed") + ":", error);
                toast.error(error?.response?.data?.message || t("admin.messages.createFailed"));
                throw error;
            } finally {
                isLoading.value = false;
            }
        });

        return wrapped();
    };

    const handleUpdate = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TMenuUpdate) => {
            isLoading.value = true;

            try {
                const id = Number(useRoute().params.id);
                const finalPayload = {
                    name: payload.name,
                    slug: payload.slug || null,
                    position: payload.position,
                    publish: payload.publish,
                    menu_items: payload.menuItems || [],
                };
                await menuService.update(id, finalPayload as Partial<IMenu>);
                toast.success(t("admin.messages.updateSuccess"));
                if (shouldClose) {
                    return navigateTo(MENUINDEX);
                }
            } catch (error: any) {
                if (error?.data?.isValidationError) {
                    errorToField(setFieldError, error?.data?.errors, t, "name");
                    errorToField(setFieldError, error?.data?.errors, t, "slug");
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

    const show = async (id: number) => {
        try {
            const response = await menuService.show(id);
            return response;
        } catch (error: any) {
            console.error("Lỗi! Không tìm thấy bản ghi:", error);
            toast.error(error?.response?.data?.message || t("admin.messages.loadDataError"));
            throw error;
        }
    };

    const handleTogglePublish = async (item: any) => {
        try {
            await menuService.update(item.id, { publish: item.publish });
            toast.success(t("admin.messages.updateSuccess"));
        } catch (error: any) {
            toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
            item.publish = !item.publish;
        }
    };

    const handleDelete = async (id: number) => {
        isLoading.value = true;
        try {
            await menuService.delete(id);
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
            await menuService.deleteMultiple(selectedIds.value);
            toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
            selectedIds.value = [];
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
        } finally {
            isLoading.value = false;
        }
    };

    const setSelectedItem = (item: IMenu) => {
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
            const { ...rest } = (filters.value as any) || {};
            const response = await menuService.pagination({
                ...rest,
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

    const initializeData = async () => {
        await fetchData();
    };

    const positionOptions = [
        { label: t("admin.common.form.header"), value: "header" },
        { label: t("admin.common.form.footer"), value: "footer" },
        { label: t("admin.common.form.sidebar"), value: "sidebar" },
        { label: t("admin.common.form.profile"), value: "profile" },
        { label: t("admin.common.form.mobile"), value: "mobile" },
    ];

    onMounted(async () => {
        await fetchData();
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
        positionOptions,
        slug,
        setValues,
        show,
        handleStore,
        handleUpdate,
        handleDelete,
        handleDeleteMultiple,
        handleTogglePublish,
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

export type TMenuCreate = Partial<IMenu>;
export type TMenuUpdate = TMenuCreate;
