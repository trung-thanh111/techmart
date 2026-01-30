import { useAuthStore } from "~/stores/auth/auth";
import { authService } from "~/services/auth/auth.service";

export default defineNuxtPlugin(async (nuxtApp) => {
  const auth = useAuthStore();

  // If store thinks we are authenticated
  if (auth.isAuthenticated) {
    try {
      const now = Date.now();
      if (auth.expireAt && now > auth.expireAt) {
        // Try refresh token if expired
        const refreshRes = await authService.refresh();
        // Update auth store with new tokens
        if (refreshRes?.data) {
          auth.setAuth(refreshRes.data.user, {
            accessToken: refreshRes.data.accessToken,
            refreshToken: null,
            csrfToken: refreshRes.data.csrfToken,
            expireAt: refreshRes.data.expireAt,
          });
        }
      }
    } catch (e: any) {
      console.error("Client Auth Restore Failed", e);
      auth.clearAuth();
    }
  }
});
