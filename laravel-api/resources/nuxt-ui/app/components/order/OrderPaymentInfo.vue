<script setup lang="ts">
import type { IOrder } from "~/interfaces/order/order.interface";
import { PAYMENT_METHOD_LABELS, PAYMENT_STATUS_LABELS } from "~/utils/order/order.constants";
import { formatPrice, formatDate, getPaymentStatusClass } from "~/utils/order/order.helpers";

defineProps<{
    order?: IOrder | null;
}>();

const getMethodLabel = (method?: string) => PAYMENT_METHOD_LABELS[method || ""] || method || "-";
const getStatusLabel = (status?: string | null) => {
    if (!status) return "-";
    return PAYMENT_STATUS_LABELS[status] || status;
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Thanh toán</h6>
        </div>
        <div class="card-body">
            <!-- Summary -->
            <div class="table-responsive mb-3">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Phương thức:</td>
                            <td class="text-end fw-medium">{{ getMethodLabel(order?.paymentMethod) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Trạng thái chung:</td>
                            <td class="text-end">
                                <span :class="['badge', getPaymentStatusClass(order?.paymentStatus)]">
                                    {{ getStatusLabel(order?.paymentStatus) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Order totals -->
            <div class="border-top pt-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Tạm tính:</span>
                    <span>{{ formatPrice(order?.subtotal || 0) }}</span>
                </div>
                <div v-if="order?.discountAmount" class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Giảm giá:</span>
                    <span class="text-success">-{{ formatPrice(order.discountAmount) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Phí ship:</span>
                    <span>{{ formatPrice(order?.shippingFee || 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Thuế (VAT):</span>
                    <span>{{ formatPrice(order?.taxAmount || 0) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fw-semibold">Tổng cộng:</span>
                    <span class="fw-bold text-danger fs-16">{{ formatPrice(order?.totalAmount || 0) }}</span>
                </div>
            </div>

            <!-- Payment history -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fs-13 fw-semibold mb-3">Lịch sử giao dịch ({{ order?.payments?.length || 0 }})</h6>

                <div v-if="!order?.payments?.length" class="text-center text-muted fs-12">Chưa có giao dịch</div>

                <div v-else class="vstack gap-3">
                    <div v-for="payment in order.payments" :key="payment.id" class="border rounded p-3 bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-medium text-success">{{ formatPrice(payment.amount || 0) }}</div>
                                <small class="text-muted">{{ formatDate(payment.createdAt) }}</small>
                            </div>
                            <span :class="['badge', getPaymentStatusClass(payment.status)]">{{ getStatusLabel(payment.status) }}</span>
                        </div>

                        <div class="fs-12 text-muted">
                            <div v-if="payment.transactionId" class="d-flex justify-content-between">
                                <span>Mã giao dịch:</span>
                                <code>{{ payment.transactionId }}</code>
                            </div>
                            <div v-if="payment.paymentGateway" class="d-flex justify-content-between mt-1">
                                <span>Cổng thanh toán:</span>
                                <span>{{ payment.paymentGateway }}</span>
                            </div>
                            <div v-if="payment.cardBrand" class="d-flex justify-content-between mt-1">
                                <span>Thẻ:</span>
                                <span>{{ payment.cardBrand }} •••• {{ payment.cardLast4 }}</span>
                            </div>
                            <div v-if="payment.failureReason" class="mt-2 text-danger bg-danger-subtle p-2 rounded">Lỗi: {{ payment.failureReason }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
