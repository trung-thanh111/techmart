<script setup lang="ts">
import AvatarGroup from "primevue/avatargroup";

const props = defineProps<{
  avatars: { image?: string; label?: string }[];
  max?: number;
  shape?: "circle" | "square";
}>();

const displayAvatars = computed(() => {
  if (!props.max || props.avatars.length <= props.max) {
    return props.avatars;
  }
  return props.avatars.slice(0, props.max);
});

const hiddenCount = computed(() => {
  if (!props.max) return 0;
  return props.avatars.length > props.max ? props.avatars.length - props.max : 0;
});
</script>

<template>
  <AvatarGroup>
    <Avatar v-for="(item, idx) in displayAvatars" :key="idx" :image="item.image" :label="item.label" :shape="shape || 'circle'" />
    <Avatar v-if="hiddenCount > 0" :label="`+${hiddenCount}`" :shape="shape || 'circle'" />
  </AvatarGroup>
</template>
