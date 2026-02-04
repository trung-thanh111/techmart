<script setup lang="ts">
import { PAYMENT_METHOD_LABELS } from "~/utils/order/order.constants";

interface Props {
    method?: string;
    size?: "sm" | "md" | "lg";
}

const props = withDefaults(defineProps<Props>(), {
    size: "md",
});

const PAYMENT_ICONS: Record<string, string> = {
    cod: "/images/logo/cod.png",
    bank_transfer: "/images/logo/credit-card.png",
    credit_card: "/images/logo/credit-card.png",
    momo: "/images/logo/momo.svg",
    vnpay: "/images/logo/vnpay.webp",
    zalopay: "/images/logo/vnpay.webp",
};

const iconSrc = computed(() => PAYMENT_ICONS[props.method || ""] || PAYMENT_ICONS.cod);
const label = computed(() => PAYMENT_METHOD_LABELS[props.method || ""] || props.method || "N/A");

const sizeClass = computed(() => {
    switch (props.size) {
        case "sm":
            return "w-6 h-6";
        case "lg":
            return "w-10 h-10";
        default:
            return "w-8 h-8";
    }
});
</script>

<template>
    <div class="inline-flex items-center justify-center" :title="label">
        <NuxtImg :src="iconSrc" :alt="label" :class="sizeClass" class="object-contain" loading="lazy" />
    </div>
</template>
