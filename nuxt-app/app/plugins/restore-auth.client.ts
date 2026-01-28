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
        await authService.refresh();
      }
    } catch (e: any) {
      console.error("Client Auth Restore Failed", e);
      auth.clearAuth();
    }
  }
});
