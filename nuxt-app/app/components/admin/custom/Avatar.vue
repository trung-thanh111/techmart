<script setup lang="ts">
const props = defineProps<{
  src: string;
  alt: string;
}>();
</script>

<template>
  <Avatar :src="props.src" :alt="props.alt" class="mr-2" size="large" shape="circle" />
</template>