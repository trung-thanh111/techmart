import type { RouterConfig } from "@nuxt/schema";
import { PROFILEINDEX } from "~/utils/constants";

export default <RouterConfig>{
  scrollBehavior(to, from, savedPosition) {
    // Nếu có savedPosition (back/forward), dùng nó
    if (savedPosition) return savedPosition;

    // Nếu cùng route (chỉ hash thay đổi), không scroll
    if (to.path === from?.path && to.hash) {
      return { el: to.hash, behavior: "smooth" };
    }

    // Nếu cùng route (không có hash), giữ nguyên scroll position
    if (to.path === from?.path) {
      return false;
    }

    // Account area: giữ nguyên scroll
    const isAccountArea = to.path.startsWith(PROFILEINDEX) && from?.path?.startsWith(PROFILEINDEX);
    if (isAccountArea) return false;

    return false;
  },
};
