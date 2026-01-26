import z from "zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import { errorToField, slugify } from "~/utils/helpers";
import { toTypedSchema } from "@vee-validate/zod";
import { useAppToast } from "~/composables/useAppToast";
import { popupService } from "~/services/system/popup.service";
import type { BasePayloadFilter, IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPopup } from "~/interfaces/system/popup.interface";
import { POPUPINDEX, MAX_FILE_SIZE, ACCEPTED_IMAGE_TYPES } from "~/utils/constants";

export const usePopup = (initialFilters?: Record<string, any>, mode: "create" | "update" = "create") => {
    const { t } = useI18n();
    const config = useRuntimeConfig();
    const toast = useAppToast();

    const data = ref<IApiOk<IPaginate<IPopup>>>();
    const filters = ref<Record<string, any>>(initialFilters ?? { keyword: "", perpage: 20, publish: null });
    const page = ref(1);
    const isLoading = ref(false);
    const selectedIds = ref<number[]>([]);
    const selectedItem = ref<IPopup | null>(null);
    const slug = computed(() => slugify(values.title || ""));

    const schema = {
        title: z.string({ required_error: "admin.validation.titleRequired" }).trim().min(1, "admin.validation.titleRequired").max(255, "admin.validation.titleMaxLength"),
        type: z.string({ required_error: "admin.validation.typeRequired" }).refine((v) => ["promotion", "notification", "confirm", "form", "guide"].includes(v), { message: "admin.validation.typeInvalid" }),
        slug: z.string().max(255, "admin.validation.slugMaxLength").nullable().optional(),
        content: z.string().nullable().optional(),
        image: z
            .any()
            .optional()
            .refine((file) => !file || file.size <= MAX_FILE_SIZE, "admin.common.maxFileSize")
            .refine((file) => !file || ACCEPTED_IMAGE_TYPES.includes(file.type), "admin.common.acceptedImageTypes"),
        link: z.string().url().max(255).nullable().optional(),
        buttonText: z.string().max(255).nullable().optional(),
        formHtml: z.string().nullable().optional(),
        showDelay: z.number().min(0).nullable().optional(),
        priority: z.boolean().default(false).optional(),
        publish: z.number({ required_error: "admin.validation.statusRequired" }).refine((val) => [0, 1, 2].includes(val), { message: "admin.validation.statusInvalid" }),
    };

    const createSchema = z.object({ ...schema });
    const updateSchema = z.object({ ...schema });
    type TPopupCreate = z.infer<typeof createSchema>;
    type TPopupUpdate = z.infer<typeof updateSchema>;

    const { handleSubmit, values, errors, setValues, resetForm, setFieldError } = useForm<TPopupCreate | TPopupUpdate>({
        validationSchema: toTypedSchema(mode === "create" ? createSchema : updateSchema),
    });

    const typeOptions = computed(() => [
        { label: t("admin.page.popup.type.promotion"), value: "promotion" },
        { label: t("admin.page.popup.type.notification"), value: "notification" },
        { label: t("admin.page.popup.type.confirm"), value: "confirm" },
        { label: t("admin.page.popup.type.form"), value: "form" },
        { label: t("admin.page.popup.type.guide"), value: "guide" },
    ]);

    const handleStore = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TPopupCreate | TPopupUpdate) => {
            isLoading.value = true;
            try {
                const response = await popupService.store(payload as Partial<IPopup>);
                toast.success(response?.message || t("admin.messages.createSuccess"));
                if ((payload as any).priority === true) {
                    const newId = response?.data?.id;
                    if (newId) {
                        try {
                            await unsetOtherPriorities(newId);
                        } catch (err) {
                            console.error("Error unsetting other priorities after create:", err);
                        }
                    }
                }

                if (shouldClose) {
                    return navigateTo(POPUPINDEX);
                }
                resetForm();
            } catch (error: any) {
                if (error?.data?.isValidationError) {
                    errorToField(setFieldError, error?.data?.errors, t, "title");
                    return;
                }
                console.error("Store error:", error);
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
            const response = await popupService.show(id);
            return response;
        } catch (error: any) {
            console.error("Not found:", error);
            toast.error(error?.response?.data?.message || t("admin.messages.loadDataError"));
            throw error;
        }
    };

    const handleUpdate = (shouldClose = false) => {
        const wrapped = handleSubmit(async (payload: TPopupUpdate) => {
            isLoading.value = true;
            try {
                const id = Number(useRoute().params.id);
                await popupService.update(id, payload as Partial<IPopup>);
                toast.success(t("admin.messages.updateSuccess"));
                // if priority is set true on this record, unset priority on others
                if ((payload as any).priority === true) {
                    try {
                        await unsetOtherPriorities(id);
                    } catch (err) {
                        console.error("Error unsetting other priorities after update:", err);
                    }
                }

                if (shouldClose) {
                    return navigateTo(POPUPINDEX);
                }
            } catch (error: any) {
                if (error?.data?.isValidationError) {
                    errorToField(setFieldError, error?.data?.errors, t, "title");
                    return;
                }
                console.error("Update error:", error);
                toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
            } finally {
                isLoading.value = false;
            }
        });

        return wrapped();
    };

    const handleTogglePriority = async (item: any) => {
        try {
            await popupService.update(item.id, { priority: item.priority });
            // update local data immediately
            if (data.value?.data?.data) {
                data.value.data.data = data.value.data.data.map((r: any) => {
                    if (r.id === item.id) return { ...r, priority: item.priority };
                    // if this item just set to true, unset others locally
                    if (item.priority === true) return { ...r, priority: false };
                    return r;
                });
            }
            if (item.priority === true) {
                try {
                    await unsetOtherPriorities(item.id);
                } catch (err) {
                    console.error("Error unsetting other priorities after toggle:", err);
                }
            }
            toast.success(t("admin.messages.updateSuccess"));
        } catch (error: any) {
            toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
            item.priority = !item.priority;
        }
    };

    const unsetOtherPriorities = async (exceptId: number) => {
        try {
            const res = await popupService.pagination({ perpage: 200, page: 1, type: "all" });
            const rows: any[] = res?.data?.data ?? [];
            const toUpdate = rows.filter((r) => r.id && r.id !== exceptId && r.priority === true);
            if (toUpdate.length === 0) return;
            await Promise.all(
                toUpdate.map((r) =>
                    popupService.update(Number(r.id), {
                        priority: false,
                    }),
                ),
            );
            // update local data to reflect changes without reload
            if (data.value?.data?.data) {
                data.value.data.data = data.value.data.data.map((r: any) => {
                    if (r.id === exceptId) return { ...r, priority: true };
                    return { ...r, priority: false };
                });
            }
        } catch (err) {
            console.error("unsetOtherPriorities error:", err);
            throw err;
        }
    };

    const handleDelete = async (id: number) => {
        isLoading.value = true;
        try {
            await popupService.delete(id);
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
            await popupService.deleteMultiple(selectedIds.value);
            toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
            selectedIds.value = [];
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || t("admin.messages.deleteFailed"));
        } finally {
            isLoading.value = false;
        }
    };

    const setSelectedItem = (item: IPopup) => {
        selectedItem.value = item;
    };

    const confirmDelete = async () => {
        if (!selectedItem.value) return;
        await handleDelete(selectedItem.value.id!);
        selectedItem.value = null;
    };

    const fetchData = async () => {
        isLoading.value = true;
        try {
            const response = await popupService.pagination({
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
        typeOptions,
        handleUpdate,
        handleDelete,
        handleDeleteMultiple,
        handleTogglePriority,
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

export type TPopupCreate = Partial<IPopup>;
export type TPopupUpdate = TPopupCreate;
