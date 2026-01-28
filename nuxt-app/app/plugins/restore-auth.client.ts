import { useAuthStore } from "~/stores/auth/auth";
import { authService } from "~/services/auth/auth.service";

export default defineNuxtPlugin(async (nuxtApp) => {
  const auth = useAuthStore();

  // If store thinks we are authenticated
  if (auth.isAuthenticated) {
    try {
      // Attempt to verify/refresh token.
      // If cookies are missing, this call should fail (401).
      // authService.me() or authService.refresh()
      // Let's use refresh if expired, or me if not sure.
      // Usually 'me' is safer to check validity.
      // If access token is in store but cookie missing,
      // 'me' call will use the store token header?
      // useApi: "headers.set("Authorization", ...accessToken)"
      // So if store has token, it sends it.
      // If token is expired, backend returns 401.
      // If token is valid but cookie missing?
      // The backend usually expects cookie for refresh, but access token might be enough for 'me'.

      // However, user says "cookie expired".
      // If cookie is httpOnly refresh token, and it is gone.
      // And access token in store is expired.
      // Then verify logic:

      const now = Date.now();
      if (auth.expireAt && now > auth.expireAt) {
        // Try refresh
        await authService.refresh();
      } else {
        // Token looks valid, verify with backend
        const res = await authService.me();
        if (res.data && res.data.user) {
          auth.user = res.data.user;
        }
      }
    } catch (e: any) {
      // If 401, clear auth
      // useApi interceptor might redirect to login, but we also want to clear store state
      // so header updates.
      console.error("Client Auth Restore Failed", e);
      auth.clearAuth();
    }
  }
});
