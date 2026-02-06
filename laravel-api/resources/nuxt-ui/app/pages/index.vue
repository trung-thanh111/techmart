<template>
    <div></div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth/auth";
import { TOLOGIN, TODASHBOARD } from "~/utils/constants";

const authStore = useAuthStore();

if (!authStore.isAuthenticated) {
    navigateTo(TOLOGIN);
} else {
    navigateTo(TODASHBOARD);
}
</script>
