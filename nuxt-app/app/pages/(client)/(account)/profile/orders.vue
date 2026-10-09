<template>
  <div></div>
</template>

<script setup lang="ts">
import { PROFILEORDER } from "~/utils/constants";

await navigateTo(PROFILEORDER, { replace: true, redirectCode: 301 });
</script>
