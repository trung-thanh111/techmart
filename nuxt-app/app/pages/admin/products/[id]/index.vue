<template>
  <div>Admin Product Detail</div>
  <slot />
</template>
<script setup>
definePageMeta({
  layout: 'admin',
  middleware: ['auth-required', 'auth-admin']
})
</script>

