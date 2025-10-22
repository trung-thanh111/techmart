<template>
  <nav class="flex items-center text-xs md:text-sm bg-white rounded-md shadow-lg space-x-1 py-1 md:space-x-2 my-3" aria-label="Breadcrumb">
    <NuxtLink to="/" class="inline-flex mr-0 items-center px-2 py-1 rounded text-gray-500 hover:text-sky-500 transition">
      <svg class="md:w-4 md:h-4 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m10-11v10a1 1 0 01-1 1h-5" />
      </svg>
    </NuxtLink>
    <span v-if="crumbs && crumbs.length" class="text-gray-400 px-1 select-none mr-0 font-medium">/</span>
    <template v-for="(crumb, idx) in crumbs">
      <NuxtLink v-if="crumb.href && idx !== crumbs.length - 1" :to="crumb.href" :key="`link-${idx}`" class="inline-flex mr-0 items-center px-2 py-1 rounded text-gray-500 hover:text-sky-500 transition">
        {{ crumb.label }}
      </NuxtLink>
      <span v-else :key="`span-${idx}`" class="text-gray-700 font-medium px-1 py-1">
        {{ crumb.label }}
      </span>
      <span v-if="idx < crumbs.length - 1" :key="`divider-${idx}`" class="text-gray-400 px-1 select-none font-medium">/</span>
    </template>
  </nav>
</template>

<script setup lang="ts">
defineProps<{
  crumbs: Array<{ label: string; href?: string }>;
}>();
</script>
