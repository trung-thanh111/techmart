<template></template>

<script setup>
import { useAdminAssets } from "~/composables/admin/useAdminAssets";
// SEO meta
useSeoMeta({
  title: 'Techmart - Quản trị',
  description: 'Hệ thống quản trị Techmart - Quản lý sản phẩm, đơn hàng, khách hàng',
  ogTitle: 'Techmart - Quản trị',
  ogDescription: 'Hệ thống quản trị Techmart',
  ogType: 'website',
  robots: 'noindex, nofollow', // Ẩn khỏi search engines
})

const { loadAdminAssets, loadJS } = useAdminAssets();

onMounted(async () => {
  await loadAdminAssets();
});
</script>
