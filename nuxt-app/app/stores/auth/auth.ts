import { defineStore } from "pinia";
import piniaPersistedstate from "pinia-plugin-persistedstate";
import type { IAuthState, IUser, IJWTTokens } from "~/interfaces/user/user.interface";

const isAdminFromUser = (u: IUser | null) => (u ? u.userCatalogueId === 2 : false);

export const useAuthStore = defineStore("auth", {
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
      this.user = user;
      this.accessToken = tokens.accessToken;
      this.refreshToken = tokens.refreshToken ?? null;
      this.csrfToken = tokens.csrfToken;
      this.expireAt = tokens.expireAt;
      this.isAuthenticated = !!tokens.accessToken;
      this.isAdmin = isAdminFromUser(user);
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
