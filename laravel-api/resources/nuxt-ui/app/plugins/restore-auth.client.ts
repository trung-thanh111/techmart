import { useAuthStore } from "~/stores/auth/auth";
import { authService } from "~/services/auth/auth.service";

export default defineNuxtPlugin(async (nuxtApp) => {
    const auth = useAuthStore();

    if (auth.isAuthenticated) {
        try {
            const now = Date.now();
            if (auth.expireAt && now > auth.expireAt) {
                // Token expired, try to refresh
                const res = await authService.refresh();
                if (res.data) {
                    auth.setAuth(res.data.user, {
                        accessToken: res.data.accessToken,
                        refreshToken: null,
                        csrfToken: res.data.csrfToken,
                        expireAt: res.data.expireAt,
                    });
                }
            } else {
                const res = await authService.me();
                if (res.data) {
                    if (!res.data.isAdmin) {
                        throw new Error("User is not admin");
                    }
                    auth.user = res.data;
                    auth.isAdmin = res.data.isAdmin;
                }
            }
        } catch (e: any) {
            console.error("Admin Auth Restore Failed", e);
            auth.clearAuth();
        }
    }
});
