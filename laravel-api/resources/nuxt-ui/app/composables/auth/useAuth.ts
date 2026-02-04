import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { TOLOGIN } from "~/utils/constants";

export const useAuth = () => {
    const auth = useAuthStore();

    const getMe = async () => {
        try {
            const res = await authService.me();
            // /me endpoint only returns user data, not tokens
            // Update user info in store without changing tokens
            if (res.data) {
                auth.user = res.data;
                auth.isAdmin = res.data.isAdmin ?? false;
            }
            return res.data;
        } catch (e) {
            // nếu 401 sẽ bị interceptor trả lỗi
            console.error("getMe failed:", e);
        }
    };

    const handleLogout = async () => {
        try {
            await authService.logout();
        } catch (err) {
        } finally {
            auth.clearAuth();
            await navigateTo(TOLOGIN);
        }
    };

    return {
        handleLogout,
        getMe,
        isAuthenticated: computed(() => auth.isAuthenticated),
        isAdmin: computed(() => auth.isAdmin),
        user: computed(() => auth.user),
    };
};
