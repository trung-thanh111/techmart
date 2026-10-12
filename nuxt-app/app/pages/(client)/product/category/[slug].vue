<template>
  <div class="p-8 text-center text-gray-500">
    <p>Đang chuyển hướng...</p>
  </div>
</template>

<script setup lang="ts">
import { useRoute, navigateTo, definePageMeta } from "#imports";

const route = useRoute();
const targetSlug = (route.params.slug as string) || "";

await navigateTo(
  {
    path: `/category/${targetSlug}`,
    query: route.query,
  },
  { redirectCode: 301, replace: true }
);

definePageMeta({
  layout: "client",
});
</script>
