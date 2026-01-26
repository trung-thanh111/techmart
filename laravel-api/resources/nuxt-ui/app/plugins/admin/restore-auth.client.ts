import { useAdminAuthStore } from "~/stores/auth/adminAuth";
import { adminAuthService } from "~/services/auth/admin.auth.service";

export default defineNuxtPlugin(async (nuxtApp) => {
  const auth = useAdminAuthStore();

  if (auth.isAuthenticated) {
    try {
      const now = Date.now();
      if (auth.expireAt && now > auth.expireAt) {
        await adminAuthService.refresh();
      } else {
        // Token looks valid, verify with backend
        const res = await adminAuthService.me();
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
