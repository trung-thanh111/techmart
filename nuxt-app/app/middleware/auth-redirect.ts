import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TOHOME, TOLOGIN, TOREGISTER } from "~/utils/constants";

export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore();
  const toast = useAppToast();

  if (authStore.isAuthenticated && (to.path === TOLOGIN || to.path === TOREGISTER)) {
    if (toast) {
      toast?.message("Thông báo bảo mật", {
        description: "Bạn đã đăng nhập, chuyển hướng...",
      });
    }
    return navigateTo(TOHOME);
  }
});
