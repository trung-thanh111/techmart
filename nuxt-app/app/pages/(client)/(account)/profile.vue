<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <ProfileHeader />
    <section class="flex bg-white shadow-md rounded-lg">
      <Sidebar />
      <main class="flex-1 p-2 md:p-4 lg:p-8 mx-auto">
        <NuxtPage keepalive />
      </main>
    </section>
  </div>
</template>

<script setup lang="ts">
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Sidebar from "~/components/navigation/SidebarProfile.vue";
import ProfileHeader from "~/components/navigation/ProfileHeader.vue";
import { PROFILEINDEX } from "~/utils/constants";
import { useI18n } from "vue-i18n";
import { computed } from "vue";

const { t } = useI18n();
const breadcrumbs = computed(() => [{ label: t("profile.breadcrumb"), href: PROFILEINDEX }]);

definePageMeta({
  layout: "client",
  middleware: ["auth-required"],
});
</script>

<style scoped></style>
