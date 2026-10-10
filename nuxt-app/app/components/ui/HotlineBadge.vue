<template>
  <div v-if="loading && !configs" class="inline-flex items-center gap-1.5 text-sm animate-pulse">
    <div v-if="showIcon" :class="[iconClass || 'w-4 h-4', 'bg-gray-200 rounded-full']"></div>
    <span v-if="showLabel" class="h-3 w-12 bg-gray-200 rounded"></span>
    <span class="h-4 w-24 bg-gray-200 rounded"></span>
  </div>
  <div v-else-if="hotline" class="inline-flex items-center gap-1.5 text-sm">
    <slot name="icon">
      <svg
        v-if="showIcon"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        :class="iconClass || 'w-4 h-4'"
      >
        <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.72 19.72 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 1.72c.13 1.05.37 2.07.72 3.06a2 2 0 0 1-.45 2.11l-1.27 1.27a16 16 0 0 0 6.29 6.29l1.27-1.27a2 2 0 0 1 2.11-.45c.99.35 2.01.59 3.06.72A2 2 0 0 1 22 16.92z" />
      </svg>
    </slot>

    <span v-if="showLabel" :class="labelClass">{{ label || "Hotline:" }}</span>

    <a
      :href="`tel:${cleanPhone(hotline)}`"
      :class="linkClass || 'hover:underline transition-colors font-semibold'"
    >
      {{ hotline }}
    </a>

    <template v-if="showSecondary && hotlineSecondary">
      <span class="opacity-60">-</span>
      <a
        :href="`tel:${cleanPhone(hotlineSecondary)}`"
        :class="linkClass || 'hover:underline transition-colors font-semibold'"
      >
        {{ hotlineSecondary }}
      </a>
    </template>
  </div>
</template>

<script setup lang="ts">
import { useSystemConfig } from "~/composables/system/useSystemConfig";

interface Props {
  showIcon?: boolean;
  showSecondary?: boolean;
  showLabel?: boolean;
  label?: string;
  iconClass?: string;
  labelClass?: string;
  linkClass?: string;
}

withDefaults(defineProps<Props>(), {
  showIcon: true,
  showSecondary: false,
  showLabel: false,
  label: "",
  iconClass: "",
  labelClass: "",
  linkClass: "",
});

const { configs, loading, hotline, hotlineSecondary } = useSystemConfig();

const cleanPhone = (phone: string) => {
  return phone.replace(/[^\d+]/g, "");
};
</script>
