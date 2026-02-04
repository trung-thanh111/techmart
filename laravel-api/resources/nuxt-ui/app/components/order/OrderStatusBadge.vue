<script setup lang="ts">
import { getStatusClass } from "~/utils/order/order.helpers";
import { ORDER_STATUS_LABELS } from "~/utils/order/order.constants";

const props = defineProps<{
    status?: string;
    size?: "sm" | "md" | "lg";
}>();

const label = computed(() => ORDER_STATUS_LABELS[props.status || ""] || props.status || "-");
const badgeClass = computed(() => getStatusClass(props.status));
const sizeClass = computed(() => {
    const sizes = {
        sm: "fs-11",
        md: "fs-12",
        lg: "fs-13",
    };
    return sizes[props.size || "md"];
});
</script>

<template>
    <span :class="['badge', badgeClass, sizeClass]">{{ label }}</span>
</template>
