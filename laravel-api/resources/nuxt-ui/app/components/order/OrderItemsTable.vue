<script setup lang="ts">
import type { IOrderItem } from "~/interfaces/order/order.interface";
import { formatPrice } from "~/utils/order/order.helpers";

defineProps<{
    items?: IOrderItem[];
}>();

const config = useRuntimeConfig();

const selectedItem = ref<IOrderItem | null>(null);
const showDetailModal = ref(false);

const getImageUrl = (item: IOrderItem) => {
    if (item.productImage?.url) return item.productImage.url;
    if (item.productImage?.path) return `${config.public.baseUrl}/storage/${item.productImage.path}`;
    return IMAGEDEFAULT;
};

const formatAttributeValue = (val: any): string => {
    if (val === null || val === undefined) return "";
    if (typeof val === "object" && !Array.isArray(val)) {
        if (val.name) return val.name;
        if (val.value) return val.value;
        if (val.label) return val.label;

        return Object.entries(val)
            .filter(([k]) => k !== "id")
            .map(([_, v]) => v)
            .join(", ");
    }
    if (Array.isArray(val)) {
        return val.map((v) => formatAttributeValue(v)).join(", ");
    }
    return String(val);
};

const formatAttributes = (attrs?: Record<string, any>) => {
    if (!attrs || typeof attrs !== "object") return "";
    return Object.entries(attrs)
        .map(([key, val]) => `${key}: ${formatAttributeValue(val)}`)
        .join(", ");
};

const openDetail = (item: IOrderItem) => {
    selectedItem.value = item;
    showDetailModal.value = true;
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Sản phẩm ({{ items?.length || 0 }})</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%">Sản phẩm</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                            <th class="text-center" style="width: 50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!items?.length">
                            <td colspan="5" class="text-center text-muted py-4">Không có dữ liệu</td>
                        </tr>
                        <tr v-for="item in items" :key="item.id" class="cursor-pointer" @click="openDetail(item)">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <NuxtImg :src="getImageUrl(item)" :alt="item.productName" class="rounded object-fit-contain" width="60" height="60" loading="lazy" />
                                    <div>
                                        <h6 class="fs-13 mb-1 text-primary-hover">{{ item.productName }}</h6>
                                        <div class="vstack gap-1">
                                            <p v-if="item.productSku" class="text-muted fs-11 mb-0">SKU: {{ item.productSku }}</p>
                                            <p v-if="item.productAttributes" class="text-muted fs-11 mb-0">
                                                {{ formatAttributes(item.productAttributes) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-col align-items-center">
                                    <span class="fw-medium">{{ formatPrice(item.unitPrice || 0) }}</span>
                                    <del v-if="item.originalPrice && item.originalPrice > (item.unitPrice || 0)" class="text-muted fs-11">
                                        {{ formatPrice(item.originalPrice) }}
                                    </del>
                                </div>
                            </td>
                            <td class="text-center">{{ item.quantity || 1 }}</td>
                            <td class="text-end fw-semibold text-danger">
                                {{ formatPrice(item.subtotal || (item.unitPrice || 0) * (item.quantity || 1)) }}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-ghost-primary" @click.stop="openDetail(item)">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Modal -->
        <Dialog v-model:visible="showDetailModal" modal header="Chi tiết sản phẩm" :style="{ width: '800px', maxWidth: '90vw' }">
            <div v-if="selectedItem" class="row">
                <!-- Image -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="border rounded p-2 bg-light d-flex justify-content-center">
                        <NuxtImg :src="getImageUrl(selectedItem)" :alt="selectedItem.productName" class="img-fluid rounded object-fit-contain" style="max-height: 200px" />
                    </div>
                </div>

                <!-- Info -->
                <div class="col-md-8">
                    <h5 class="fw-bold mb-2 fs-16">{{ selectedItem.productName }}</h5>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted fs-12 d-block mb-1">SKU</label>
                            <span class="fw-medium">{{ selectedItem.productSku || "-" }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted fs-12 d-block mb-1">Bảo hành</label>
                            <span v-if="selectedItem.warrantyMonths" class="badge bg-info-subtle text-info">
                                {{ selectedItem.warrantyMonths }} tháng
                                <span v-if="selectedItem.warrantyExpiresAt" class="ms-1">(Hết hạn: {{ selectedItem.warrantyExpiresAt }})</span>
                            </span>
                            <span v-else class="text-muted">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted fs-12 d-block mb-1">Thuộc tính</label>
                            <span>{{ formatAttributes(selectedItem.productAttributes) || "-" }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted fs-12 d-block mb-1">Đổi trả</label>
                            <span :class="selectedItem.isReturnable ? 'text-success' : 'text-danger'">
                                <i :class="selectedItem.isReturnable ? 'ri-check-line' : 'ri-close-line'"></i>
                                {{ selectedItem.isReturnable ? `Cho phép (${selectedItem.returnDay} ngày)` : "Không cho phép" }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-3 border-dashed" />

                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Đơn giá:</span>
                            <span>{{ formatPrice(selectedItem.unitPrice || 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Số lượng:</span>
                            <span>x{{ selectedItem.quantity }}</span>
                        </div>
                        <div v-if="selectedItem.discountAmount" class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá:</span>
                            <span>-{{ formatPrice(selectedItem.discountAmount) }}</span>
                        </div>
                        <div v-if="selectedItem.taxAmount" class="d-flex justify-content-between mb-2">
                            <span>Thuế:</span>
                            <span>{{ formatPrice(selectedItem.taxAmount) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-dashed pt-2 mt-2">
                            <span class="fw-bold">Tổng tiền:</span>
                            <span class="fw-bold text-danger fs-16">{{ formatPrice(selectedItem.total || 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Đóng" severity="secondary" @click="showDetailModal = false" class="rounded text-xs mt-3" size="small" />
            </template>
        </Dialog>
    </div>
</template>
