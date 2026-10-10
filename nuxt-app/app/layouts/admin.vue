<template>
  <div class="min-h-screen bg-gray-100 flex flex-col text-gray-800">
    <!-- Admin Topbar -->
    <header class="bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-white shadow-md sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <NuxtLink to="/admin/system" class="flex items-center gap-2 font-bold text-lg tracking-wide hover:opacity-90">
            <i class="pi pi-shield text-xl text-yellow-300"></i>
            <span>TechMart Admin</span>
          </NuxtLink>
          <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-mono">v1.0</span>
        </div>

        <div class="flex items-center gap-4">
          <NuxtLink
            to="/"
            class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors"
          >
            <i class="pi pi-external-link text-xs"></i>
            <span>Xem website</span>
          </NuxtLink>

          <div class="flex items-center gap-2 pl-2 border-l border-white/20">
            <span class="text-sm font-medium hidden sm:inline">{{ authStore.user?.name || "Admin" }}</span>
            <button
              @click="handleLogout"
              class="text-xs bg-red-600 hover:bg-red-700 text-white px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1"
              title="Đăng xuất"
            >
              <i class="pi pi-sign-out"></i>
              <span class="hidden sm:inline">Đăng xuất</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Admin Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth/auth";
import { useAuth } from "~/composables/auth/useAuth";

const authStore = useAuthStore();
const { handleLogout } = useAuth();
</script>
