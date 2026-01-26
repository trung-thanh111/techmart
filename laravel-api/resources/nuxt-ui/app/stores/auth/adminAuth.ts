import { defineStore } from "pinia";
import type { IAuthState, IUser, IJWTTokens } from "~/interfaces/user/user.interface";

export const useAdminAuthStore = defineStore("admin-auth", {
  state: (): IAuthState => ({
    user: null,
    accessToken: null,
    refreshToken: null,
    csrfToken: null,
    expireAt: null,
    isAuthenticated: false,
    isAdmin: false,
  }),

  actions: {
    setAuth(user: IUser, tokens: IJWTTokens) {
      if (!user.isAdmin) {
        // Safety check just in case backend slippage, though backend blocks it.
        throw new Error("Not an admin account");
      }
      this.user = user;
      this.accessToken = tokens.accessToken;
      this.refreshToken = tokens.refreshToken ?? null;
      this.csrfToken = tokens.csrfToken;
      this.expireAt = tokens.expireAt ? Date.now() + tokens.expireAt * 1000 : null;
      this.isAuthenticated = !!tokens.accessToken;
      this.isAdmin = true;
    },

    clearAuth() {
      this.user = null;
      this.accessToken = null;
      this.refreshToken = null;
      this.csrfToken = null;
      this.expireAt = null;
      this.isAuthenticated = false;
      this.isAdmin = false;
    },
  },

  persist: true,
});
