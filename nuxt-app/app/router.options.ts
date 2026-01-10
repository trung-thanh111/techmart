import type { RouterConfig } from "@nuxt/schema";
import { PROFILEINDEX } from "~/utils/constants";

export default <RouterConfig>{
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition;
    const isAccountArea = to.path.startsWith(PROFILEINDEX) && from?.path?.startsWith(PROFILEINDEX);
    if (isAccountArea) return false;
    return { top: 0 };
  },
};
