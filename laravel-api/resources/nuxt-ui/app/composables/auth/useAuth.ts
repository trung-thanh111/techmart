import { authService } from "~/services/auth/auth.service";
import { useAuthStore } from "~/stores/auth/auth";
import { TOLOGIN } from "~/utils/constants";

export const useAuth = () => {
    const auth = useAuthStore();

    const getMe = async () => {
        try {
            const res = await authService.me();
            auth.setAuth(res.data.user, {
                accessToken: res.data.accessToken,
                refreshToken: null,
                csrfToken: res.data.csrfToken,
                expireAt: res.data.expireAt,
            });
            return res.data.user;
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
