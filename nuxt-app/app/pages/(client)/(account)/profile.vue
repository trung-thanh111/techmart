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
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import Sidebar from "~/components/client/navigation/SidebarProfile.vue";
import ProfileHeader from "~/components/client/navigation/ProfileHeader.vue";

const breadcrumbs = [
  { label: 'Quản lý tài khoản', href: '/profile' },
];

definePageMeta({
  layout: "client",
  middleware: ["auth-required"],
});
</script>

<style scoped>
</style>


