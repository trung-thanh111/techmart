<script setup lang="ts">
import type { IOrder } from "~/interfaces/order/order.interface";
import Button from "~/components/custom/Button.vue";

const props = defineProps<{
    order?: IOrder | null;
}>();

const emit = defineEmits<{
    (e: "updateAdminNote", note: string): void;
}>();

const isEditing = ref(false);
const adminNote = ref("");

const startEdit = () => {
    adminNote.value = props.order?.adminNote || "";
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
};

const saveNote = () => {
    emit("updateAdminNote", adminNote.value);
    isEditing.value = false;
};
</script>

<template>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Ghi chú</h6>
            <button v-if="!isEditing" type="button" class="btn btn-sm btn-soft-primary" @click="startEdit">
                <i class="ri-edit-line"></i>
            </button>
        </div>
        <div class="card-body">
            <div v-if="order?.customerNote" class="mb-3">
                <label class="form-label fs-12 text-muted mb-1">
                    <i class="ri-user-line me-1"></i>
                    Ghi chú từ khách hàng
                </label>
                <div class="p-2 bg-light-subtle border rounded fs-13">
                    {{ order.customerNote }}
                </div>
            </div>

            <div>
                <label class="form-label fs-12 text-muted mb-1">
                    <i class="ri-admin-line me-1"></i>
                    Ghi chú nội bộ
                </label>

                <div v-if="!isEditing">
                    <div v-if="order?.adminNote" class="p-2 bg-gray-subtle border border-gray rounded fs-13">
                        {{ order.adminNote }}
                    </div>
                    <p v-else class="text-muted fs-13 mb-0">Chưa có ghi chú</p>
                </div>

                <div v-else>
                    <textarea v-model="adminNote" class="form-control form-control-sm" rows="3" placeholder="Nhập ghi chú nội bộ..."></textarea>
                    <div class="d-flex gap-2 mt-2">
                        <Button label="Lưu" severity="success" size="small" icon="ri-save-line" @click="saveNote" class="rounded text-xs" />
                        <Button label="Hủy" severity="secondary" size="small" @click="cancelEdit" class="rounded text-xs" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
