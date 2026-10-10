<script setup lang="ts">
import FilterBar from "~/components/navigation/FilterBar.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import AppPagination from "~/components/navigation/AppPagination.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import Select from "primevue/select";
import { closeModal } from "#imports";
import { useProductReview } from "~/composables/product/useProductReview";
import type { IProductReview } from "~/interfaces/product/review.interface";
import { PRODUCTINDEX } from "~/utils/constants";

const {
    data,
    isLoading,
    filters,
    selectedIds,
    selectedItem,
    ratingOptions,
    publishOptions,
    search,
    setPage,
    updateStatus,
    handleBulkStatus,
    handleDeleteMultiple,
    setSelectedItem,
    confirmDelete,
    updateSelectedIds,
    getTotalItems,
    getAllItemIds,
} = useProductReview();

provide("getTotalItems", getTotalItems);
provide("getAllItemIds", getAllItemIds);

const viewingReview = ref<IProductReview | null>(null);

const handleSelectedIdsChange = (value: boolean | number[]) => {
    if (Array.isArray(value)) updateSelectedIds(value);
};

const handleOpenDetail = (item: IProductReview) => {
    viewingReview.value = item;
};

const handleDeleteSingle = (item: IProductReview) => setSelectedItem(item);

const handleConfirmDeleteSingle = async () => {
    await confirmDelete();
    closeModal("deleteRecord");
};

const handleConfirmDeleteMultiple = async () => {
    await handleDeleteMultiple();
    closeModal("deleteMultipleRecord");
};

const handleQuickApproveModal = async (item: IProductReview) => {
    await updateStatus(item.id, 2);
    if (viewingReview.value?.id === item.id) {
        viewingReview.value.publish = 2;
    }
};

const handleQuickHideModal = async (item: IProductReview) => {
    await updateStatus(item.id, 0);
    if (viewingReview.value?.id === item.id) {
        viewingReview.value.publish = 0;
    }
};

const hasData = computed(() => Boolean(data.value?.data?.data?.length));
const hasSelectedItems = computed(() => selectedIds.value.length > 0);

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-uppercase fw-bold">Quản lý Đánh giá</h5>
        <Breadcrumb :crumbs="[{ label: 'Sản phẩm', to: PRODUCTINDEX }, { label: 'Đánh giá' }]" />
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Bulk Action Toolbar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2" :class="{ 'opacity-0 pointer-events-none': !hasSelectedItems }">
                    <span class="text-muted text-xs me-1">Đã chọn: <strong>{{ selectedIds.length }}</strong></span>
                    <button
                        type="button"
                        class="btn btn-sm btn-success d-flex align-items-center gap-1"
                        :disabled="!hasSelectedItems || isLoading"
                        @click="handleBulkStatus(2)"
                    >
                        <i class="ri-check-line"></i> Duyệt hiển thị
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-warning d-flex align-items-center gap-1 text-white"
                        :disabled="!hasSelectedItems || isLoading"
                        @click="handleBulkStatus(0)"
                    >
                        <i class="ri-eye-off-line"></i> Ẩn đánh giá
                    </button>
                    <Button
                        variant="delete"
                        type="button"
                        :label="`Xóa (${selectedIds.length})`"
                        :loading="isLoading"
                        :disabled="!hasSelectedItems"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteMultipleRecord"
                        class="btn-sm"
                    />
                    <Button
                        variant="cancel"
                        :loading="isLoading"
                        :disabled="!hasSelectedItems"
                        @click="updateSelectedIds([])"
                        class="btn-sm"
                    />
                </div>
            </div>

            <!-- Filters -->
            <FilterBar @search="search" :publish-filter="true">
                <div class="min-w-[180px]">
                    <Select
                        v-model="filters.rating"
                        :options="ratingOptions"
                        option-label="label"
                        option-value="value"
                        placeholder="Lọc số sao"
                        class="w-full text-sm"
                        @change="search(filters)"
                    />
                </div>
            </FilterBar>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="table-layout: fixed">
                    <colgroup>
                        <col style="width: 40px" />
                        <col style="width: 220px" />
                        <col style="width: 180px" />
                        <col style="width: 130px" />
                        <col />
                        <col style="width: 90px" />
                        <col style="width: 140px" />
                        <col style="width: 140px" />
                        <col style="width: 130px" />
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px">
                                <AppCheckbox
                                    :model-value="selectedIds"
                                    :is-select-all="true"
                                    @update:model-value="handleSelectedIdsChange"
                                />
                            </th>
                            <th>Sản phẩm</th>
                            <th>Khách hàng</th>
                            <th class="text-center">Số sao</th>
                            <th>Đánh giá</th>
                            <th class="text-center">Lượt thích</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thời gian</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="9" class="text-center text-muted py-4">Đang tải dữ liệu đánh giá...</td>
                        </tr>
                        <tr v-else-if="!hasData">
                            <td colspan="9" class="text-center text-muted py-5">
                                Không có dữ liệu
                            </td>
                        </tr>
                        <template v-else>
                            <tr
                                v-for="item in data?.data?.data ?? []"
                                :key="item.id"
                                :class="{ 'table-active': selectedIds.includes(item.id) }"
                            >
                                <td>
                                    <AppCheckbox
                                        :model-value="selectedIds"
                                        :value="item.id"
                                        @update:model-value="handleSelectedIdsChange"
                                    />
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            :src="item.product?.image ? (item.product.image.startsWith('http') ? item.product.image : `/storage/${item.product.image}`) : '/images/defaults/record.jpg'"
                                            alt="product-thumb"
                                            class="rounded object-fit-cover flex-shrink-0"
                                            style="width: 44px; height: 44px"
                                        />
                                        <div class="overflow-hidden">
                                            <span class="d-block fw-semibold text-truncate text-sm" :title="item.product?.name || `Sản phẩm #${item.product_id}`">
                                                {{ item.product?.name || `ID #${item.product_id}` }}
                                            </span>
                                            <small class="text-muted text-xs">Mã: #{{ item.product_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            :src="item.user_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(item.user_name || 'User') + '&background=0284c7&color=fff'"
                                            alt="avatar"
                                            class="rounded-circle object-fit-cover flex-shrink-0"
                                            style="width: 32px; height: 32px"
                                        />
                                        <div class="overflow-hidden">
                                            <div class="fw-medium text-truncate text-sm">{{ item.user_name }}</div>
                                            <small class="text-muted text-truncate d-block text-xs">{{ item.user_email || 'Khách vãng lai' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-warning text-xs mb-1">
                                        <i v-for="s in 5" :key="s" :class="s <= item.rating ? 'ri-star-fill' : 'ri-star-line'"></i>
                                    </div>
                                    <span class="badge bg-light text-dark border">{{ item.rating }} / 5</span>
                                </td>
                                <td>
                                    <p class="mb-1 text-sm text-truncate-2" :title="item.comment">
                                        {{ item.comment }}
                                    </p>
                                    <div v-if="item.images && item.images.length" class="d-flex gap-1 mt-1">
                                        <span class="badge bg-info-subtle text-info text-xs">
                                            <i class="ri-image-line me-1"></i>{{ item.images.length }} ảnh
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted text-xs">
                                        <i class="ri-heart-line text-danger me-1"></i>{{ item.likes_count || 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        v-if="item.publish === 2"
                                        class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 cursor-pointer"
                                        title="Click để ẩn đánh giá"
                                        @click="updateStatus(item.id, 0)"
                                    >
                                        <i class="ri-checkbox-circle-line me-1"></i>Đã duyệt
                                    </span>
                                    <span
                                        v-else-if="item.publish === 1"
                                        class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 cursor-pointer"
                                        title="Click để duyệt hiển thị"
                                        @click="updateStatus(item.id, 2)"
                                    >
                                        <i class="ri-time-line me-1"></i>Chờ duyệt
                                    </span>
                                    <span
                                        v-else
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 cursor-pointer"
                                        title="Click để duyệt hiển thị"
                                        @click="updateStatus(item.id, 2)"
                                    >
                                        <i class="ri-eye-off-line me-1"></i>Đã ẩn
                                    </span>
                                </td>
                                <td class="text-center text-muted text-xs">
                                    <div>{{ item.created_at || '-' }}</div>
                                    <small class="text-secondary">{{ item.created_at_human }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-primary px-2 py-1"
                                            title="Xem chi tiết"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewReviewModal"
                                            @click="handleOpenDetail(item)"
                                        >
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button
                                            v-if="item.publish !== 2"
                                            type="button"
                                            class="btn btn-sm btn-soft-success px-2 py-1"
                                            title="Duyệt nhanh"
                                            @click="updateStatus(item.id, 2)"
                                        >
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button
                                            v-if="item.publish !== 0"
                                            type="button"
                                            class="btn btn-sm btn-soft-warning px-2 py-1"
                                            title="Ẩn nhanh"
                                            @click="updateStatus(item.id, 0)"
                                        >
                                            <i class="ri-eye-off-line"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-danger px-2 py-1"
                                            title="Xóa đánh giá"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteRecord"
                                            @click="handleDeleteSingle(item)"
                                        >
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="hasData && data?.data" class="mt-4">
                <AppPagination :pagination="data.data" @update:page="setPage" />
            </div>
        </div>
    </div>

    <!-- Modal Xem Chi Tiết Đánh Giá -->
    <div id="viewReviewModal" class="modal fade" tabindex="-1" aria-labelledby="viewReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-bold" id="viewReviewModalLabel">
                        <i class="ri-feedback-line text-primary me-2"></i>Chi tiết Đánh giá Sản phẩm
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div v-if="viewingReview" class="modal-body py-4">
                    <div class="row g-3">
                        <!-- Product info -->
                        <div class="col-12">
                            <div class="p-3 rounded bg-light border d-flex align-items-center gap-3">
                                <img
                                    :src="viewingReview.product?.image ? (viewingReview.product.image.startsWith('http') ? viewingReview.product.image : `/storage/${viewingReview.product.image}`) : '/images/defaults/record.jpg'"
                                    alt="product"
                                    class="rounded object-fit-cover"
                                    style="width: 56px; height: 56px"
                                />
                                <div>
                                    <div class="fw-bold text-dark">{{ viewingReview.product?.name || `Sản phẩm #${viewingReview.product_id}` }}</div>
                                    <small class="text-muted">ID sản phẩm: {{ viewingReview.product_id }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Customer & Meta -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="fw-bold text-muted text-uppercase fs-12 mb-2">Người gửi đánh giá</h6>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <img
                                        :src="viewingReview.user_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(viewingReview.user_name || 'User') + '&background=0284c7&color=fff'"
                                        alt="avatar"
                                        class="rounded-circle"
                                        style="width: 42px; height: 42px"
                                    />
                                    <div>
                                        <div class="fw-semibold">{{ viewingReview.user_name }}</div>
                                        <div class="text-muted text-sm">{{ viewingReview.user_email || 'Không có email' }}</div>
                                        <div v-if="viewingReview.user_id" class="text-primary text-xs">Tài khoản thành viên (#{{ viewingReview.user_id }})</div>
                                        <div v-else class="text-secondary text-xs">Khách vãng lai</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="fw-bold text-muted text-uppercase fs-12 mb-2">Thông tin đánh giá</h6>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="text-muted text-sm">Mức đánh giá:</span>
                                    <div class="text-warning">
                                        <i v-for="s in 5" :key="s" :class="s <= viewingReview.rating ? 'ri-star-fill' : 'ri-star-line'"></i>
                                    </div>
                                    <span class="fw-bold text-dark">({{ viewingReview.rating }}/5 sao)</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="text-muted text-sm">Trạng thái:</span>
                                    <span v-if="viewingReview.publish === 2" class="badge bg-success">Đã duyệt (Công khai)</span>
                                    <span v-else-if="viewingReview.publish === 1" class="badge bg-warning text-dark">Chờ duyệt</span>
                                    <span v-else class="badge bg-secondary">Đã ẩn / Vi phạm</span>
                                </div>
                                <div class="text-muted text-xs">
                                    Gửi lúc: {{ viewingReview.created_at }} ({{ viewingReview.created_at_human }})
                                </div>
                            </div>
                        </div>

                        <!-- Review comment -->
                        <div class="col-12">
                            <div class="border rounded p-3 bg-white">
                                <h6 class="fw-bold text-muted text-uppercase fs-12 mb-2">Nội dung nhận xét</h6>
                                <p class="mb-0 text-dark fs-14 line-height-lg" style="white-space: pre-wrap">
                                    {{ viewingReview.comment }}
                                </p>
                            </div>
                        </div>

                        <!-- Review images -->
                        <div v-if="viewingReview.images && viewingReview.images.length" class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="fw-bold text-muted text-uppercase fs-12 mb-2">Hình ảnh kèm theo ({{ viewingReview.images.length }})</h6>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <a
                                        v-for="(img, idx) in viewingReview.images"
                                        :key="idx"
                                        :href="img.startsWith('http') ? img : `/storage/${img}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="d-inline-block border rounded overflow-hidden shadow-sm"
                                    >
                                        <img
                                            :src="img.startsWith('http') ? img : `/storage/${img}`"
                                            alt="review-img"
                                            class="object-fit-cover"
                                            style="width: 100px; height: 100px"
                                        />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button
                        v-if="viewingReview && viewingReview.publish !== 2"
                        type="button"
                        class="btn btn-success d-flex align-items-center gap-1"
                        @click="handleQuickApproveModal(viewingReview)"
                    >
                        <i class="ri-check-line"></i> Duyệt công khai
                    </button>
                    <button
                        v-if="viewingReview && viewingReview.publish !== 0"
                        type="button"
                        class="btn btn-warning text-white d-flex align-items-center gap-1"
                        @click="handleQuickHideModal(viewingReview)"
                    >
                        <i class="ri-eye-off-line"></i> Ẩn đánh giá
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xóa 1 bản ghi -->
    <div id="deleteRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteRecordLabel" aria-hidden="true">
        <div class="modal-dialog zoomIn">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title text-danger uppercase fw-bold" id="deleteRecordLabel">Xác nhận xóa đánh giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">Bạn có chắc chắn muốn xóa đánh giá này?</h5>
                    <div v-if="selectedItem" class="mt-3">
                        <p class="text-muted fz-[12px] mb-1">Đánh giá của: <strong>{{ selectedItem.user_name }}</strong></p>
                        <div class="border rounded p-2 bg-light text-sm text-truncate text-muted">
                            "{{ selectedItem.comment }}"
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" />
                    <Button variant="delete" :loading="isLoading" label="Xóa đánh giá" :disabled="!selectedItem" @click="handleConfirmDeleteSingle" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xóa hàng loạt -->
    <div id="deleteMultipleRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteMultipleRecordLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg zoomIn">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title text-danger uppercase fw-bold" id="deleteMultipleRecordLabel">Xóa nhiều đánh giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">Bạn có chắc chắn muốn xóa {{ selectedIds.length }} đánh giá đã chọn?</h5>
                    <div class="mt-3">
                        <p class="text-muted fz-[12px] mb-2">Danh sách đánh giá được chọn:</p>
                        <div class="border rounded p-3 bg-light max-h-[250px] overflow-y-auto">
                            <ul class="list-unstyled mb-0">
                                <li v-for="item in data?.data?.data?.filter((i) => selectedIds.includes(i.id))" :key="item.id" class="py-1 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-medium text-dark text-sm">{{ item.user_name }} - {{ item.rating }} sao</span>
                                        <span class="text-muted text-xs text-truncate max-w-[400px]">{{ item.comment }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" />
                    <Button variant="delete" :loading="isLoading" label="Xác nhận xóa tất cả" :disabled="selectedIds.length === 0" @click="handleConfirmDeleteMultiple" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
