<template>
  <div class="container-header">
    <header class="w-full text-sm font-medium">
      <div class="bg-gradient-to-r from-[#00b88c] to-[#007c8b] py-2 text-white">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-2 text-md">
          <div class="flex items-center flex-wrap gap-4">
            <div class="flex items-center gap-1">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.72 19.72 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13 1.05.37 2.07.72 3.06a2 2 0 0 1-.45 2.11l-1.27 1.27a16 16 0 0 0 6.29 6.29l1.27-1.27a2 2 0 0 1 2.11-.45c.99.35 2.01.59 3.06.72A2 2 0 0 1 22 16.92z" /></svg>
              <div class="flex gap-1">
                <span>{{ $t("client.header.hotlineLabel") }}</span>
                <NuxtLink to="#" data-tippy-content="hotline" class="hover:text-gray-300 transition-colors">{{ $t("client.header.hotline1") }} -</NuxtLink>
                <NuxtLink to="#" class="hover:text-gray-300 transition-colors">{{ $t("client.header.hotline2") }}</NuxtLink>
              </div>
            </div>
            <NuxtLink :to="TOAGENCY" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M3 9l1-5h16l1 5M5 22V12H19V22M2 9h20" /></svg>
              <span>{{ $t("client.header.dealerSystem") }}</span>
            </NuxtLink>
          </div>
          <div class="hidden md:flex items-center gap-4">
            <NuxtLink :to="TOFAQ" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4">
                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3l58.3 0c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24l0-13.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1l-58.3 0c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
              </svg>
              <span>{{ $t("client.header.faq") }}</span>
            </NuxtLink>
            <NuxtLink :to="TOPOLICY" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4">
                <path d="M256 80C149.9 80 62.4 159.4 49.6 262c9.4-3.8 19.6-6 30.4-6c26.5 0 48 21.5 48 48l0 128c0 26.5-21.5 48-48 48c-44.2 0-80-35.8-80-80l0-16 0-48 0-48C0 146.6 114.6 32 256 32s256 114.6 256 256l0 48 0 48 0 16c0 44.2-35.8 80-80 80c-26.5 0-48-21.5-48-48l0-128c0-26.5 21.5-48 48-48c10.8 0 21 2.1 30.4 6C449.6 159.4 362.1 80 256 80z" />
              </svg>
              <span>{{ $t("client.header.supportPolicy") }}</span>
            </NuxtLink>
            <!-- <NuxtLink to="#" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 3.87 3.13 7 7 7s7-3.13 7-7a7 7 0 0 0-7-7z" /></svg>
              <span>{{ $t("client.header.showroom") }}</span>
            </NuxtLink> -->
          </div>
        </div>
      </div>

      <div class="w-full max-w-7xl mx-auto">
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
      </div>

      <!-- Main Header -->
      <div class="bg-gradient-to-r from-[#00b88c] to-[#007c8b] px-2 pt-3">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
          <div class="hidden md:flex items-center gap-6">
            <NuxtLink to="/" class="h-15 w-40 rounded flex items-center justify-center">
              <img :src="logo" loading="lazy" alt="logo" />
            </NuxtLink>
          </div>
          <div class="flex-1 max-w-xl relative">
            <SearchSuggestion />
          </div>
          <div class="items-center gap-4 hidden sm:flex text-white">
            <NuxtLink to="#" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-7 h-7 mt-1.5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14,2 14,8 20,8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10,9 9,9 8,9" />
              </svg>
              <div class="font-light text-sm">
                <p>{{ $t("client.header.track") }}</p>
                <p>{{ $t("client.header.order") }}</p>
              </div>
            </NuxtLink>
            <div class="relative inline-block text-left group">
              <NuxtLink v-if="!user" :to="TOLOGIN" class="flex items-center gap-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 mt-1.5">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
                <div>
                  <span class="hover:underline">{{ $t("auth.login") }}</span>
                  <p class="font-light text-xs">{{ $t("client.nav.account") }}</p>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 mt-1.5"><polyline points="6,9 12,15 18,9" /></svg>
              </NuxtLink>
              <button v-else type="button" class="bg-transparent shadow-none focus:outline-none">
                <span class="flex items-center gap-1">
                  <span class="text-right">
                    <span class="hidden xl:inline-block font-medium">{{ user?.name }}</span>
                    <span class="hidden xl:block font-normal text-[10px]">
                      {{ user?.userCatalogueId === 1 ? $t("client.header.customer") : $t("client.header.admin") }}
                    </span>
                  </span>
                  <NuxtImg class="rounded-full w-9 h-9 object-cover" :src="avatar" :alt="'Avatar ' + user?.name" loading="lazy" />
                </span>
              </button>
              <div class="absolute top-full right-0 w-full h-3 group-hover:block hidden z-0"></div>
              <div v-if="user" class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 hidden group-hover:block transition-all duration-300 ease-in-out">
                <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
                  <div class="px-4 py-2 text-sm text-gray-500 font-semibold">{{ $t("client.header.hello", { name: user?.name }) }}</div>
                  <div class="py-1">
                    <NuxtLink :to="PROFILEINDEX" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2 cursor-pointer">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                      </svg>
                      <span>{{ $t("client.nav.info") }}</span>
                    </NuxtLink>
                    <NuxtLink :to="TOWISHLIST" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-gray-400 cursor-pointer mr-2">
                        <path d="M462.3 62.6C407 7.6 324.8-10.6 256 47.9 187.2-10.6 105-7.6 49.7 62.6-16.6 145.3 23.8 268.7 130.4 357.1L239 469.6c9.4 9.4 24.6 9.4 33.9 0l108.6-112.5C488.2 268.7 528.6 145.3 462.3 62.6z" />
                      </svg>
                      <span>{{ $t("client.nav.wishlist") }}</span>
                    </NuxtLink>
                    <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                      <span>{{ $t("client.header.messages") }}</span>
                    </NuxtLink>
                    <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                      </svg>
                      <span>{{ $t("client.header.taskboard") }}</span>
                    </NuxtLink>
                    <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 text-gray-400 mr-2" viewBox="0 0 512 512">
                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336l24 0 0-64-24 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l48 0c13.3 0 24 10.7 24 24l0 88 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-80 0c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z" />
                      </svg>
                      <span>{{ $t("client.header.help") }}</span>
                    </NuxtLink>
                  </div>
                  <div class="border-t border-gray-500"></div>
                  <div class="py-1">
                    <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                      </svg>
                      <span>
                        {{ $t("client.header.balance") }} :
                        <b>$5971.67</b>
                      </span>
                    </NuxtLink>
                    <NuxtLink to="#" class="relative flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 text-gray-400 mr-2" viewBox="0 0 512 512">
                        <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
                      </svg>
                      <span>{{ $t("client.header.settings") }}</span>
                      <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full">{{ $t("client.header.new") }}</span>
                    </NuxtLink>

                    <NuxtLink v-if="authStore.isAdmin" :to="TODASHBOARD" class="flex items-center px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor" class="w-4 h-4 mr-2">
                        <path d="M256.1 161.1L256.1 210.7L256.6 211.2C263.1 128.8 332 64 416.1 64C436.2 64 455.5 67.7 473.2 74.5C483.2 78.3 485 91 477.5 98.6L388.8 187.3C385.8 190.3 384.1 194.4 384.1 198.6L384.1 240C384.1 248.8 391.3 256 400.1 256L441.5 256C445.7 256 449.8 254.3 452.8 251.3L541.5 162.6C549.1 155 561.8 156.9 565.6 166.9C572.4 184.6 576.1 203.9 576.1 224C576.1 284.6 542.4 337.4 492.6 364.5L574.1 446C592.8 464.7 592.8 495.1 574.1 513.9L514 574C495.3 592.7 464.9 592.7 446.1 574L320.1 448C292.7 420.6 286.5 380.1 301.6 346.7L210.9 256L161.3 256C150.6 256 140.6 250.7 134.7 241.8L55.4 122.9C51.2 116.6 52 108.1 57.4 102.7L102.8 57.3C108.2 51.9 116.6 51.1 123 55.3L241.9 134.5C250.8 140.4 256.1 150.4 256.1 161.1zM247.6 360.6C241.3 397.6 250 436.7 274 468L179 562.9C150.9 591 105.3 591 77.2 562.9C49.1 534.8 49.1 489.2 77.2 461.1L212.6 325.7L247.6 360.7z" />
                      </svg>
                      <span>{{ $t("client.header.adminPanel") }}</span>
                    </NuxtLink>
                    <button @click="handleLogout" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100 w-full text-left">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mr-2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16,17 21,12 16,7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                      </svg>
                      <span>{{ $t("client.nav.logout") }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="relative inline-block text-left group">
              <NuxtLink :to="TOCART" class="block relative text-white mt-2 group/cart">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-1 min-w-[20px] text-center">
                  {{ cartCount }}
                </span>
              </NuxtLink>
              <div class="absolute top-full right-0 w-full h-5 group-hover:block hidden z-10"></div>
              <div v-if="cartItems.length > 0" class="absolute right-0 top-12 z-30 min-w-[24rem] origin-top-right bg-white shadow-md rounded-md hidden group-hover:block transition-all duration-300 ease-in-out">
                <div class="w-full rounded-md border-0 shadow-xl font-normal">
                  <div class="w-full max-w-md rounded-lg shadow-lg overflow-hidden text-sm">
                    <div class="px-4 py-2 bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-white font-medium">
                      {{ $t("client.action.quickView") }}
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
                    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                      <p class="text-gray-500 text-sm">{{ $t("client.cart.itemsInCart", { count: cartCount }) }}</p>
                      <NuxtLink :to="TOCART" class="bg-info-500 hover:bg-info-600 text-white font-medium p-2 rounded text-sm transition-all duration-200">
                        {{ $t("client.action.viewCart") }}
                      </NuxtLink>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile -->
          <div class="w-full flex items-center justify-between md:hidden my-2">
            <button @click="toggleMobileMenu" class="text-white p-2">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
              </svg>
            </button>
            <div class="flex items-center gap-4 text-white">
              <NuxtLink to="#" class="flex items-center gap-1 hover:text-gray-300 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 mt-1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10,9 9,9 8,9" />
                </svg>
                <span class="font-normal">
                  {{ $t("client.header.track") }}
                  <br />
                  {{ $t("client.header.order") }}
                </span>
              </NuxtLink>
              <div class="relative inline-block text-left group">
                <NuxtLink to="#" class="flex items-center gap-1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 mt-1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                  <div>
                    <span class="hover:underline">{{ $t("auth.login") }}</span>
                    <p class="font-normal text-sm">{{ $t("client.header.account") }}</p>
                  </div>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 mt-1.5"><polyline points="6,9 12,15 18,9" /></svg>
                </NuxtLink>
                <div class="absolute top-full right-0 w-full h-3 group-hover:block hidden z-0"></div>
                <div class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 hidden group-hover:block transition-all duration-300 ease-in-out">
                  <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
                    <div class="px-4 py-2 text-sm text-gray-500 font-semibold">{{ $t("auth.hello", { name: "Thanh Trung" }) }}</div>
                    <div class="py-1">
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                          <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span>{{ $t("client.nav.info") }}</span>
                      </NuxtLink>
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                        <span>{{ $t("client.header.messages") }}</span>
                      </NuxtLink>
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                          <line x1="16" y1="2" x2="16" y2="6" />
                          <line x1="8" y1="2" x2="8" y2="6" />
                          <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <span>{{ $t("client.header.taskboard") }}</span>
                      </NuxtLink>
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <circle cx="12" cy="12" r="10" />
                          <circle cx="12" cy="16" r="1" />
                          <path d="M12 13v-3" />
                        </svg>
                        <span>{{ $t("client.header.help") }}</span>
                      </NuxtLink>
                    </div>
                    <div class="border-t border-gray-500"></div>
                    <div class="py-1">
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        <span>
                          {{ $t("client.header.balance") }} :
                          <b>$5971.67</b>
                        </span>
                      </NuxtLink>
                      <NuxtLink to="#" class="relative flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-400 mr-2">
                          <circle cx="12" cy="12" r="3" />
                          <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1" />
                        </svg>
                        <span>{{ $t("client.nav.settings") }}</span>
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full">{{ $t("client.header.new") }}</span>
                      </NuxtLink>
                      <NuxtLink to="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mr-2">
                          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                          <polyline points="16,17 21,12 16,7" />
                          <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span>{{ $t("auth.logout") }}</span>
                      </NuxtLink>
                    </div>
                  </div>
                </div>
              </div>
              <div class="relative inline-block text-left group">
                <NuxtLink :to="TOCART" class="block relative text-white mt-4 group/cart">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-1 min-w-[20px] text-center">
                    {{ cartCount }}
                  </span>
                </NuxtLink>
                <div class="absolute top-full right-0 w-full max-h-5 group-hover:block hidden z-10"></div>
                <div v-if="cartItems.length > 0" class="absolute right-0 top-12 z-30 min-w-[24rem] origin-top-right bg-white shadow-md rounded-md hidden group-hover:block transition-all duration-300 ease-in-out">
                  <div class="w-full rounded-md border-0 shadow-xl font-normal">
                    <div class="w-full max-w-md rounded-lg shadow-lg overflow-hidden text-sm">
                      <div class="px-4 py-2 bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-white font-medium">
                        {{ $t("client.action.quickView") }}
                      </div>
                      <div class="max-h-[300px] overflow-y-auto">
                        <div v-for="item in cartItems" :key="item.id" class="flex items-start px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                          <NuxtImg :src="getCartItemImage(item)" :alt="getCartItemName(item)" class="w-14 h-14 object-cover rounded mr-3 flex-shrink-0" loading="lazy" />
                          <div class="flex-1 min-w-0">
                            <NuxtLink :to="`/product/${item.product?.slug || ''}`" v-tooltip.top="getCartItemName(item)" class="font-semibold text-gray-800 leading-snug text-sm hover:text-teal-600">
                              <p class="line-clamp-2">
                                {{ getCartItemName(item) }}
                              </p>
                            </NuxtLink>
                            <div class="flex items-center justify-between mt-1">
                              <span class="text-red-500 text-xs">{{ formatPrice((item.price || 0) * (item.quantity || 0)) }}</span>
                              <span class="text-gray-400 text-xs">x{{ item.quantity }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                        <span class="text-gray-500 text-sm">{{ $t("client.cart.itemsInCart", { count: cartCount }) }}</span>
                        <NuxtLink :to="TOCART" class="!bg-info-500 hover:!bg-info-600 text-white font-medium p-2 rounded text-sm transition-all duration-200">
                          {{ $t("client.action.viewCart") }}
                        </NuxtLink>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="w-full max-w-7xl mx-auto">
          <div class="mt-8 hidden sm:grid grid-cols-2 md:grid-cols-5 text-white text-sm">
            <!-- menu -->
            <Menu :menu-tree="menuTree" />

            <div class="flex items-center justify-center bg-gradient-to-r from-[#a8d444] to-[#b7e354] text-[#005a63] hover:opacity-90 cursor-pointer h-10 px-3 rounded transition-opacity">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mt-1 text-yellow-300 mr-1"><polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2" /></svg>
              <span>{{ $t("client.common.promotion") }}</span>
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- mobile -->
    <MenuMobile :is-open="isMobileMenuOpen" :menu-tree="menuTree" @close="closeMobileMenu" />
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useI18n } from "vue-i18n";
import { TOLOGIN, TOAGENCY, TOFAQ, PROFILEINDEX, TODASHBOARD, TOCART, TOPOLICY, TOWISHLIST } from "~/utils/constants";
import logo from "~/assets/shared/images/logo/logo-website.png";
import { useAuthStore } from "~/stores/auth/auth";
import { useAuth } from "~/composables/auth/useAuth";
import { useCart } from "~/composables/client/cart/useCart";
import { useMenu } from "~/composables/client/menu/useMenu";
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { IMAGEDEFAULT } from "~/utils/constants";
import type { ICartItem } from "~/interfaces/cart/cartItem.interface";
import SearchSuggestion from "../features/SearchSuggestion.vue";
import Menu from "../navigation/Menu.vue";
import MenuMobile from "../navigation/MenuMobile.vue";

const { t } = useI18n();

// Auth
const authStore = useAuthStore();
const { user } = storeToRefs(authStore);
const avatar = computed(() => {
  const img = user.value?.image;
  if (img && img.path) return img.url || "/shared/images/defaults/avatar.jpg";
  return "/shared/images/defaults/avatar.jpg";
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
