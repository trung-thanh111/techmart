<template></template>

<script setup>
import { useAdminAssets } from "~/composables/admin/useAdminAssets";

const { loadAdminAssets } = useAdminAssets();

onMounted(async () => {
  await loadAdminAssets();
});
</script>
