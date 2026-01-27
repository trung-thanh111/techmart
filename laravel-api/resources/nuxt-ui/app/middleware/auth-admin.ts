import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { TOLOGIN } from "~/utils/constants";

export default defineNuxtRouteMiddleware(() => {
    const toast = useAppToast();
    const authStore = useAuthStore();

    if (!authStore.isAuthenticated || !authStore.isAdmin) {
        if (toast) {
            toast.warning("Bảo mật", {
                description: "Bạn không có quyền truy cập trang quản trị.",
            });
        }
        return navigateTo(TOLOGIN);
    }
});
