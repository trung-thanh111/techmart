import { useAdminAuthStore } from "~/stores/auth/adminAuth";
import { useAppToast } from "~/composables/useAppToast";

export default defineNuxtRouteMiddleware(() => {
  const toast = useAppToast();
  const authStore = useAdminAuthStore();

  if (!authStore.isAuthenticated || !authStore.isAdmin) {
    if (toast) {
      toast.warning("Bảo mật", {
        description: "Bạn không có quyền truy cập trang quản trị.",
      });
    }
    return navigateTo(TOHOME);
  }
});
