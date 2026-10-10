<template>
  <div v-if="loading && !configs" :class="wrapperClass || 'flex items-center gap-3 animate-pulse'">
    <div v-for="i in 3" :key="i" class="w-6 h-6 rounded-full bg-gray-200"></div>
  </div>
  <div v-else-if="activeSocials.length > 0" :class="wrapperClass || 'flex items-center gap-3'">
    <a
      v-for="item in activeSocials"
      :key="item.name"
      :href="item.url"
      target="_blank"
      rel="noopener noreferrer"
      :title="item.title"
      :class="itemClass || 'hover:opacity-80 transition-opacity'"
    >
      <i :class="[item.icon, iconClass || 'text-xl']"></i>
      <span v-if="showTitle" class="ml-1 text-xs">{{ item.title }}</span>
    </a>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useSystemConfig } from "~/composables/system/useSystemConfig";

interface Props {
  wrapperClass?: string;
  itemClass?: string;
  iconClass?: string;
  showTitle?: boolean;
}

withDefaults(defineProps<Props>(), {
  wrapperClass: "",
  itemClass: "",
  iconClass: "",
  showTitle: false,
});

const { configs, loading, facebook, youtube, tiktok, zalo, messenger, instagram } = useSystemConfig();

const activeSocials = computed(() => {
  const list = [
    { name: "facebook", title: "Facebook", icon: "fab fa-facebook", url: facebook.value },
    { name: "youtube", title: "YouTube", icon: "fab fa-youtube", url: youtube.value },
    { name: "tiktok", title: "TikTok", icon: "fab fa-tiktok", url: tiktok.value },
    { name: "instagram", title: "Instagram", icon: "fab fa-instagram", url: instagram.value },
    { name: "messenger", title: "Messenger", icon: "fab fa-facebook-messenger", url: messenger.value },
    { name: "zalo", title: "Zalo", icon: "fas fa-comment-dots", url: zalo.value },
  ];
  return list.filter((s) => Boolean(s.url && s.url.trim()));
});
</script>
