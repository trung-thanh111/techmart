<script setup lang="ts">
import FilterBar from "~/components/navigation/FilterBar.vue";
import { closeModal } from "#imports";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Pagination from "~/components/navigation/AppPagination.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import OrderStatusBadge from "~/components/order/OrderStatusBadge.vue";
import PaymentMethodIcon from "~/components/order/PaymentMethodIcon.vue";
import { useOrder } from "~/composables/order/useOrder";
import { formatPrice, formatDate, canDeleteOrder, getDaysUntilDeletable } from "~/utils/order/order.helpers";
import { PAYMENT_STATUS_LABELS, PAYMENT_METHOD_LABELS } from "~/utils/order/order.constants";

const { data, isLoading, config, selectedIds, selectedItem, statusOptions, paymentStatusOptions, filters, search, setPage, handleDeleteMultiple, setSelectedItem, confirmDelete, updateSelectedIds, getTotalItems, getAllItemIds, checkCanDelete } = useOrder();

provide("getTotalItems", getTotalItems);
provide("getAllItemIds", getAllItemIds);

const handleSelectedIdsChange = (value: boolean | number[]) => {
    if (Array.isArray(value)) updateSelectedIds(value);
};

const handleDeleteSingle = (item: any) => setSelectedItem(item);

const handleConfirmDeleteSingle = async () => {
    await confirmDelete();
    closeModal("deleteRecord");
};

const handleConfirmDeleteMultiple = async () => {
    await handleDeleteMultiple();
    closeModal("deleteMultipleRecord");
};

const isBulkSelecting = computed(() => selectedIds.value.length >= 1);
const hasData = computed(() => Boolean(data.value?.data?.data?.length));
const hasSelectedItems = computed(() => selectedIds.value.length > 0);

const getPaymentStatusLabel = (status?: string) => PAYMENT_STATUS_LABELS[status || ""] || status || "-";
const getPaymentMethodLabel = (method?: string) => PAYMENT_METHOD_LABELS[method || ""] || method || "-";

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-uppercase fw-bold">Quản lý đơn hàng</h5>
        <Breadcrumb :crumbs="[{ label: 'Đơn hàng' }]" />
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center fz-[12px] gap-2" :class="{ 'opacity-0': !hasSelectedItems }">
                    <Button variant="delete" type="button" :label="`Xóa (${selectedIds.length})`" :loading="isLoading" :disabled="!hasSelectedItems" data-bs-toggle="modal" data-bs-target="#deleteMultipleRecord" class="rounded text-xs" size="small" />
                    <Button variant="cancel" :loading="isLoading" :disabled="!hasSelectedItems" @click="updateSelectedIds([])" class="rounded text-xs" size="small" />
                </div>
                <!-- No create button for orders -->
            </div>
            <FilterBar @search="search" :publish-filter="false">
                <div class="min-w-[180px]">
                    <Select v-model="filters.status" :options="statusOptions" option-label="label" option-value="value" filter showClear placeholder="Trạng thái đơn" class="w-full text-sm" />
                </div>
                <div class="min-w-[180px]">
                    <Select v-model="filters.paymentStatus" :options="paymentStatusOptions" option-label="label" option-value="value" filter showClear placeholder="Thanh toán" class="w-full text-sm" />
                </div>
            </FilterBar>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="table-layout: fixed">
                    <colgroup>
                        <col style="width: 40px" />
                        <col style="width: 150px" />
                        <col />
                        <col style="width: 120px" />
                        <col style="width: 120px" />
                        <col style="width: 120px" />
                        <col style="width: 130px" />
                        <col style="width: 140px" />
                        <col style="width: 80px" />
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">
                                <AppCheckbox :model-value="selectedIds" :is-select-all="true" @update:model-value="handleSelectedIdsChange" />
                            </th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thanh toán</th>
                            <th class="text-center">Phương thức</th>
                            <th class="text-center">Ngày tạo</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="9" class="text-center text-muted">Đang tải...</td>
                        </tr>
                        <template v-else>
                            <tr v-for="item in data?.data?.data ?? []" :key="item.id" class="cursor-pointer" :class="{ 'table-active': selectedIds.includes(item.id!) }">
                                <td>
                                    <AppCheckbox :model-value="selectedIds" :value="item.id!" @update:model-value="handleSelectedIdsChange" />
                                </td>
                                <td>
                                    <NuxtLink :to="`${ORDERINDEX}/${item.id}/edit`" class="fw-medium text-primary">#{{ item.code }}</NuxtLink>
                                </td>
                                <td>
                                    <div class="text-truncate italic">
                                        <span class="fw-medium">{{ item.customer?.name || "-" }}</span>
                                        <br />
                                        <small class="text-muted">(+84) {{ item.customer?.phone?.slice(1) || "-" }}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold text-danger">
                                    {{ formatPrice(item.totalAmount || 0) }}
                                </td>
                                <td class="text-center">
                                    <OrderStatusBadge :status="item.status" size="sm" />
                                </td>
                                <td class="text-center">
                                    <span :class="['badge', item.paymentStatus === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning']">
                                        {{ getPaymentStatusLabel(item.paymentStatus) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <PaymentMethodIcon :method="item.paymentMethod" size="sm" />
                                </td>
                                <td class="text-center fs-12">
                                    {{ formatDate(item.createdAt) }}
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" :class="{ disabled: isBulkSelecting }" :data-bs-toggle="isBulkSelecting ? null : 'dropdown'" @click.stop.prevent>
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <NuxtLink class="dropdown-item text-info" :to="`/order/order/${item.id}/edit`">
                                                    <i class="ri-eye-line me-1"></i>
                                                    Chi tiết
                                                </NuxtLink>
                                            </li>
                                            <li v-if="checkCanDelete(item.createdAt)">
                                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteRecord" @click.stop="handleDeleteSingle(item)">
                                                    <i class="ri-delete-bin-line me-1"></i>
                                                    Xóa
                                                </button>
                                            </li>
                                            <li v-else>
                                                <span class="dropdown-item text-muted fs-11" :title="`Còn ${getDaysUntilDeletable(item.createdAt)} ngày`">
                                                    <i class="ri-lock-line me-1"></i>
                                                    Chưa thể xóa
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="(data?.data?.data?.length ?? 0) === 0">
                                <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <Pagination v-if="!isLoading && hasData && data?.data?.links" :pagination="data.data as any" @update:page="setPage" class="mt-3" />
        </div>
    </div>

    <!-- Delete Single Modal -->
    <div id="deleteRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteRecordLabel" aria-hidden="true">
        <div class="modal-dialog zoomIn">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger uppercase" id="deleteRecordLabel">Xóa đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">Bạn có chắc muốn xóa đơn hàng này?</h5>
                    <div v-if="selectedItem" class="mt-3">
                        <p class="text-muted fz-[12px] mt-3 mb-1">Đơn hàng</p>
                        <input :value="`#${selectedItem.code}`" type="text" class="form-control border-danger border-1 text-danger" readonly />
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" class="rounded text-xs" size="small" />
                    <Button variant="delete" :loading="isLoading" label="Xóa" :disabled="!selectedItem" @click="handleConfirmDeleteSingle" class="rounded text-xs" size="small" />
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Multiple Modal -->
    <div id="deleteMultipleRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteMultipleRecordLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg zoomIn">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger uppercase" id="deleteMultipleRecordLabel">Xóa nhiều đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">Bạn có chắc muốn xóa các đơn hàng đã chọn?</h5>
                    <div class="mt-3">
                        <p class="text-muted fz-[12px] mb-2">Đã chọn ({{ selectedIds.length }}):</p>
                        <div class="border rounded p-3 bg-light max-h-[300px] overflow-y-auto">
                            <ul class="list-unstyled mb-0">
                                <li v-for="item in data?.data?.data?.filter((i) => selectedIds.includes(i.id as any))" :key="item.id" class="py-1">
                                    <div class="d-flex align-items-center">
                                        <AppCheckbox :model-value="selectedIds" :value="item.id" @update:model-value="handleSelectedIdsChange" class="me-2" />
                                        <span class="text-danger">#{{ item.code }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" class="rounded text-xs" size="small" />
                    <Button variant="delete" :loading="isLoading" label="Xóa tất cả" :disabled="selectedIds.length === 0" @click="handleConfirmDeleteMultiple" class="rounded text-xs" size="small" />
                </div>
            </div>
        </div>
    </div>
</template>
