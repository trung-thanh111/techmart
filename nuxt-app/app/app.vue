<template>
  <LoadingSkeleton v-if="$route.path === '/' && isLoading" />
  <NuxtLayout v-else>
    <NuxtPage />
  </NuxtLayout>
  <Toaster position="top-right" richColors closeButton :duration="2000" />
</template>

<script setup>
import LoadingSkeleton from "~/components/ui/LoadingSkeleton.vue";

const isLoading = ref(false);
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Inter", sans-serif;
  line-height: 1.6;
  overflow-x: hidden;
}

#app {
  min-height: 100vh;
}
</style>
