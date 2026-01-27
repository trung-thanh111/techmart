import { useAuthStore } from "~/stores/auth/auth";
import { authService } from "~/services/auth/auth.service";

export default defineNuxtPlugin(async (nuxtApp) => {
    const auth = useAuthStore();

    if (auth.isAuthenticated) {
        try {
            const now = Date.now();
            if (auth.expireAt && now > auth.expireAt) {
                await authService.refresh();
            } else {
                // Token looks valid, verify with backend
                const res = await authService.me();
                if (res.data && res.data.user) {
                    // Ensure the user returned is actually an admin
                    if (!res.data.user.isAdmin) {
                        throw new Error("User is not admin");
                    }
                    auth.user = res.data.user;
                }
            }
        } catch (e: any) {
            console.error("Admin Auth Restore Failed", e);
            auth.clearAuth();
        }
    }
});
