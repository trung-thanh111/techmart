<template>
  <div v-if="loading && !configs" :class="['inline-flex items-center justify-center animate-pulse', className]">
    <div class="h-9 w-32 bg-gray-200/50 rounded"></div>
  </div>
  <NuxtLink v-else to="/" :class="['inline-flex items-center justify-center', className]">
    <NuxtImg
      v-if="resolvedLogo"
      :src="resolvedLogo"
      :alt="alt || companyName || 'Logo'"
      class="h-auto max-h-12 w-auto object-contain transition-opacity duration-300"
      format="webp"
      loading="lazy"
    />
    <span v-else class="text-xl font-bold tracking-tight text-white">
      {{ shortName || companyName || "" }}
    </span>
  </NuxtLink>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useSystemConfig } from "~/composables/system/useSystemConfig";

interface Props {
  variant?: "header" | "footer";
  className?: string;
  alt?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: "header",
  className: "",
  alt: "",
});

const { configs, loading, logo, companyName, shortName } = useSystemConfig();

const resolvedLogo = computed(() => {
  return logo.value || "/images/logo/logo-website.png";
});
</script>
