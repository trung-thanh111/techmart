<template>
  <div class="main-content max-w-7xl mx-auto">
    <div v-if="postDetailLoading" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
        <p class="text-gray-600">{{ $t('client.postDetail.loadingPost') }}</p>
      </div>
    </div>
    <div v-else-if="postDetailError" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <div class="text-red-500 text-6xl mb-4">⚠️</div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $t('client.postDetail.cannotLoadPost') }}</h2>
        <p class="text-gray-600 mb-4">{{ postDetailError }}</p>
        <NuxtLink to="/post" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">{{ $t('client.postDetail.backToList') }}</NuxtLink>
      </div>
    </div>
    <div v-else-if="postDetail">
      <Breadcrumb :crumbs="breadcrumbs" />
      <section class="relative w-full mb-5">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-12 lg:col-span-9 bg-white rounded-lg shadow-md p-2">
            <div class="h-[40vw] max-h-[400px] min-h-[180px] overflow-hidden rounded-xl">
              <NuxtImg :src="postDetail?.image?.url || '/public/images/posts/post-home-4.jpg'" :alt="postDetail?.name || 'Cover'" class="w-full h-full object-cover object-center" loading="lazy" />
            </div>
            <div class="max-w-4xl mx-auto px-2 sm:px-4 -mt-16 sm:-mt-24 relative z-10">
              <div class="bg-white shadow-xl rounded-2xl p-3 sm:p-6 md:p-8">
                <div class="flex flex-wrap gap-3 items-center mb-4">
                  <span v-for="catalogue in postDetail?.postCatalogues as any || []" :key="catalogue.id" class="bg-red-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                    {{ catalogue.name }}
                  </span>
                  <span v-for="tag in postDetail?.tags as any || []" :key="tag.id" class="bg-sky-100 text-sky-600 text-xs font-medium px-3 py-1 rounded-full">#{{ tag.name }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 leading-snug mb-4 break-words">
                  {{ postDetail?.name || "Đang tải..." }}
                </h1>
                <div class="flex items-center text-sm text-gray-500 mb-6">
                  <NuxtImg :src="postDetail?.creator?.avatar || '/public/images/social/zalo.png'" :alt="postDetail?.creator?.name || 'Author'" class="w-8 h-8 rounded-full mr-3 border border-gray-300" loading="lazy" />
                  <div>
                    <p class="text-red-600 font-semibold">{{ postDetail?.creator?.name || $t('client.postDetail.author') }}</p>
                    <div class="flex items-center text-gray-400">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 0 0-1 1v1H5a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3h-.01V3a1 1 0 1 0-2 0v1H8V3a1 1 0 0 0-1-1H6z" /></svg>
                      <span>{{ postDetail?.publishedAtHuman }}</span>
                    </div>
                  </div>
                </div>
                <div v-if="toc.length" class="mb-5 bg-sky-50 border-l-4 border-sky-400 rounded-md">
                  <div class="flex items-center justify-between w-full px-5 py-3 cursor-pointer" @click="isTocOpen = !isTocOpen">
                    <p class="font-semibold text-sky-700 text-lg">{{ $t('client.postDetail.tableOfContent') }}</p>
                    <svg class="w-4 h-4 shrink-0 transition-transform" :class="isTocOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                  <transition name="accordian">
                    <div v-show="isTocOpen" class="px-5 pb-5">
                      <ul class="space-y-2 text-sky-600 text-sm mt-2">
                        <li v-for="item in toc" :key="item.id">
                          <a :href="`#${item.id}`" class="hover:underline font-medium" :class="{ 'pl-4': item.level > 2, 'pl-8': item.level > 3 }">
                            {{ item.text }}
                          </a>
                        </li>
                      </ul>
                    </div>
                  </transition>
                </div>
                <div v-if="contentWithIds" class="text-gray-700 space-y-3 sm:space-y-4 text-base leading-relaxed break-words prose prose-lg max-w-none" v-html="contentWithIds"></div>
                <div v-else-if="postDetailLoading" class="text-gray-500 italic">{{ $t('client.postDetail.loadingContent') }}</div>
                <div v-else class="text-gray-500 italic">{{ $t('client.postDetail.noContent') }}</div>
                <div class="flex flex-wrap items-center justify-between mt-3 gap-1 sm:gap-0">
                  <div class="flex items-center space-x-1 text-gray-500 cursor-pointer">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" /></svg>
                    <span class="text-xs sm:text-sm">{{ $t('client.postDetail.likes') }}</span>
                  </div>
                  <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-3">
                    <span class="caption font-medium text-gray-500 mr-2 md:mr-3 hidden sm:block text-xs sm:text-sm">{{ $t('client.postDetail.share') }}</span>
                    <NuxtLink aria-label="message-share" class="hover:opacity-70">
                      <img src="/assets/client/images/social/messenger.png" alt="facebook message" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <NuxtLink aria-label="facebook-share" class="hover:opacity-70">
                      <img src="/assets/client/images/social/facebook.png" alt="facebook share" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <NuxtLink aria-label="printer-share" class="hover:opacity-70">
                      <img src="/assets/client/images/social/zalo.png" alt="sharing zalo" class="object-cover w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" loading="lazy" />
                    </NuxtLink>
                    <div class="relative">
                      <div class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 hover:opacity-70">
                        <span>
                          <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 24">
                            <rect x=".5" fill="#D8DCE7" rx="12"></rect>
                            <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.156 8.03l1.532-1.532a3.404 3.404 0 1 1 4.814 4.816l-2.188 2.189a3.403 3.403 0 0 1-4.814 0"></path>
                            <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.844 15.97l-1.532 1.532a3.406 3.406 0 0 1-4.815 0 3.406 3.406 0 0 1 .001-4.816l2.188-2.189a3.404 3.404 0 0 1 4.814 0"></path>
                          </svg>
                        </span>
                      </div>
                      <input id="input-copy" type="hidden" />
                      <div class="absolute top-0 bg-green-300 text-white duration-300 py-1 px-3 w-max left-[160%] hidden">{{ $t('client.postDetail.copiedLink') }}</div>
                    </div>
                  </div>
                </div>
                <div class="w-full max-w-8xl mx-auto my-5">
                  <div class="h-[2px] w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
                </div>
                <div class="max-w-2xl mt-5">
                  <h1 class="text-2xl font-bold text-gray-600 mb-4 tracking-wide">{{ $t('client.postDetail.yourThoughts') }}</h1>
                  <form class="rounded-md bg-gray-100 overflow-hidden shadow-md">
                    <textarea class="w-full p-4 text-gray-600 placeholder-gray-500 focus:outline-none resize-none bg-gray-100" rows="4" :placeholder="$t('client.postDetail.whatAreYouThinking')" required></textarea>
                    <div class="flex items-center justify-between bg-gray-300 px-4 py-2">
                      <div class="flex items-center space-x-4 text-gray-600">
                        <button type="button">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18v14H3V5zm3 8l3 3 4-4 5 5H5z" /></svg>
                        </button>
                      </div>
                      <button class="flex items-center justify-center ml-3 text-white rounded-md h-9 w-14 bg-sky-500 hover:bg-sky-600 xl:ml-5">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.98481 2.44399C3.11333 1.57147 1.15325 3.46979 1.96543 5.36824L3.82086 9.70527C3.90146 9.89367 3.90146 10.1069 3.82086 10.2953L1.96543 14.6323C1.15326 16.5307 3.11332 18.4291 4.98481 17.5565L16.8184 12.0395C18.5508 11.2319 18.5508 8.76865 16.8184 7.961L4.98481 2.44399ZM3.34453 4.77824C3.0738 4.14543 3.72716 3.51266 4.35099 3.80349L16.1846 9.32051C16.762 9.58973 16.762 10.4108 16.1846 10.68L4.35098 16.197C3.72716 16.4879 3.0738 15.8551 3.34453 15.2223L5.19996 10.8853C5.21944 10.8397 5.23735 10.7937 5.2537 10.7473L9.11784 10.7473C9.53206 10.7473 9.86784 10.4115 9.86784 9.99726C9.86784 9.58304 9.53206 9.24726 9.11784 9.24726L5.25157 9.24726C5.2358 9.20287 5.2186 9.15885 5.19996 9.11528L3.34453 4.77824Z" fill="white"></path></svg>
                      </button>
                    </div>
                  </form>
                </div>
                <div class="max-w-2xl mx-auto space-y-4 mt-5">
                  <h1 class="text-xl font-bold text-gray-500 mb-4">{{ $t('client.postDetail.comments') }} (23)</h1>
                  <div v-for="product_reviews in 5" :key="product_reviews" class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                      <div class="flex items-center space-x-3">
                        <img class="w-8 h-8 rounded-full" src="https://i.pravatar.cc/40" alt="avatar" loading="lazy" />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">Michael Gough</p>
                          <p class="text-xs text-gray-500">Feb. 8, 2022</p>
                        </div>
                      </div>
                      <div class="relative inline-block text-left group">
                        <button class="text-gray-400">
                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm6-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm4-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" /></svg>
                        </button>
                        <div class="absolute top-full right-0 w-full h-3 group-hover:block hidden z-0"></div>
                        <div class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 hidden group-hover:block transition-all duration-300 ease-in-out">
                          <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
                            <div class="py-1">
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-user text-gray-400 text-md mr-2"></i>
                                <span>Profile</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-comment-dots text-gray-400 text-md mr-2"></i>
                                <span>Messages</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-calendar-check text-gray-400 text-md mr-2"></i>
                                <span>Taskboard</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-solid fa-life-ring text-gray-400 text-md mr-2"></i>
                                <span>Help</span>
                              </a>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="py-1">
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                                <i class="fa-solid fa-arrow-right-from-bracket text-md mr-2"></i>
                                <span>Logout</span>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-3">Very straight-to-point article. Really worth time reading. Thank you! But tools are just the instruments for the UX designers...</p>
                    <div class="flex items-center space-x-6 text-gray-500 text-xs">
                      <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" /></svg>
                        <span>11 thích</span>
                      </span>
                      <button class="hover:underline hover:text-sky-500">{{ $t('client.postDetail.reply') }}</button>
                    </div>
                  </div>
                  <div class="ml-8 bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                      <div class="flex items-center space-x-3">
                        <img class="w-8 h-8 rounded-full" src="https://i.pravatar.cc/40?img=2" alt="avatar" loading="lazy" />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">Jese Leos</p>
                          <p class="text-xs text-gray-500">Feb. 12, 2022</p>
                        </div>
                      </div>
                      <div class="relative inline-block text-left group">
                        <button class="text-gray-400">
                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm6-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm4-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" /></svg>
                        </button>
                        <div class="absolute top-full right-0 w-full h-3 group-hover:block hidden z-0"></div>
                        <div class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 hidden group-hover:block transition-all duration-300 ease-in-out">
                          <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
                            <div class="py-1">
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-user text-gray-400 text-md mr-2"></i>
                                <span>Profile</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-comment-dots text-gray-400 text-md mr-2"></i>
                                <span>Messages</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-regular fa-calendar-check text-gray-400 text-md mr-2"></i>
                                <span>Taskboard</span>
                              </a>
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-solid fa-life-ring text-gray-400 text-md mr-2"></i>
                                <span>Help</span>
                              </a>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="py-1">
                              <a href="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                                <i class="fa-solid fa-arrow-right-from-bracket text-md mr-2"></i>
                                <span>Logout</span>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-3">Much appreciated! Glad you liked it 😊</p>
                    <div class="flex items-center space-x-6 text-gray-500 text-xs">
                      <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" /></svg>
                        <span>11 thích</span>
                      </span>
                      <button class="hover:underline hover:text-sky-500">{{ $t('client.postDetail.reply') }}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Sidebar -->
          <div class="col-span-12 lg:col-span-3">
            <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200 mb-3 md:mb-5">
              <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">{{ $t('client.postDetail.quickView') }}</h2>
              <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-2 gap-2 mb-3 md:mb-4">
                <NuxtLink v-for="post in (hotQuickItems || []).slice(0, 6)" :key="post.id" :to="`/post/${post.slug}`" class="group space-y-1 block">
                  <NuxtImg :src="post.image?.url || '/public/images/posts/post-home-5.jpg'" :alt="post.name" class="rounded-md object-cover w-full h-16 md:w-[145px] md:h-[80px]" loading="lazy" />
                  <span class="text-xs md:text-sm text-gray-800 font-medium group-hover:text-cyan-600 transition-colors line-clamp-2">
                    {{ post.name }}
                  </span>
                </NuxtLink>
              </div>
              <NuxtLink to="/post" class="block w-full text-center text-xs md:text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded py-2 transition">{{ $t('client.postDetail.viewAll') }} ({{ (hotQuickItems || []).length }})</NuxtLink>
            </div>
            <div class="bg-white rounded-lg p-3 md:p-4 border border-gray-200">
              <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-4">
                <span class="text-sky-600">#</span>
                {{ $t('client.postDetail.tags') }}
              </h2>
              <div class="flex flex-wrap gap-2">
                <NuxtLink v-for="tag in tagList || []" :key="tag.id" :to="`/post?tag=${tag.slug}`" class="px-2 md:px-3 py-1 text-xs md:text-[12px] bg-[#d9dfe3] max-w-max rounded font-semibold text-[#7281a3] hover:bg-[#c8d1d9] transition-colors">#{{ tag.name }}</NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section v-if="relateds.length > 0" class="mx-auto bg-white rounded-md shadow sm:py-1 mb-2 md:py-2 px-1 sm:px-3 md:px-6 md:mb-6 sm:mb-3">
        <h2 class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ $t('client.postDetail.relatedPosts') }}</h2>
        <Splide :options="postRelatedOptions(relateds.length)" id="post-related-slide">
          <SplideSlide v-for="relatedItem in relateds" :key="relatedItem.id">
            <NuxtLink :to="`/post/${encodeURIComponent(relatedItem.slug)}`">
              <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-cyan-200 flex flex-col h-full">
                <div class="relative overflow-hidden aspect-[16/9] w-full">
                  <NuxtImg :src="relatedItem.image?.url || '/public/images/posts/post-home-3.jpg'" :alt="relatedItem.name" class="w-full h-full object-cover object-center rounded-t-xl" loading="lazy" />
                  <div v-if="relatedItem.isHot" class="absolute top-2 left-2 bg-white/10 backdrop-blur-sm rounded-lg px-2 py-1">
                    <span class="text-red-500 font-bold text-xs">{{ $t('client.postDetail.hot') }}</span>
                  </div>
                </div>
                <div class="p-3 sm:p-4 flex flex-col flex-1">
                  <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-700 mb-2 sm:mb-3 hover:text-cyan-600 transition-colors line-clamp-2 break-words">
                    {{ relatedItem.name }}
                  </h3>
                  <p class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-4 leading-relaxed line-clamp-2 break-words">
                    {{ relatedItem.description }}
                  </p>
                  <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 mt-auto">
                    <span>{{ relatedItem.creator?.name }}</span>
                    <span>{{ relatedItem.publishedAtHumen }}</span>
                  </div>
                </div>
              </div>
            </NuxtLink>
          </SplideSlide>
        </Splide>
      </section>
    </div>
  </div>
</template>
<script setup lang="ts">
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { usePost } from "~/composables/client/post/usePost";
import { buildToc, type TocItem } from "~/composables/useToc";
import { usePostSlider } from "~/composables/usePostSlider";

const { fetchDetailBySlug, postDetail, postDetailLoading, postDetailError, fetchTags, loadHotQuick, tagList, hotQuickItems } = usePost();
const { postRelatedOptions } = usePostSlider();

const isTocOpen = ref(true);
const contentWithIds = ref<string>("");
const toc = ref<TocItem[]>([]);

const slug = computed(() => String(useRoute().params.slug || ""));

const relateds = computed(() => (Array.isArray((postDetail.value as any)?.relateds) ? (postDetail.value as any).relateds : []));

onMounted(async () => {
  if (!slug.value) return;
  await fetchDetailBySlug(slug.value);
  await Promise.all([fetchTags(), loadHotQuick()]);
});

watch(
  () => postDetail.value?.content,
  (content) => {
    if (content) {
      const { toc: t, html } = buildToc(content);
      toc.value = t;
      contentWithIds.value = html;
    } else {
      toc.value = [];
      contentWithIds.value = "";
    }
  },
  { immediate: true }
);

const breadcrumbs = computed(() => [
  { label: $t('client.post.breadcrumb'), href: "/post" },
  { label: postDetail.value?.name || slug.value, href: `/post/${slug.value}` },
]);

useHead(() => ({
  title: postDetail.value?.metaTitle || postDetail.value?.name || "Bài viết",
  meta: [
    { name: "description", content: postDetail.value?.metaDescription || postDetail.value?.description || "" },
    { name: "keywords", content: postDetail.value?.metaKeyword || "" },
    { property: "og:title", content: postDetail.value?.name || "Bài viết" },
    { property: "og:description", content: postDetail.value?.description || "" },
    { property: "og:image", content: postDetail.value?.image?.url || "" },
    { property: "og:type", content: "article" },
  ],
}));

definePageMeta({
  layout: "client",
});
</script>
