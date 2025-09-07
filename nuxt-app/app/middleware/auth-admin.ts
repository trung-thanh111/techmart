import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TOHOME } from "~/utils/constants";

export default defineNuxtRouteMiddleware(() => {
  const toast = useAppToast();
  const authStore = useAuthStore();

  if (!authStore.isAdmin) {
    if (toast) {
      toast.warning("Quyền bảo mật", {
        description: "Bạn không có quyền truy cập trang.",
      });
    }
    return navigateTo(TOHOME);
  }
});
