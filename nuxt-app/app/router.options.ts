import type { RouterConfig } from '@nuxt/schema'

export default <RouterConfig>{
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    const isAccountArea = to.path.startsWith('/profile') && from?.path?.startsWith('/profile')
    if (isAccountArea) return false
    return { top: 0 }
  }
}


