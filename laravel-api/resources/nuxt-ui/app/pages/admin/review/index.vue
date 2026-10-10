<script setup lang="ts">
import ReviewIndexPage from "../product/review/index.vue";

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <ReviewIndexPage />
</template>
