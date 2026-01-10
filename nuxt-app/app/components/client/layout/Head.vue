<template></template>

<script setup lang="ts">
import { useSeo } from "~/composables/useSeo";

const { setClientSeo } = useSeo();

// SEO
setClientSeo();
</script>
