<script setup lang="ts">
import type { IOrder } from "~/interfaces/order/order.interface";
import { getCityName, getCityOptions, getWardOptions, getWardName } from "~/utils/location.helpers";
import { SHIPPING_METHOD_LABELS } from "~/utils/order/order.constants";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as z from "zod";

const props = defineProps<{
    order?: IOrder | null;
    editable?: boolean;
}>();

const emit = defineEmits<{
    (e: "update", payload: { shippingStreet?: string; shippingWard?: string; shippingCity?: string }): void;
}>();

const isEditing = ref(false);

const schema = toTypedSchema(
    z.object({
        shippingStreet: z.string().min(1, "Vui lòng nhập địa chỉ"),
        shippingCity: z.string().min(1, "Vui lòng chọn Tỉnh/Thành phố"),
        shippingWard: z.string().min(1, "Vui lòng chọn Phường/Xã/Thị Trấn"),
    }),
);

const { handleSubmit, errors, defineField, resetForm, setValues } = useForm({
    validationSchema: schema,
});

const [shippingStreet, shippingStreetAttrs] = defineField("shippingStreet");
const [shippingCity, shippingCityAttrs] = defineField("shippingCity");
const [shippingWard, shippingWardAttrs] = defineField("shippingWard");

// Options
const cityOptions = computed(() => getCityOptions());
const wardOptions = computed(() => getWardOptions(shippingCity.value));

// Init form
watch(
    () => props.order,
    (newOrder) => {
        if (newOrder) {
            // Pre-fill if needed, but we usually fill when opening modal
        }
    },
    { immediate: true },
);

const startEdit = () => {
    setValues({
        shippingStreet: props.order?.shippingStreet || "",
        shippingCity: props.order?.shippingCity || "",
        shippingWard: props.order?.shippingWard || "",
    });
    isEditing.value = true;
};

const closeEdit = () => {
    isEditing.value = false;
    resetForm();
};

const onSubmit = handleSubmit((values) => {
    emit("update", values);
    closeEdit();
});

// Format full address for display
const formatFullAddress = computed(() => {
    if (!props.order) return "";
    const parts = [props.order.shippingStreet, getWardName(props.order.shippingCity, props.order.shippingWard), getCityName(props.order.shippingCity)].filter(Boolean);
    return parts.join(", ");
});
const canEdit = computed(() => {
    if (!props.editable || !props.order?.status) return false;
    return ["pending", "confirmed", "processing"].includes(props.order.status);
});
</script>

<template>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fs-15 fw-semibold text-uppercase">Địa chỉ giao hàng</h6>
            <button v-if="canEdit" type="button" class="btn btn-sm btn-soft-primary" @click="startEdit">
                <i class="ri-edit-line"></i>
            </button>
        </div>
        <div class="card-body">
            <!-- View mode -->
            <div>
                <p class="mb-2 fw-medium">{{ formatFullAddress || "Chưa có địa chỉ" }}</p>
                <ul class="list-unstyled mb-0 vstack gap-1 text-muted fs-12">
                    <li v-if="order?.shippingMethod">
                        <i class="ri-truck-line me-1"></i>
                        {{ SHIPPING_METHOD_LABELS[order.shippingMethod] || order.shippingMethod }}
                        <span v-if="order.shippingProvider">({{ order.shippingProvider }})</span>
                    </li>
                    <li v-if="order?.trackingNumber">
                        <i class="ri-barcode-line me-1"></i>
                        Mã vận đơn:
                        <strong>{{ order.trackingNumber }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Edit Modal -->
        <Dialog v-model:visible="isEditing" modal header="Cập nhật địa chỉ" :style="{ width: '500px' }">
            <form @submit.prevent="onSubmit" class="vstack gap-3">
                <div class="d-flex flex-column gap-2">
                    <label class="form-label mb-0 required">Tỉnh/Thành phố</label>
                    <Select v-model="shippingCity" :options="cityOptions" optionLabel="label" optionValue="value" placeholder="Chọn Tỉnh/Thành phố" class="w-100 rounded py-[1px]" filter :class="{ 'p-invalid': errors.shippingCity }" v-bind="shippingCityAttrs" overlayClass="!z-[99999]" size="small" />
                    <small class="text-danger">{{ errors.shippingCity }}</small>
                </div>

                <div class="d-flex flex-column gap-2">
                    <label class="form-label mb-0 required">Phường/Xã/Thị Trấn</label>
                    <Select v-model="shippingWard" :options="wardOptions" optionLabel="label" optionValue="value" placeholder="Chọn Phường/Xã/Thị Trấn" class="w-100 rounded py-[1px]" filter checkmark :disabled="!shippingCity" :class="{ 'p-invalid': errors.shippingWard }" v-bind="shippingWardAttrs" overlayClass="!z-[99999]" size="small" />
                    <small class="text-danger">{{ errors.shippingWard }}</small>
                </div>

                <div class="d-flex flex-column gap-2">
                    <label class="form-label mb-0 required">Địa chỉ (Số nhà, đường)</label>
                    <textarea v-model="shippingStreet" rows="3" class="form-control" placeholder="Nhập số nhà, tên đường..." :class="{ 'is-invalid': errors.shippingStreet }" v-bind="shippingStreetAttrs" />
                    <small class="text-danger">{{ errors.shippingStreet }}</small>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <Button label="Hủy" severity="secondary" @click="closeEdit" class="rounded text-xs" size="small" />
                    <Button label="Lưu thay đổi" severity="success" type="submit" class="rounded text-xs" size="small" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
