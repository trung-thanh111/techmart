<script setup lang="ts">
const props = defineProps<{
  field: string;
  mask: string;
  placeholder: string;
}>();
</script>

<template>
  <InputMask id="basic" v-model="field" :field="field" :mask="mask" :placeholder="placeholder" />
</template>
