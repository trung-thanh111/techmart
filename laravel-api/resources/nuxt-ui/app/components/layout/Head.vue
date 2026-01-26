<template></template>

<script setup>
import { useAdminAssets } from "~/composables/useAdminAssets";

const { loadAdminAssets } = useAdminAssets();

onMounted(async () => {
    await loadAdminAssets();
});
</script>
