import { useI18n } from "vue-i18n";
import { useAppToast } from "~/composables/useAppToast";
import { orderService } from "~/services/order/order.service";
import type { IApiOk, IPaginate, BasePayloadFilter } from "~/interfaces/base.interface";
import type { IOrder, OrderPayloadFilter, OrderUpdatePayload } from "~/interfaces/order/order.interface";
import { canDeleteOrder, getDaysUntilDeletable } from "~/utils/order/order.helpers";
import { ORDER_STATUS_OPTIONS, PAYMENT_STATUS_OPTIONS, PAYMENT_METHOD_OPTIONS, ADMIN_ALLOWED_STATUS } from "~/utils/order/order.constants";

export const useOrder = (initialFilters?: OrderPayloadFilter) => {
    const { t } = useI18n();
    const config = useRuntimeConfig();
    const toast = useAppToast();

    // State
    const data = ref<IApiOk<IPaginate<IOrder>>>();
    const order = ref<IOrder | null>(null);
    const filters = ref<OrderPayloadFilter>(
        initialFilters ?? {
            keyword: "",
            perpage: 20,
            status: null,
            publish: null,
            paymentStatus: null,
            paymentMethod: null,
        },
    );
    const page = ref(1);
    const isLoading = ref(false);
    const selectedIds = ref<number[]>([]);
    const selectedItem = ref<IOrder | null>(null);

    // Options
    const statusOptions = ORDER_STATUS_OPTIONS;
    const paymentStatusOptions = PAYMENT_STATUS_OPTIONS;
    const paymentMethodOptions = PAYMENT_METHOD_OPTIONS;
    const adminAllowedStatus = ADMIN_ALLOWED_STATUS;

    // Fetch list
    const fetchData = async () => {
        isLoading.value = true;
        try {
            const { status, paymentStatus, paymentMethod, ...rest } = filters.value || {};
            const response = await orderService.pagination({
                ...rest,
                status: status || undefined,
                payment_status: paymentStatus || undefined,
                payment_method: paymentMethod || undefined,
                page: page.value,
                relations: "order_items",
            });
            data.value = response;
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
            console.error("Lỗi tải dữ liệu đơn hàng:", err);
        } finally {
            isLoading.value = false;
        }
    };

    // Fetch single order with full relations
    const fetchOrder = async (id: number | string) => {
        isLoading.value = true;
        try {
            const response = await orderService.show(id, {
                relations: "order_items,order_status_histories,order_payments,order_shipments,order_refunds",
            });
            order.value = response.data;
            return response;
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.loadDataError"));
            console.error("Lỗi tải chi tiết đơn hàng:", err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Update order
    const handleUpdate = async (id: number, payload: OrderUpdatePayload) => {
        isLoading.value = true;
        try {
            await orderService.update(id, payload);
            toast.success(t("admin.messages.updateSuccess"));
            await fetchOrder(id);
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.updateFailed"));
            console.error("Lỗi cập nhật đơn hàng:", err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Update status with note
    const handleUpdateStatus = async (id: number, status: string, note?: string) => {
        isLoading.value = true;
        try {
            await orderService.updateStatus(id, status, note);
            toast.success(t("admin.messages.updateSuccess"));
            await fetchOrder(id);
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.updateFailed"));
            console.error("Lỗi cập nhật trạng thái:", err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Update refund status
    const handleUpdateRefundStatus = async (orderId: number, refundId: number, status: string) => {
        isLoading.value = true;
        try {
            await orderService.updateRefundStatus(orderId, refundId, status);
            toast.success(t("admin.messages.updateSuccess"));
            await fetchOrder(orderId);
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.updateFailed"));
            console.error("Lỗi cập nhật trạng thái hoàn tiền:", err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Delete single
    const handleDelete = async (id: number) => {
        const orderToDelete = data.value?.data?.data?.find((o) => o.id === id);
        if (!canDeleteOrder(orderToDelete?.createdAt)) {
            const daysLeft = getDaysUntilDeletable(orderToDelete?.createdAt);
            toast.error(t("admin.messages.orderDeleteRestricted", { days: daysLeft }));
            return;
        }

        isLoading.value = true;
        try {
            await orderService.delete(id);
            toast.success(t("admin.messages.deleteSuccess"));
            await fetchData();
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.deleteFailed"));
        } finally {
            isLoading.value = false;
        }
    };

    // Delete multiple
    const handleDeleteMultiple = async () => {
        if (selectedIds.value.length === 0) return;

        // Check all selected orders can be deleted
        const orders = data.value?.data?.data?.filter((o) => selectedIds.value.includes(o.id!)) || [];
        const nonDeletable = orders.filter((o) => !canDeleteOrder(o.createdAt));
        if (nonDeletable.length > 0) {
            toast.error(t("admin.messages.orderDeleteMultipleRestricted", { count: nonDeletable.length }));
            return;
        }

        isLoading.value = true;
        try {
            await orderService.bulkDelete(selectedIds.value);
            toast.success(t("admin.messages.deleteMultipleSuccess", { count: selectedIds.value.length }));
            selectedIds.value = [];
            await fetchData();
        } catch (err: any) {
            toast.error(err?.response?.data?.message || t("admin.messages.deleteFailed"));
        } finally {
            isLoading.value = false;
        }
    };

    // Set selected item for delete modal
    const setSelectedItem = (item: IOrder) => {
        selectedItem.value = item;
    };

    // Confirm delete from modal
    const confirmDelete = async () => {
        if (!selectedItem.value?.id) return;
        await handleDelete(selectedItem.value.id);
        selectedItem.value = null;
    };

    // Search & filter
    const search = async (newFilters: BasePayloadFilter) => {
        filters.value = { ...filters.value, ...newFilters };
        page.value = 1;
        selectedIds.value = [];
        await fetchData();
    };

    // Pagination
    const setPage = async (newPage: number) => {
        page.value = newPage;
        selectedIds.value = [];
        await fetchData();
    };

    // Update selected IDs
    const updateSelectedIds = (ids: number[]) => {
        selectedIds.value = ids;
    };

    // Get total items for checkbox
    const getTotalItems = () => {
        return data.value?.data?.data?.length || 0;
    };

    // Get all item IDs for select all
    const getAllItemIds = () => {
        return data.value?.data?.data?.map((item) => item.id) || [];
    };

    // Check if order can be deleted
    const checkCanDelete = (createdAt?: string) => {
        return canDeleteOrder(createdAt);
    };

    // Computed
    const hasData = computed(() => Boolean(data.value?.data?.data?.length));
    const hasSelectedItems = computed(() => selectedIds.value.length > 0);

    // Initialize on mount
    onMounted(async () => {
        await fetchData();
    });

    return {
        // State
        data,
        order,
        isLoading,
        filters,
        config,
        page,
        selectedIds,
        selectedItem,

        // Options
        statusOptions,
        paymentStatusOptions,
        paymentMethodOptions,
        adminAllowedStatus,

        // Computed
        hasData,
        hasSelectedItems,

        // Methods
        fetchData,
        fetchOrder,
        handleUpdate,
        handleUpdateStatus,
        handleUpdateRefundStatus,
        handleDelete,
        handleDeleteMultiple,
        setSelectedItem,
        confirmDelete,
        search,
        setPage,
        updateSelectedIds,
        getTotalItems,
        getAllItemIds,
        checkCanDelete,
    };
};
