<script setup lang="ts">
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Button from "~/components/custom/Button.vue";
import OrderStatusBadge from "~/components/order/OrderStatusBadge.vue";
import OrderTimeline from "~/components/order/OrderTimeline.vue";
import OrderItemsTable from "~/components/order/OrderItemsTable.vue";
import OrderCustomerInfo from "~/components/order/OrderCustomerInfo.vue";
import OrderShippingInfo from "~/components/order/OrderShippingInfo.vue";
import OrderShipmentList from "~/components/order/OrderShipmentList.vue";
import OrderRefundList from "~/components/order/OrderRefundList.vue";
import OrderPaymentInfo from "~/components/order/OrderPaymentInfo.vue";
import OrderNote from "~/components/order/OrderNote.vue";
import { useOrder } from "~/composables/order/useOrder";
import { formatDate, canTransitionStatus } from "~/utils/order/order.helpers";
import { ORDERINDEX } from "~/utils/constants";
import { ORDER_STATUS_LABELS } from "~/utils/order/order.constants";

const route = useRoute();
const orderId = computed(() => Number(route.params.id));

const { order, isLoading, fetchOrder, handleUpdate, handleUpdateStatus, handleUpdateItemStatus, adminAllowedStatus } = useOrder();

// Form state
const showStatusModal = ref(false);
const newStatus = ref("");
const statusNote = ref("");

// Fetch order on mount
onMounted(async () => {
    await fetchOrder(orderId.value);
});

// Handle shipping address update
const handleShippingUpdate = async (payload: { shippingStreet?: string; shippingWard?: string; shippingCity?: string }) => {
    await handleUpdate(orderId.value, payload);
};

// Handle admin note update
const handleAdminNoteUpdate = async (note: string) => {
    await handleUpdate(orderId.value, { adminNote: note });
};

// Handle status update
const openStatusModal = () => {
    newStatus.value = "";
    statusNote.value = "";
    showStatusModal.value = true;
};

const confirmStatusUpdate = async () => {
    if (!newStatus.value) return;
    await handleUpdateStatus(orderId.value, newStatus.value, statusNote.value || undefined);
    showStatusModal.value = false;
};

// Get available status transitions
const availableStatuses = computed(() => {
    return adminAllowedStatus.filter((status) => canTransitionStatus(order.value?.status, status));
});

// Print order invoice
const printOrder = () => {
    if (typeof window !== "undefined") {
        window.print();
    }
};

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <h1 class="mb-0 text-uppercase fw-semibold fs-20">Chi tiết đơn hàng</h1>
            <OrderStatusBadge v-if="order?.status" :status="order.status" size="md" />
        </div>
        <Breadcrumb :crumbs="[{ label: 'Đơn hàng', to: ORDERINDEX }, { label: `#${order?.code}` }]" />
    </div>

    <!-- Loading -->
    <div v-if="isLoading && !order" class="card">
        <div class="card-body text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div v-else class="row">
        <!-- Left Column - 2/3 -->
        <div class="col-lg-8">
            <!-- Order Info Summary -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-muted mb-1 fs-12">Mã đơn hàng</p>
                            <p class="fw-semibold mb-0">#{{ order?.code }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 fs-12">Ngày tạo</p>
                            <p class="fw-medium mb-0">{{ formatDate(order?.createdAt) }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 fs-12">Cập nhật</p>
                            <p class="fw-medium mb-0">{{ formatDate(order?.updatedAt) }}</p>
                        </div>
                        <div class="col-md-3 text-end d-flex gap-2 justify-content-end align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="printOrder" title="In phiếu giao hàng">
                                <i class="ri-printer-line me-1"></i> In đơn
                            </button>
                            <Button v-if="availableStatuses.length > 0" size="sm" label="Cập nhật trạng thái" icon="ri-refresh-line" @click="openStatusModal" :loading="isLoading" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <OrderItemsTable :items="order?.items" :loading="isLoading" @update-status="(itemId, status) => handleUpdateItemStatus(orderId, itemId, status)" class="mb-3" />

            <!-- Timeline -->
            <OrderTimeline :histories="order?.statusHistories" />

            <!-- Shipments & Refunds -->
            <div v-if="order?.shipments?.length || order?.refunds?.length" class="row mb-3">
                <div v-if="order?.shipments?.length" class="col-12 mb-3">
                    <OrderShipmentList :shipments="order.shipments" />
                </div>
                <div v-if="order?.refunds?.length" class="col-12">
                    <OrderRefundList :refunds="order.refunds" @updated="fetchOrder(orderId)" />
                </div>
            </div>
        </div>

        <!-- Right Column - 1/3 -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <OrderCustomerInfo :order="order" class="mb-3" />

            <!-- Shipping Info -->
            <OrderShippingInfo :order="order" :editable="true" @update="handleShippingUpdate" class="mb-3" />

            <!-- Payment Info -->
            <OrderPaymentInfo :order="order" class="mb-3" />

            <!-- Notes -->
            <OrderNote :order="order" @update-admin-note="handleAdminNoteUpdate" />
        </div>
    </div>

    <!-- Status Update Modal -->
    <Teleport to="body">
        <div v-if="showStatusModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                        <button type="button" class="btn-close" @click="showStatusModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái hiện tại</label>
                            <div>
                                <OrderStatusBadge :status="order?.status" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Chuyển sang trạng thái
                                <span class="text-danger">*</span>
                            </label>
                            <Select v-model="newStatus" :options="availableStatuses.map((s) => ({ label: ORDER_STATUS_LABELS[s] || s, value: s }))" optionLabel="label" optionValue="value" placeholder="-- Chọn trạng thái --" class="w-100 rounded py-[1px]" size="small" overlayClass="!z-[9999]" appendTo="body" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea v-model="statusNote" class="form-control" rows="2" placeholder="Nhập ghi chú..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button label="Hủy" severity="secondary" @click="showStatusModal = false" class="rounded text-xs" size="small" />
                        <Button :loading="isLoading" :disabled="!newStatus" label="Xác nhận" severity="success" icon="ri-check-line" @click="confirmStatusUpdate" class="rounded text-xs" size="small" />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
@media print {
    header,
    .app-menu,
    .navbar-header,
    .breadcrumb,
    .btn,
    button,
    .modal {
        display: none !important;
    }

    body,
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
}
</style>
