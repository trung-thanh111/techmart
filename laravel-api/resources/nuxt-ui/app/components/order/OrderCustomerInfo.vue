<script setup lang="ts">
import type { IOrder } from "~/interfaces/order/order.interface";

defineProps<{
    order?: IOrder | null;
}>();

const formatPhone = (phone?: string) => {
    if (!phone) return "-";
    if (phone.startsWith("0")) return `(+84) ${phone.slice(1)}`;
    return phone;
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Thông tin khách hàng</h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                        {{ order?.customer?.name?.charAt(0)?.toUpperCase() || "?" }}
                    </div>
                </div>
                <div>
                    <h6 class="mb-0">{{ order?.customer?.name || "-" }}</h6>
                    <small class="text-muted">ID: {{ order?.userId || "-" }}</small>
                </div>
            </div>
            <ul class="list-unstyled mb-0 vstack gap-2">
                <li class="d-flex align-items-center gap-2">
                    <i class="ri-mail-line text-muted"></i>
                    <span>{{ order?.customer?.email || "-" }}</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                    <i class="ri-phone-line text-muted"></i>
                    <span>{{ formatPhone(order?.customer?.phone) }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
