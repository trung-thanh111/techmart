<script setup lang="ts">
const props = defineProps<{
  crumbs: {
    label: string;
    to?: string;
  }[];
}>();
</script>

<template>
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item">
      <NuxtLink to="/admin/dashboard">Dashboard</NuxtLink>
    </li>

    <li v-for="(crumb, index) in props.crumbs" :key="index" class="breadcrumb-item" :class="{ active: index === props.crumbs.length - 1 }">
      <template v-if="crumb.to && index !== props.crumbs.length - 1">
        <NuxtLink :to="crumb.to">{{ crumb.label }}</NuxtLink>
      </template>
      <template v-else>
        {{ crumb.label }}
      </template>
    </li>
  </ol>
</template>
