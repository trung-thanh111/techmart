import { useAuthStore } from "~/stores/auth/auth";
import { useAdminAuthStore } from "~/stores/auth/adminAuth";
import { TODASHBOARD, TOHOME } from "~/utils/constants";

export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore();
  const adminAuthStore = useAdminAuthStore();

  if (authStore.isAuthenticated) {
    return navigateTo(TOHOME);
  }

  if (adminAuthStore.isAuthenticated) {
    return navigateTo(TODASHBOARD);
  }
});
