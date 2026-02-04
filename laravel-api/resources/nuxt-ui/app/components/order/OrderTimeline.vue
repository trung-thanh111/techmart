<script setup lang="ts">
import type { IOrderStatusHistory } from "~/interfaces/order/order.interface";
import { ORDER_STATUS_LABELS } from "~/utils/order/order.constants";

const props = defineProps<{
    histories?: IOrderStatusHistory[];
}>();

const getStatusLabel = (status?: string) => ORDER_STATUS_LABELS[status || ""] || status || "-";

// Sort histories by createdAt desc without grouping
const sortedHistories = computed(() => {
    if (!props.histories?.length) return [];
    return [...props.histories].sort((a, b) => new Date(b.createdAt!).getTime() - new Date(a.createdAt!).getTime());
});

const formatDateTime = (dateString?: string) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Lịch sử trạng thái</h6>
        </div>
        <div class="card-body">
            <div v-if="!histories?.length" class="text-muted text-center py-3">Chưa có lịch sử</div>

            <div v-else class="timeline-2">
                <div class="timeline-continue">
                    <div v-for="(history, idx) in sortedHistories" :key="history.id || idx" class="row timeline-right">
                        <div class="col-12">
                            <p class="timeline-date">
                                {{ formatDateTime(history.createdAt) }}
                            </p>
                        </div>
                        <div class="col-12">
                            <div class="timeline-box p-3 shadow border border-gray-100">
                                <div class="timeline-text">
                                    <div class="d-flex align-items-center">
                                        <div class="timeline-icon flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-info-subtle text-info">
                                                    <i class="ri-record-circle-line fs-16"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 fs-14 fw-semibold text-sky-600">
                                                {{ getStatusLabel(history.newStatus) }}
                                                <small v-if="history.oldStatus" class="text-muted fw-normal ms-1">(từ {{ getStatusLabel(history.oldStatus) }})</small>
                                            </h5>
                                            <p v-if="history.note" class="text-muted mb-0">{{ history.note }}</p>
                                            <p v-if="history?.user" class="text-muted mb-0 fs-12 mt-1">
                                                <i class="ri-user-line me-1"></i>
                                                {{ history?.user?.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.timeline-2 {
    position: relative;
}

.timeline-2::after {
    position: absolute;
    content: "";
    width: 3px;
    height: 100%;
    top: 0px;
    left: 40px;
    margin-left: -1px;
    background: #0ea5e9;
}

.timeline-date {
    margin-bottom: 0.5rem;
    font-weight: 500;
}
</style>
