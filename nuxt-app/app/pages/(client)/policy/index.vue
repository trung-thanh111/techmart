<template>
  <div></div>
</template>

<script setup lang="ts">
import { navigateTo } from "#app";
import { TOPRIVACYPOLICY } from "~/utils/constants";

navigateTo(TOPRIVACYPOLICY, { replace: true });
</script>
