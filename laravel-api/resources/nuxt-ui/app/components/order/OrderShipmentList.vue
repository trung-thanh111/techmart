<script setup lang="ts">
import type { IOrderShipment } from "~/interfaces/order/order.interface";
import { formatPrice, formatDate } from "~/utils/order/order.helpers";
import { SHIPMENT_STATUS_LABELS, SHIPMENT_SERVICE_LABELS } from "~/utils/order/order.constants";

defineProps<{
    shipments?: IOrderShipment[];
}>();

const getStatusColor = (status?: string) => {
    const colors: Record<string, string> = {
        pending: "bg-warning-subtle text-warning",
        picking: "bg-info-subtle text-info",
        picked_up: "bg-primary-subtle text-primary",
        in_transit: "bg-info-subtle text-info",
        out_for_delivery: "bg-primary-subtle text-primary",
        delivered: "bg-success-subtle text-success",
        failed: "bg-danger-subtle text-danger",
        returned: "bg-secondary-subtle text-secondary",
    };
    return colors[status || ""] || "bg-light text-muted";
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Thông tin vận chuyển</h6>
        </div>
        <div class="card-body p-0">
            <div v-if="!shipments?.length" class="text-center text-muted py-3">Chưa có thông tin vận chuyển</div>
            <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã vận đơn</th>
                            <th>Đơn vị vận chuyển</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end">Phí ship</th>
                            <th class="text-end">Ngày giao (dự kiến)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="shipment in shipments" :key="shipment.id">
                            <td>
                                <div class="fw-medium">{{ shipment.trackingNumber || "-" }}</div>
                                <small v-if="shipment.createdAt" class="text-muted fs-11">Tạo: {{ formatDate(shipment.createdAt) }}</small>
                            </td>
                            <td>
                                <div>{{ shipment.carrier || "-" }}</div>
                                <small v-if="shipment.serviceType" class="text-muted fs-11">{{ SHIPMENT_SERVICE_LABELS[shipment.serviceType || ""] || shipment.serviceType }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge" :class="getStatusColor(shipment.status)">{{ SHIPMENT_STATUS_LABELS[shipment.status || ""] || shipment.status }}</span>
                            </td>
                            <td class="text-end">{{ formatPrice(shipment.shippingCost || 0) }}</td>
                            <td class="text-end">
                                <span v-if="shipment.deliveredAt" class="text-success">{{ formatDate(shipment.deliveredAt) }}</span>
                                <span v-else-if="shipment.estimatedDeliveryAt" class="text-muted">{{ formatDate(shipment.estimatedDeliveryAt) }}</span>
                                <span v-else>-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
