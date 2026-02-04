<script setup lang="ts">
import type { IOrderRefund } from "~/interfaces/order/order.interface";
import { formatPrice, formatDate } from "~/utils/order/order.helpers";
import { REFUND_STATUS_LABELS, REFUND_REASON_LABELS } from "~/utils/order/order.constants";
import { useOrder } from "~/composables/order/useOrder";

defineProps<{
    refunds?: IOrderRefund[];
}>();

const emit = defineEmits(["updated"]);

const { handleUpdateRefundStatus, isLoading } = useOrder();

// State for modal
const showUpdateModal = ref(false);
const selectedRefund = ref<IOrderRefund | null>(null);
const newStatus = ref("");

// Available status transitions
const refundStatuses = Object.entries(REFUND_STATUS_LABELS).map(([value, label]) => ({
    label,
    value,
}));

const getStatusColor = (status?: string) => {
    switch (status) {
        case "completed":
        case "approved":
            return "bg-success-subtle text-success";
        case "pending":
            return "bg-warning-subtle text-warning";
        case "rejected":
        case "failed":
            return "bg-danger-subtle text-danger";
        case "processing":
            return "bg-info-subtle text-info";
        default:
            return "bg-light text-muted";
    }
};

const openUpdateModal = (refund: IOrderRefund) => {
    selectedRefund.value = refund;
    newStatus.value = refund.status || "";
    showUpdateModal.value = true;
};

const closeUpdateModal = () => {
    selectedRefund.value = null;
    showUpdateModal.value = false;
    newStatus.value = "";
};

const confirmUpdate = async () => {
    if (!selectedRefund.value?.orderId || !selectedRefund.value?.id || !newStatus.value) return;

    await handleUpdateRefundStatus(selectedRefund.value.orderId, selectedRefund.value.id, newStatus.value);
    emit("updated");
    closeUpdateModal();
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Lịch sử hoàn tiền</h6>
        </div>
        <div class="card-body p-0">
            <div v-if="!refunds?.length" class="text-center text-muted py-3">Chưa có yêu cầu hoàn tiền</div>
            <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã hoàn tiền</th>
                            <th>Lý do</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end">Số tiền</th>
                            <th class="text-end">Ngày tạo</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="refund in refunds" :key="refund.id">
                            <td class="fw-medium">{{ refund.refundNumber || "-" }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px" :title="refund.reasonDescription">
                                    {{ REFUND_REASON_LABELS[refund.reason || ""] || refund.reason }}
                                </div>
                                <small v-if="refund.reasonDescription" class="text-muted fs-11">{{ refund.reasonDescription }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge" :class="getStatusColor(refund.status)">{{ REFUND_STATUS_LABELS[refund.status || ""] || refund.status }}</span>
                            </td>
                            <td class="text-end fw-semibold text-danger">{{ formatPrice(refund.amount || 0) }}</td>
                            <td class="text-end">{{ formatDate(refund.createdAt) }}</td>
                            <td class="text-end">
                                <Button v-if="refund.status === 'pending' || refund.status === 'processing'" icon="ri-edit-line" size="small" severity="secondary" class="rounded text-xs p-1" @click="openUpdateModal(refund)" v-tooltip="'Cập nhật trạng thái'" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Update Status Modal -->
        <Dialog v-model:visible="showUpdateModal" modal header="Cập nhật trạng thái hoàn tiền" :style="{ width: '400px' }">
            <div class="vstack gap-3">
                <div v-if="selectedRefund" class="alert alert-soft-info mb-0 p-2">
                    Bạn đang cập nhật trạng thái cho yêu cầu
                    <strong>{{ selectedRefund.refundNumber }}</strong>
                </div>

                <div class="d-flex flex-column gap-2">
                    <label class="form-label mb-0 required">Trạng thái</label>
                    <Select v-model="newStatus" :options="refundStatuses" optionLabel="label" optionValue="value" placeholder="Chọn trạng thái" class="w-100 rounded py-[1px]" overlayClass="!z-[99999]" size="small" checkmark />
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <Button label="Hủy" severity="secondary" @click="closeUpdateModal" class="rounded text-xs" size="small" />
                    <Button label="Cập nhật" severity="success" :loading="isLoading" @click="confirmUpdate" class="rounded text-xs" size="small" />
                </div>
            </div>
        </Dialog>
    </div>
</template>
