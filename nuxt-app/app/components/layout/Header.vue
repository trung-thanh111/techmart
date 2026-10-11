<template>
  <div class="container-header">
    <header class="w-full text-sm font-medium">
      <div class="hidden md:block bg-gradient-to-r from-[#00b88c] to-[#007c8b] py-2 text-white">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-2 text-sm px-4">
          <div class="flex items-center flex-wrap gap-4">
            <HotlineBadge
              :show-secondary="true"
              :show-label="true"
              :label="$t('header.hotlineLabel')"
              link-class="hover:text-gray-300 transition-colors"
            />
            <NuxtLink :to="TOAGENCY" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M3 9l1-5h16l1 5M5 22V12H19V22M2 9h20" /></svg>
              <span>{{ $t("header.dealerSystem") }}</span>
            </NuxtLink>
          </div>
          <div class="hidden md:flex items-center gap-4">
            <NuxtLink :to="TOFAQ" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4">
                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3l58.3 0c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24l0-13.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1l-58.3 0c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
              </svg>
              <span>{{ $t("header.faq") }}</span>
            </NuxtLink>
            <NuxtLink :to="TOPOLICY" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4">
                <path d="M256 80C149.9 80 62.4 159.4 49.6 262c9.4-3.8 19.6-6 30.4-6c26.5 0 48 21.5 48 48l0 128c0 26.5-21.5 48-48 48c-44.2 0-80-35.8-80-80l0-16 0-48 0-48C0 146.6 114.6 32 256 32s256 114.6 256 256l0 48 0 48 0 16c0 44.2-35.8 80-80 80c-26.5 0-48-21.5-48-48l0-128c0-26.5 21.5-48 48-48c10.8 0 21 2.1 30.4 6C449.6 159.4 362.1 80 256 80z" />
              </svg>
              <span>{{ $t("header.supportPolicy") }}</span>
            </NuxtLink>
          </div>
        </div>
      </div>

      <div class="hidden md:block w-full max-w-7xl mx-auto">
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
      </div>

      <!-- Main Header -->
      <div class="bg-gradient-to-r from-[#00b88c] to-[#007c8b] px-3 sm:px-4 pt-2 md:pt-3">
        <!-- Desktop Header (>= 768px) -->
        <div class="hidden md:flex max-w-7xl mx-auto items-center justify-between gap-6 pb-2">
          <div class="flex items-center gap-6">
            <AppLogo variant="header" class-name="h-15 w-40 flex-shrink-0" />
          </div>
          <div class="flex-1 max-w-xl relative">
            <SearchSuggestion />
          </div>
          <ClientOnly>
            <div class="items-center gap-4 flex text-white flex-shrink-0">
              <a href="#" @click.prevent="openTrackingDialog" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-7 h-7 mt-1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10,9 9,9 8,9" />
                </svg>
                <div class="font-light text-sm">
                  <p>{{ $t("header.track") }}</p>
                  <p>{{ $t("header.order") }}</p>
                </div>
              </a>
              <div ref="userMenuRef" class="relative inline-block text-left">
                <NuxtLink v-if="!user" :to="TOLOGIN" class="flex items-center gap-1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 mt-1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                  <div>
                    <span class="hover:underline">{{ $t("auth.login") }}</span>
                    <p class="font-light text-xs">{{ $t("nav.account") }}</p>
                  </div>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 mt-1.5"><polyline points="6,9 12,15 18,9" /></svg>
                </NuxtLink>
                <button v-else type="button" @click="toggleUserMenu" class="bg-transparent shadow-none focus:outline-none">
                  <span class="flex items-center gap-1">
                    <span class="text-right">
                      <span class="hidden xl:inline-block font-medium">{{ user?.name }}</span>
                      <span class="hidden xl:block font-normal text-[10px]">
                        {{ user?.userCatalogueId === 1 ? $t("header.customer") : $t("header.admin") }}
                      </span>
                    </span>
                    <NuxtImg class="rounded-full w-9 h-9 object-cover" :src="avatar" :alt="$t('common.avatar') + ' ' + user?.name" loading="lazy" />
                  </span>
                </button>
                <div v-show="isUserMenuOpen && user" class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 transition-all duration-300 ease-in-out">
                  <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
                    <div class="px-4 py-2 text-sm text-gray-500 font-semibold">{{ $t("header.hello", { name: user?.name }) }}</div>
                    <div class="py-1">
                      <NuxtLink :to="PROFILEINDEX" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2 cursor-pointer">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                          <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span>{{ $t("nav.info") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILEORDER" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                          <polyline points="14,2 14,8 20,8" />
                          <line x1="16" y1="13" x2="8" y2="13" />
                          <line x1="16" y1="17" x2="8" y2="17" />
                          <polyline points="10,9 9,9 8,9" />
                        </svg>
                        <span>{{ $t("sidebar.myOrders") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="TOWISHLIST" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-gray-400 cursor-pointer mr-2">
                          <path d="M462.3 62.6C407 7.6 324.8-10.6 256 47.9 187.2-10.6 105-7.6 49.7 62.6-16.6 145.3 23.8 268.7 130.4 357.1L239 469.6c9.4 9.4 24.6 9.4 33.9 0l108.6-112.5C488.2 268.7 528.6 145.3 462.3 62.6z" />
                        </svg>
                        <span>{{ $t("nav.wishlist") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILEADDRESSORDER" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                          <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span>{{ $t("sidebar.addressOrder") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILECHANGEPASSWORD" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                          <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <span>{{ $t("sidebar.changePassword") }}</span>
                      </NuxtLink>
                    </div>
                    <div class="py-1">
                      <button @click="handleLogout" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100 w-full text-left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mr-2">
                          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                          <polyline points="16,17 21,12 16,7" />
                          <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span>{{ $t("nav.logout") }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div ref="cartMenuRef" class="relative inline-block text-left group">
                <NuxtLink :to="TOCART" class="block relative text-white mt-2">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-1 min-w-[20px] text-center">
                    {{ cartCount }}
                  </span>
                </NuxtLink>
                <div class="absolute top-full right-0 w-full h-10 group-hover:block hidden z-10"></div>
                <div v-if="cartItems.length > 0" class="absolute right-0 top-12 z-30 min-w-[24rem] origin-top-right bg-white shadow-md rounded-lg hidden group-hover:block transition-all duration-300 ease-in-out">
                  <div class="w-full rounded-lg border-0 shadow-xl font-normal">
                    <div class="w-full max-w-md rounded-lg shadow-lg overflow-hidden text-sm">
                      <div class="px-4 py-2 bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-white font-medium rounded-t-lg">
                        {{ $t("action.quickView") }}
                      </div>
                      <div class="max-h-[300px] overflow-y-auto">
                        <div v-for="item in cartItems" :key="item.id" class="flex items-start px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                          <NuxtImg :src="getCartItemImage(item)" :alt="getCartItemName(item)" class="w-14 h-14 object-cover rounded mr-3 flex-shrink-0" loading="lazy" />
                          <div class="flex-1 min-w-0">
                            <NuxtLink :to="`/product/${item.product?.slug || ''}`" class="font-medium text-gray-800 text-sm line-clamp-2 hover:text-teal-600 line-clamp-custom-2">
                              {{ getCartItemName(item) }}
                            </NuxtLink>
                            <div class="flex items-center justify-between mt-1">
                              <span class="text-red-500 font-semibold text-sm">{{ formatPrice((item.price || 0) * (item.quantity || 0)) }}</span>
                              <span class="text-gray-400 text-xs">x{{ item.quantity }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100">
                        <p class="text-gray-500 text-sm">{{ $t("cart.itemsInCart", { count: cartCount }) }}</p>
                        <NuxtLink :to="TOCART" class="bg-[#0097cc] hover:bg-[#007c8b] text-white font-medium p-2 rounded text-xs transition-all duration-200">
                          {{ $t("action.viewCart") }}
                        </NuxtLink>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </ClientOnly>
        </div>

        <!-- Mobile / Tablet Header (< 768px) -->
        <div class="md:hidden max-w-7xl mx-auto">
          <!-- Row 1: Hamburger Menu + Logo on Left, Actions on Right -->
          <div class="flex items-center justify-between gap-2 py-1 text-white">
            <div class="flex items-center gap-2">
              <button @click="toggleMobileMenu" class="text-white p-1.5 -ml-1 rounded-md hover:bg-white/10 transition-colors" aria-label="Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                  <line x1="3" y1="6" x2="21" y2="6" />
                  <line x1="3" y1="12" x2="21" y2="12" />
                  <line x1="3" y1="18" x2="21" y2="18" />
                </svg>
              </button>
              <AppLogo variant="header" class-name="h-9 w-28 flex-shrink-0" />
            </div>

            <ClientOnly>
              <div class="flex items-center gap-2 sm:gap-3 text-white">
                <!-- Tra cứu đơn hàng -->
                <a href="#" @click.prevent="openTrackingDialog" class="flex items-center p-1.5 rounded-md hover:bg-white/10 transition-colors" :title="$t('header.trackOrder')">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14,2 14,8 20,8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10,9 9,9 8,9" />
                  </svg>
                </a>

                <!-- User / Auth -->
                <div ref="mobileUserMenuRef" class="relative inline-block text-left">
                  <NuxtLink v-if="!user" :to="TOLOGIN" class="flex items-center p-1.5 rounded-md hover:bg-white/10 transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                  </NuxtLink>
                  <button v-else type="button" @click="toggleMobileUserMenu" class="bg-transparent shadow-none focus:outline-none flex items-center p-0.5">
                    <NuxtImg class="rounded-full w-7 h-7 object-cover border border-white/40" :src="avatar" :alt="$t('common.avatar') + ' ' + user?.name" loading="lazy" />
                  </button>
                  <div v-show="isMobileUserMenuOpen && user" class="absolute right-0 z-30 mt-2 w-52 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5">
                    <div class="px-3 py-2 text-xs text-gray-500 font-semibold truncate">{{ $t("header.hello", { name: user?.name }) }}</div>
                    <div class="py-1 text-xs">
                      <NuxtLink :to="PROFILEINDEX" @click="isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                        <span>{{ $t("nav.info") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILEORDER" @click="isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14,2 14,8 20,8" /></svg>
                        <span>{{ $t("sidebar.myOrders") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="TOWISHLIST" @click="isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-gray-400 mr-2"><path d="M462.3 62.6C407 7.6 324.8-10.6 256 47.9 187.2-10.6 105-7.6 49.7 62.6-16.6 145.3 23.8 268.7 130.4 357.1L239 469.6c9.4 9.4 24.6 9.4 33.9 0l108.6-112.5C488.2 268.7 528.6 145.3 462.3 62.6z" /></svg>
                        <span>{{ $t("nav.wishlist") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILEADDRESSORDER" @click="isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>
                        <span>{{ $t("sidebar.addressOrder") }}</span>
                      </NuxtLink>
                      <NuxtLink :to="PROFILECHANGEPASSWORD" @click="isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                        <span>{{ $t("sidebar.changePassword") }}</span>
                      </NuxtLink>
                    </div>
                    <div class="py-1 text-xs">
                      <button @click="handleLogout(); isMobileUserMenuOpen = false" class="flex items-center px-3 py-2 text-red-700 hover:bg-red-50 w-full text-left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16,17 21,12 16,7" /><line x1="21" y1="12" x2="9" y2="12" /></svg>
                        <span>{{ $t("nav.logout") }}</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Giỏ hàng -->
                <NuxtLink :to="TOCART" class="relative p-1.5 rounded-md hover:bg-white/10 transition-colors text-white block">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-[10px] font-bold h-4 min-w-[16px] px-1 flex items-center justify-center leading-none shadow">
                    {{ cartCount }}
                  </span>
                </NuxtLink>
              </div>
            </ClientOnly>
          </div>

          <!-- Row 2: Search Suggestion Bar Full Width -->
          <div class="w-full pb-2">
            <SearchSuggestion />
          </div>
        </div>

        <div class="w-full max-w-7xl mx-auto">
          <div class="mt-4 md:mt-8 hidden md:grid grid-cols-2 md:grid-cols-5 text-white text-sm">
            <!-- menu -->
            <Menu :menu-tree="menuTree" />
            <NuxtLink :to="TOSHOP" class="flex items-center justify-center cursor-pointer h-10 px-3 rounded transition-all hover:bg-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 mt-0.5 text-white mr-2">
                <path d="M3 9L5 3h14l2 6" />
                <path d="M3 9h18" />
                <rect x="4" y="9" width="16" height="11" rx="2" />
                <path d="M10 20v-6h4v6" />
              </svg>
              <span>{{ $t("common.shop") }}</span>
            </NuxtLink>
            <NuxtLink :to="TOVOUCHER" class="flex items-center justify-center bg-gradient-to-r from-[#a8d444] to-[#b7e354] text-[#005a63] hover:opacity-90 cursor-pointer h-10 px-3 rounded transition-opacity">
              <svg viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mt-1 text-yellow-300 mr-1"><polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2" /></svg>
              <span>{{ $t("common.promotion") }}</span>
            </NuxtLink>
          </div>
        </div>
      </div>
    </header>
    <!-- mobile -->
    <MenuMobile :is-open="isMobileMenuOpen" :menu-tree="menuTree" @close="closeMobileMenu" />

    <Dialog v-model:visible="visibleTracking" :header="$t('header.trackOrder')" :modal="true" :style="{ width: '760px' }" :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
      <div class="flex flex-col gap-4 text-gray-700">
        <!-- Form Tra cứu -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                {{ $t('order.trackCode') }} <span class="text-red-500">*</span>
              </label>
              <InputText
                v-model="trackingCode"
                :placeholder="$t('order.trackCodePlaceholder')"
                class="w-full text-sm"
                @keydown.enter="trackOrder"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                {{ $t('order.trackPhone') }}
                <span v-if="!authStore.accessToken" class="text-red-500">*</span>
              </label>
              <InputText
                v-model="trackingPhone"
                :placeholder="authStore.accessToken ? $t('order.trackPhoneAuthPlaceholder') : $t('order.trackPhonePlaceholder')"
                class="w-full text-sm"
                @keydown.enter="trackOrder"
              />
            </div>
          </div>
          <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 border-t border-gray-200/70">
            <span class="text-xs text-gray-500 italic">
              {{ $t('order.trackSearchGuide') }}
            </span>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
              <Button
                v-if="trackedOrder"
                severity="secondary"
                outlined
                size="small"
                :label="$t('order.trackReset')"
                icon="pi pi-refresh"
                @click="resetTracking"
              />
              <Button
                size="small"
                icon="pi pi-search"
                :label="$t('order.trackSubmit')"
                :loading="trackingLoading"
                @click="trackOrder"
              />
            </div>
          </div>
        </div>

        <!-- Thông báo lỗi khi không tìm thấy đơn -->
        <div v-if="trackingError" class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start gap-2">
          <i class="pi pi-exclamation-circle text-red-500 text-base mt-0.5"></i>
          <span>{{ trackingError }}</span>
        </div>

        <!-- Kết quả tra cứu chi tiết đơn hàng -->
        <div v-if="trackedOrder" class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
          <!-- Header tóm tắt đơn -->
          <div class="bg-gray-100 p-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">{{ $t('order.code') }}:</span>
                <span class="font-bold text-gray-800 text-base">#{{ trackedOrder.code }}</span>
              </div>
              <div class="text-xs text-gray-500 mt-0.5">
                {{ $t('order.date') }}: {{ formatDate(trackedOrder.createdAt) }}
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <Tag :value="getStatusLabel(trackedOrder.status)" :severity="getStatusSeverity(trackedOrder.status)" />
              <Tag :value="getPaymentStatusLabel(trackedOrder.paymentStatus)" :severity="getPaymentStatusSeverity(trackedOrder.paymentStatus)" />
            </div>
          </div>

          <div class="p-4 space-y-4">
            <!-- Thông tin người nhận & Địa chỉ giao hàng -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-gray-50/80 rounded-lg text-xs">
              <div>
                <p class="font-bold text-gray-700 mb-1 uppercase tracking-wide flex items-center gap-1.5">
                  <i class="pi pi-user text-gray-500"></i> {{ $t('order.trackRecipient') }}
                </p>
                <p class="font-semibold text-gray-900 text-sm">{{ trackedOrder.customer?.name || trackedOrder.customerName || '-' }}</p>
                <p class="text-gray-600 mt-0.5">{{ trackedOrder.customer?.phone || trackedOrder.customerPhone || '-' }}</p>
                <p v-if="trackedOrder.customer?.email || trackedOrder.customerEmail" class="text-gray-500 mt-0.5">
                  {{ trackedOrder.customer?.email || trackedOrder.customerEmail }}
                </p>
              </div>
              <div>
                <p class="font-bold text-gray-700 mb-1 uppercase tracking-wide flex items-center gap-1.5">
                  <i class="pi pi-map-marker text-gray-500"></i> {{ $t('order.trackAddress') }}
                </p>
                <p class="text-gray-700 leading-relaxed font-medium">
                  {{ formatShippingAddress(trackedOrder) }}
                </p>
                <p v-if="trackedOrder.customerNote" class="text-gray-600 mt-1 italic">
                  <strong>{{ $t('order.trackNote') }}:</strong> {{ trackedOrder.customerNote }}
                </p>
              </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div>
              <p class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <i class="pi pi-box text-gray-500"></i>
                {{ $t('order.trackItems') }} ({{ trackedOrder.items?.length || 0 }})
              </p>
              <div class="divide-y divide-gray-100 max-h-56 overflow-y-auto pr-1">
                <div v-for="item in trackedOrder.items" :key="item.id" class="py-2.5 flex items-center gap-3">
                  <img
                    :src="item.productImage?.url || IMAGEDEFAULT"
                    :alt="item.productName"
                    loading="lazy"
                    class="w-12 h-12 object-contain rounded border border-gray-200 bg-white flex-shrink-0"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 line-clamp-1">{{ item.productName }}</p>
                    <p v-if="item.productSku" class="text-[11px] text-gray-400">SKU: {{ item.productSku }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                      {{ formatPrice(item.unitPrice) }} x {{ item.quantity }}
                    </p>
                  </div>
                  <div class="text-right flex-shrink-0">
                    <span class="text-xs font-bold text-gray-800">
                      {{ formatPrice(item.total || (item.unitPrice * item.quantity)) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bảng tổng hợp chi phí -->
            <div class="border-t border-gray-200 pt-3 space-y-1.5 text-xs text-gray-600">
              <div class="flex justify-between">
                <span>{{ $t('cart.subtotal') || 'Tạm tính' }}:</span>
                <span class="font-medium text-gray-800">{{ formatPrice(trackedOrder.subtotal) }}</span>
              </div>
              <div v-if="trackedOrder.shippingFee" class="flex justify-between">
                <span>{{ $t('cart.shipping') || 'Phí vận chuyển' }}:</span>
                <span class="font-medium text-gray-800">{{ formatPrice(trackedOrder.shippingFee) }}</span>
              </div>
              <div v-if="trackedOrder.discountAmount" class="flex justify-between text-green-600">
                <span>{{ $t('cart.discount') || 'Giảm giá' }}:</span>
                <span class="font-medium">-{{ formatPrice(trackedOrder.discountAmount) }}</span>
              </div>
              <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-gray-200">
                <span>{{ $t('cart.grandTotal') || 'Tổng thanh toán' }}:</span>
                <span class="text-base text-red-600 font-extrabold">{{ formatPrice(trackedOrder.totalAmount) }}</span>
              </div>
            </div>

            <!-- Nút liên kết cho tài khoản sở hữu đơn hàng -->
            <div v-if="authStore.accessToken && (trackedOrder.userId === authStore.user?.id || !trackedOrder.userId)" class="text-right pt-1">
              <NuxtLink
                :to="`${PROFILEORDER}/${trackedOrder.code}`"
                class="text-xs font-semibold text-[#00b88c] hover:underline inline-flex items-center gap-1"
                @click="visibleTracking = false"
              >
                {{ $t('order.trackViewDetail') }} <i class="pi pi-arrow-right text-[10px]"></i>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </Dialog>
  </div>

</template>
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

import { useI18n } from "vue-i18n";
import { onClickOutside } from "@vueuse/core";
import { TOLOGIN, TOAGENCY, TOFAQ, PROFILEINDEX, TOCART, TOPOLICY, TOWISHLIST, TOSHOP, PROFILEORDER, TOVOUCHER, PROFILECHANGEPASSWORD, PROFILEADDRESSORDER } from "~/utils/constants";
import AppLogo from "~/components/ui/AppLogo.vue";
import HotlineBadge from "~/components/ui/HotlineBadge.vue";
import { useAuthStore } from "~/stores/auth/auth";
import { useAuth } from "~/composables/auth/useAuth";
import { useCart } from "~/composables/cart/useCart";
import { useMenu } from "~/composables/menu/useMenu";
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { IMAGEDEFAULT } from "~/utils/constants";
import type { ICartItem } from "~/interfaces/cart/cartItem.interface";
import SearchSuggestion from "../features/SearchSuggestion.vue";
import Menu from "../navigation/Menu.vue";
import MenuMobile from "../navigation/MenuMobile.vue";

const { t } = useI18n();

import { orderService } from "~/services/order/order.service";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Tag from "primevue/tag";

// Auth
const authStore = useAuthStore();
const user = computed(() => authStore.user);
const avatar = computed(() => {
  const img = user.value?.image;
  if (img && img.path) return img.url || DEFAULTAVATAR;
  return DEFAULTAVATAR;
});

const { handleLogout } = useAuth();
const { count: cartCount, items: cartItems, loadCart } = useCart();
const { menuTree, loading: menuLoading, loadMenuBySlug } = useMenu();

const getCartItemImage = (item: ICartItem): string => {
  if (item.product?.album) {
    const images = resolveAlbum(item.product.album);
    return images[0] || IMAGEDEFAULT;
  }
  return IMAGEDEFAULT;
};

const getCartItemName = (item: ICartItem): string => {
  const productName = item.product?.name || "";
  const variantName = item.variant?.name || "";
  return variantName ? `${productName} - ${variantName}` : productName;
};

const isMobileMenuOpen = ref(false);
const subMenuOpen = ref<string | null>(null);
const productMenuOpen = ref<string | null>(null);

const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);
const isCartMenuOpen = ref(false);
const cartMenuRef = ref(null);

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
  if (isUserMenuOpen.value) isCartMenuOpen.value = false;
};

const closeUserMenu = () => {
  isUserMenuOpen.value = false;
};

const toggleCartMenu = () => {
  isCartMenuOpen.value = !isCartMenuOpen.value;
  if (isCartMenuOpen.value) isUserMenuOpen.value = false;
};

const closeCartMenu = () => {
  isCartMenuOpen.value = false;
};

onClickOutside(userMenuRef, closeUserMenu);
onClickOutside(cartMenuRef, closeCartMenu);

const isMobileUserMenuOpen = ref(false);
const mobileUserMenuRef = ref(null);

const toggleMobileUserMenu = () => {
  isMobileUserMenuOpen.value = !isMobileUserMenuOpen.value;
};

const closeMobileUserMenu = () => {
  isMobileUserMenuOpen.value = false;
};

onClickOutside(mobileUserMenuRef, closeMobileUserMenu);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  document.body.style.overflow = isMobileMenuOpen.value ? "hidden" : "";
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
  subMenuOpen.value = null;
  productMenuOpen.value = null;
  document.body.style.overflow = "";
};

onMounted(async () => {
  await loadCart();
  await loadMenuBySlug("main-menu");
});

// Order Tracking Logic
const visibleTracking = ref(false);
const trackingCode = ref("");
const trackingPhone = ref("");
const trackedOrder = ref<any>(null);
const trackingLoading = ref(false);
const trackingError = ref("");

const openTrackingDialog = () => {
  visibleTracking.value = true;
  trackingCode.value = "";
  trackingPhone.value = authStore.user?.phone || "";
  trackedOrder.value = null;
  trackingError.value = "";
};

const resetTracking = () => {
  trackingCode.value = "";
  trackingPhone.value = authStore.user?.phone || "";
  trackedOrder.value = null;
  trackingError.value = "";
};

const formatShippingAddress = (order: any) => {
  if (!order) return "-";
  if (order.shippingAddress) return order.shippingAddress;
  const parts = [order.shippingStreet, order.shippingWard, order.shippingCity].filter(Boolean);
  return parts.length > 0 ? parts.join(", ") : "-";
};

const trackOrder = async () => {
  const code = trackingCode.value.trim();
  const phone = trackingPhone.value.trim();

  if (!code) {
    trackingError.value = t("order.trackRequired") || "Vui lòng nhập mã đơn hàng.";
    return;
  }

  if (!authStore.accessToken && !phone) {
    trackingError.value = t("order.trackRequired") || "Vui lòng nhập số điện thoại hoặc email nhận hàng.";
    return;
  }

  trackingLoading.value = true;
  trackingError.value = "";
  try {
    const payload: { code: string; phone?: string; email?: string } = { code };
    if (phone) {
      if (phone.includes("@")) {
        payload.email = phone;
      } else {
        payload.phone = phone;
      }
    }

    const response = await orderService.track(payload);
    if (response?.data) {
      trackedOrder.value = response.data;
    } else {
      trackedOrder.value = null;
      trackingError.value = t("order.trackNotFound") || "Không tìm thấy thông tin đơn hàng.";
    }
  } catch (error: any) {
    trackedOrder.value = null;
    const msg = error?.response?._data?.message || error?.message;
    trackingError.value = t("order.trackNotFound") || msg || "Không tìm thấy thông tin đơn hàng.";
  } finally {
    trackingLoading.value = false;
  }
};


const formatDate = (dateString?: string) => {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
};

const getStatusSeverity = (status: string) => {
  switch (status) {
    case "pending":
      return "warn";
    case "confirmed":
      return "info";
    case "processing":
      return "info";
    case "shipped":
      return "secondary";
    case "delivered":
      return "success";
    case "cancelled":
      return "danger";
    case "refunded":
      return "warn";
    case "failed":
      return "danger";
    default:
      return "secondary";
  }
};

const getPaymentStatusSeverity = (status: string) => {
  switch (status) {
    case "paid":
      return "success";
    case "unpaid":
      return "warn";
    case "refunded":
      return "info";
    default:
      return "secondary";
  }
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: t("order.status.pending") || "Chờ xử lý",
    confirmed: t("order.status.confirmed") || "Đã xác nhận",
    processing: t("order.status.processing") || "Đang xử lý",
    shipped: t("order.status.shipped") || "Đang giao",
    delivered: t("order.status.delivered") || "Đã giao",
    cancelled: t("order.status.cancelled") || "Đã hủy",
    refunded: t("order.status.refunded") || "Đã hoàn tiền",
    failed: t("order.status.failed") || "Thất bại",
  };
  return labels[status] || status;
};

const getPaymentStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    paid: t("payment.paid") || "Đã thanh toán",
    unpaid: t("payment.unpaid") || "Chưa thanh toán",
    refunded: t("payment.refunded") || "Đã hoàn tiền",
  };
  return labels[status] || status;
};
</script>
<style scoped>
.line-clamp-custom-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  -webkit-line-clamp: 2;
  line-clamp: 2;
}
</style>
