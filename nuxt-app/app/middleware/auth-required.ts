import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TOLOGIN } from "~/utils/constants";

export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore();
  const toast = useAppToast();

  if (!authStore.isAuthenticated) {
    if (toast) {
      toast.warning("Bảo mật", {
        description: "Bạn cần đăng nhập để tiếp tục.",
      });
    }
    return navigateTo(TOLOGIN);
  }
});
