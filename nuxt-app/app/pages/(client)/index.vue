<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- baner -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 py-4">
      <!-- Hero Banner Left -->
      <div class="md:col-span-3">
        <div class="space-y-4">
          <div class="left-hero-banner">
            <!-- Main Slider -->
            <Splide ref="mainSlider" :options="mainOptions" class="mb-2 max-h-[400px]" id="hero-slide-left-main">
              <SplideSlide v-for="(item, index) in mainItems" :key="`main-${index}`">
                <NuxtLink v-if="item.link" :to="item.link" class="block w-full h-full">
                  <NuxtImg :src="item.image" loading="lazy" :alt="item.alt || `Slide ${index + 1}`" class="w-full h-[200px] sm:h-[250px] md:h-[300px] lg:h-[400px] object-cover rounded-lg" />
                </NuxtLink>
                <NuxtImg v-else :src="item.image" loading="lazy" :alt="item.alt || `Slide ${index + 1}`" class="w-full h-[200px] sm:h-[250px] md:h-[300px] lg:h-[400px] object-cover rounded-lg" />
              </SplideSlide>
            </Splide>

            <!-- Thumbnail Slider -->
            <Splide ref="thumbSlider" :options="thumbOptions" id="hero-slide-content-thumbs">
              <SplideSlide v-for="(item, index) in thumbnailItems" :key="`thumb-${index}`">
                <div class="bg-white p-2 md:p-3 rounded shadow text-center text-gray-700 flex flex-col items-center cursor-pointer hover:bg-gray-50 transition-colors">
                  <div class="flex justify-center items-center gap-2">
                    <span class="text-lg mb-1" v-html="item.icon"></span>
                    <h3 class="font-semibold line-clamp-1 md:text-lg text-base">{{ item.title }}</h3>
                  </div>
                  <p class="line-clamp-1 text-sm hidden md:inline-block">{{ item.description }}</p>
                </div>
              </SplideSlide>
            </Splide>
          </div>
        </div>
      </div>
      <!-- Right Banner -->
      <div class="md:col-span-1">
        <Splide :options="rightVirticalOptions" class="h-full" id="right-vertical-slider">
          <SplideSlide v-for="(item, index) in rightVirticalItems" :key="`right-${index}`">
            <NuxtLink v-if="item.link" :to="item.link" class="block w-full h-full">
              <NuxtImg :src="item.image" loading="lazy" :alt="item.alt || 'Banner image'" class="w-full h-[200px] sm:h-[200px] md:h-[250px] lg:h-auto object-cover rounded-lg shadow-sm hover:opacity-90 transition-all" />
            </NuxtLink>
            <NuxtImg v-else :src="item.image" loading="lazy" :alt="item.alt || 'Banner image'" class="w-full h-[200px] sm:h-[200px] md:h-[250px] lg:h-auto object-cover rounded-lg shadow-sm hover:opacity-90 transition-all" />
          </SplideSlide>
        </Splide>
      </div>
    </div>

    <div class="w-full max-w-7xl mx-auto py-2 mb-3 md:mb-5">
      <div class="h-px w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
    </div>

    <CommitMent />

    <section class="product-sell-well bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.eraOfTech") }}</h2>
            <div class="h-1 max-w-64 md:my-2 my-1 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="group flex items-center space-x-2">
            <NuxtLink to="/shop" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 group-hover:after:w-full md:text-base text-sm">{{ $t("home.viewAll") }}</NuxtLink>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.popularLightingDesc") }}</p>
      </div>
      <div class="max-w-7xl mx-auto">
        <DataState :loading="featuredProductsLoading" :error="false" :data="featuredProducts" :show-empty-image="true">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <ProductCard v-for="product in featuredProducts.slice(0, 4)" :key="product.id" :product="product" :loading="false" />
          </div>
        </DataState>
      </div>
    </section>
    <section class="product-sales bg-[linear-gradient(99.73deg,rgba(43,57,144,0.91)_-22.07%,#0097cc_47.78%,#8dc63f_117.9%)] rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <!-- Header -->
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div>
            <h2 class="text-white text-xl sm:text-3xl font-bold">{{ $t("home.superSale") }}</h2>
            <div class="h-1 max-w-48 md:my-2 my-1 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="after:-z-20 after:absolute after:w-64 after:h-16 after:bg-sky-700 after:rounded-2xl after:shadow-inner after:shadow-slate-900 relative rounded-2xl shadow-inner shadow-gray-50 flex justify-evenly items-center w-64 h-16 bg-sky-700 text-gray-50 after:outline-double after:-outline-offset-8 after:outline-white">
            <div class="absolute w-60 h-12 bg-neutral-900 rounded-2xl flex justify-around items-center before:absolute before:w-8 before:bg-sky-400 before:rounded-full before:blur-xl before:right-2 before:h-12 outline-double -outline-offset-4 outline-sky-800">
              <div class="count-down flex items-center gap-2 font-extrabold text-4xl z-10">
                <span class="hour-count-down">00</span>
                <span class="text-2xl">:</span>
                <span class="minute-count-down">00</span>
                <span class="text-2xl">:</span>
                <span class="second-count-down">00</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-between items-center">
          <p class="text-white/90 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.popularLightingDesc") }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <ProductCard v-for="product in superSaleProducts" :key="product.id" :product="product" />
      </div>
      <!-- xem thêm  -->
      <ButtonViewMore />
    </section>
    <section class="product-sell-well bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.bestSellers") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="relative group flex items-center space-x-2">
            <div class="relative">
              <button class="hover:text-sky-500 transition">
                <svg viewBox="0 0 448 512" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5">
                  <defs>
                    <linearGradient id="icon-gradient" x1="0" y1="0" x2="1" y2="1">
                      <stop offset="0%" stop-color="#2b3990" />
                      <stop offset="50%" stop-color="#0097cc" />
                      <stop offset="100%" stop-color="#8dc63f" />
                    </linearGradient>
                  </defs>
                  <path fill="url(#icon-gradient)" d="M192 104.8c0-9.2-5.8-17.3-13.2-22.8C167.2 73.3 160 61.3 160 48c0-26.5 28.7-48 64-48s64 21.5 64 48c0 13.3-7.2 25.3-18.8 34c-7.4 5.5-13.2 13.6-13.2 22.8c0 12.8 10.4 23.2 23.2 23.2l56.8 0c26.5 0 48 21.5 48 48l0 56.8c0 12.8 10.4 23.2 23.2 23.2c9.2 0 17.3-5.8 22.8-13.2c8.7-11.6 20.7-18.8 34-18.8c26.5 0 48 28.7 48 64s-21.5 64-48 64c-13.3 0-25.3-7.2-34-18.8c-5.5-7.4-13.6-13.2-22.8-13.2c-12.8 0-23.2 10.4-23.2 23.2L384 464c0 26.5-21.5 48-48 48l-56.8 0c-12.8 0-23.2-10.4-23.2-23.2c0-9.2 5.8-17.3 13.2-22.8c11.6-8.7 18.8-20.7 18.8-34c0-26.5-28.7-48-64-48s-64 21.5-64 48c0 13.3 7.2 25.3 18.8 34c7.4 5.5 13.2 13.6 13.2 22.8c0 12.8-10.4 23.2-23.2 23.2L48 512c-26.5 0-48-21.5-48-48L0 343.2C0 330.4 10.4 320 23.2 320c9.2 0 17.3 5.8 22.8 13.2C54.7 344.8 66.7 352 80 352c26.5 0 48-28.7 48-64s-21.5-64-48-64c-13.3 0-25.3 7.2-34 18.8C40.5 250.2 32.4 256 23.2 256C10.4 256 0 245.6 0 232.8L0 176c0-26.5 21.5-48 48-48l120.8 0c12.8 0 23.2-10.4 23.2-23.2z" />
                </svg>
              </button>
              <div class="absolute top-full right-0 w-full h-5 group-hover:block hidden z-0"></div>
              <div class="absolute top-full right-0 mt-1 w-72 bg-white border border-gray-200 rounded shadow-lg p-3 z-50 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200">
                <p class="text-sm font-semibold mb-2 text-gray-700">{{ $t("home.suggestedProducts") }}</p>
                <ul class="space-y-3 text-sm text-gray-600">
                  <li v-for="(product_similar, id) in 3" :key="id">
                    <NuxtLink to="/shop" class="flex items-center gap-3 p-2 rounded-lg transition hover:bg-gray-50">
                      <NuxtImg :src="IMAGEDEFAULT" loading="lazy" :alt="$t('home.suggestedProducts')" class="w-12 h-12 object-cover rounded-md" />
                      <div class="flex-1">
                        <h4 class="font-medium text-gray-700 transition line-clamp-1 group-hover:text-sky-500">Ốp lưng silicon iPhone</h4>
                        <span class="text-xs text-gray-500 line-clamp-1">Chống sốc, chống bám vân tay</span>
                      </div>
                      <div class="text-right">
                        <span class="text-red-600 font-semibold whitespace-nowrap">+29.000 ₫</span>
                        <span class="block text-xs text-gray-400 line-through">59.000 ₫</span>
                      </div>
                    </NuxtLink>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.popularLightingDesc") }}</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <ProductCard v-for="product in bestSellerProducts" :key="product.id" :product="product" />
      </div>
      <!-- xem thêm  -->
      <ButtonViewMore />
    </section>
    <section class="bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.gamingSetup") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.popularLightingDesc") }}</p>
      </div>
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 md:gap-4 gap-2">
        <!-- Left column -->
        <div class="grid grid-cols-2 md:gap-4 gap-2">
          <!-- Left column -->
          <div class="grid grid-rows-2 md:gap-4 gap-2">
            <!-- Item 1 -->
            <div class="relative bg-cover bg-no-repeat bg-center text-white rounded-2xl h-32 sm:h-48 p-4 flex flex-col justify-between shadow-lg" style="background-image: url(&quot;/images/slide/slide_home_sub.webp&quot;)">
              <div class="absolute inset-0 bg-black/10 rounded-2xl backdrop-brightness-[.85] z-0"></div>
              <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                  <h3 class="text-sm sm:text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)] font-semibold leading-tight mb-1">{{ $t("home.gamingAccessories") }}</h3>
                  <p class="text-xs">{{ $t("home.playToWin") }}</p>
                </div>
                <div class="text-right">
                  <NuxtLink to="/shop" class="text-xs sm:text-sm font-medium inline-block hover:text-sky-300 underline">{{ $t("home.viewNow") }}</NuxtLink>
                </div>
              </div>
            </div>
            <div class="relative bg-cover bg-center text-white rounded-2xl h-32 sm:h-48 p-4 flex flex-col justify-between shadow-lg" style="background-image: url(&quot;/images/slide/slide_home_sub_2.webp&quot;)">
              <div class="absolute inset-0 bg-black/10 rounded-2xl backdrop-brightness-[.85] z-0"></div>
              <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                  <h3 class="text-sm sm:text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)] font-semibold leading-tight mb-1">{{ $t("home.gamingConsoles") }}</h3>
                  <p class="text-xs">{{ $t("home.highPerformance") }}</p>
                </div>
                <div class="text-right">
                  <NuxtLink to="/shop" class="text-xs sm:text-sm font-medium inline-block hover:text-sky-300 underline">{{ $t("home.viewNow") }}</NuxtLink>
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-rows-2 md:gap-4 gap-2">
            <div class="relative bg-cover bg-center text-white rounded-2xl h-32 sm:h-48 p-4 flex flex-col justify-between shadow-lg" style="background-image: url(&quot;/images/slide/slide_home_sub_3.webp&quot;)">
              <div class="absolute inset-0 bg-black/10 rounded-2xl backdrop-brightness-[.85] z-0"></div>
              <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                  <h3 class="text-sm sm:text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)] font-semibold leading-tight mb-1">{{ $t("home.gamingMonitor") }}</h3>
                  <p class="text-xs">{{ $t("home.highRefreshRate") }}</p>
                </div>
                <div class="text-right">
                  <NuxtLink to="/shop" class="text-xs sm:text-sm font-medium inline-block hover:text-sky-300 underline">{{ $t("home.viewNow") }}</NuxtLink>
                </div>
              </div>
            </div>
            <div class="relative bg-cover bg-center text-white rounded-2xl h-32 sm:h-48 p-4 flex flex-col justify-between shadow-lg" style="background-image: url(&quot;/images/slide/slide_home_sub_4.webp&quot;)">
              <div class="absolute inset-0 bg-black/30 rounded-2xl backdrop-brightness-[.85] z-0"></div>
              <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                  <h3 class="text-sm sm:text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)] font-semibold leading-tight mb-1">{{ $t("home.mechanicalKeyboard") }}</h3>
                  <p class="text-xs">{{ $t("home.switchCherryMX") }}</p>
                </div>
                <div class="text-right">
                  <NuxtLink to="/shop" class="text-xs sm:text-sm font-medium inline-block hover:text-sky-300 underline">{{ $t("home.viewNow") }}</NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="relative bg-contain bg-center text-white rounded-2xl min-h-[264px] sm:min-h-[392px] p-4 sm:p-6 flex flex-col justify-between shadow-xl" style="background-image: url(&quot;/images/slide/slide_home_sub_3.jpg&quot;)">
          <div class="absolute inset-0 bg-black/30 rounded-2xl backdrop-brightness-[.85] z-0"></div>
          <div class="relative z-10 flex flex-col justify-between h-full text-right">
            <div>
              <h3 class="text-lg sm:text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)] font-semibold leading-tight mb-1">{{ $t("home.gamingLaptop") }}</h3>
              <p class="text-xs">{{ $t("home.battleReady") }}</p>
            </div>
            <div class="text-right">
              <NuxtLink to="/shop" class="text-xs sm:text-sm font-medium inline-block hover:text-sky-300 underline">{{ $t("home.viewNow") }}</NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="product-sales bg-[linear-gradient(99.73deg,rgba(43,57,144,0.91)_-22.07%,#0097cc_47.78%,#8dc63f_117.9%)] rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <!-- Header -->
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div>
            <h2 class="text-white text-xl md:text-3xl font-bold">{{ $t("home.highConfig") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="relative inline-block group">
            <button type="button" class="flex items-center space-x-1 text-white hover:text-gray-300">
              <span class="text-xs md:text-sm font-medium">{{ $t("home.sort") }}</span>
              <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                <path d="M151.6 469.6C145.5 476.2 137 480 128 480s-17.5-3.8-23.6-10.4l-88-96c-11.9-13-11.1-33.3 2-45.2s33.3-11.1 45.2 2L96 365.7 96 64c0-17.7 14.3-32 32-32s32 14.3 32 32l0 301.7 32.4-35.4c11.9-13 32.2-13.9 45.2-2s13.9 32.2 2 45.2l-88 96zM320 32l32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-96 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l160 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-160 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
              </svg>
            </button>
            <div class="absolute top-full right-0 w-full h-6 group-hover:block hidden z-0"></div>
            <div class="absolute right-0 z-50 mt-2 w-48 md:w-64 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-2xl ring-1 ring-black/10 hidden group-hover:block transition-all duration-300 ease-in-out">
              <div class="py-2 px-3 space-y-1 text-xs sm:text-sm md:text-base text-gray-700">
                <button @click="handleSortHighConfig('id,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': highConfigSort === 'id,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-clock SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.3 0-200-89.7-200-200S145.7 56 256 56s200 89.7 200 200-89.7 200-200 200zm12-200V136c0-6.6-5.4-12-12-12s-12 5.4-12 12v120c0 6.6 5.4 12 12 12h88c6.6 0 12-5.4 12-12s-5.4-12-12-12h-76z" /></svg>
                    {{ $t("home.newest") }}
                  </span>
                  <svg v-if="highConfigSort === 'id,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortHighConfig('price_origin,asc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': highConfigSort === 'price_origin,asc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-arrow-up-wide-short SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80s21.5 48 48 48h480c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H176c-26.5 0-48 21.5-48 48s21.5 48 48 48h352c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H304c-26.5 0-48 21.5-48 48s21.5 48 48 48h224c26.5 0 48-21.5 48-48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
                    {{ $t("home.priceLowToHigh") }}
                  </span>
                  <svg v-if="highConfigSort === 'price_origin,asc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortHighConfig('price_origin,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': highConfigSort === 'price_origin,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-arrow-down-wide-short SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48s21.5-48 48-48h480c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48s21.5-48 48-48h224c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
                    {{ $t("home.priceHighToLow") }}
                  </span>
                  <svg v-if="highConfigSort === 'price_origin,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortHighConfig('sold,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': highConfigSort === 'sold,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-chart-line SVG (thay thế cho fa-fire) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors">
                      <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 301.3l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L320 301.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l112-112z" />
                    </svg>
                    {{ $t("home.bestSelling") }}
                  </span>
                  <svg v-if="highConfigSort === 'sold,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortHighConfig('rating,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': highConfigSort === 'rating,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-star SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M287.9 17.8L354 150.2l146.1 21.3c26.2 3.8 36.7 36 17.7 54.6l-105.7 103 25 145.5c4.5 26.2-23 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.5-46.4-33.7l25-145.5L30.2 226.1c-19-18.6-8.5-50.8 17.7-54.6L194 150.2 260.1 17.8c11.7-23.6 45.6-23.9 57.8 0z" /></svg>
                    {{ $t("home.highRated") }}
                  </span>
                  <svg v-if="highConfigSort === 'rating,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        <p class="text-white/90 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.lightingDesc") }}</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <ProductCard v-for="product in highConfigProducts" :key="product.id" :product="product" />
      </div>
      <!-- xem thêm  -->
      <ButtonViewMore />
    </section>
    <section class="product-accsessory bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.techAccessories") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="relative inline-block group">
            <button type="button" class="flex items-center space-x-1 text-white hover:text-gray-300">
              <span class="text-xs md:text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f]">{{ $t("home.sort") }}</span>
              <svg viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5">
                <defs>
                  <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color: rgba(43, 57, 144, 0.91); stop-opacity: 1" />
                    <stop offset="50%" style="stop-color: #0097cc; stop-opacity: 1" />
                    <stop offset="100%" style="stop-color: #8dc63f; stop-opacity: 1" />
                  </linearGradient>
                </defs>
                <path fill="url(#iconGradient)" d="M151.6 469.6C145.5 476.2 137 480 128 480s-17.5-3.8-23.6-10.4l-88-96c-11.9-13-11.1-33.3 2-45.2s33.3-11.1 45.2 2L96 365.7 96 64c0-17.7 14.3-32 32-32s32 14.3 32 32l0 301.7 32.4-35.4c11.9-13 32.2-13.9 45.2-2s13.9 32.2 2 45.2l-88 96zM320 32l32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-96 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l160 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-160 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 128l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
              </svg>
            </button>
            <div class="absolute top-full right-0 w-full h-6 group-hover:block hidden z-0"></div>
            <div class="absolute right-0 z-50 mt-2 w-48 md:w-64 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-2xl ring-1 ring-black/10 hidden group-hover:block transition-all duration-300 ease-in-out">
              <div class="py-2 px-3 space-y-1 text-xs sm:text-sm md:text-base text-gray-700">
                <button @click="handleSortAccessories('id,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': accessorySort === 'id,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-clock SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.3 0-200-89.7-200-200S145.7 56 256 56s200 89.7 200 200-89.7 200-200 200zm12-200V136c0-6.6-5.4-12-12-12s-12 5.4-12 12v120c0 6.6 5.4 12 12 12h88c6.6 0 12-5.4 12-12s-5.4-12-12-12h-76z" /></svg>
                    {{ $t("home.newest") }}
                  </span>
                  <svg v-if="accessorySort === 'id,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortAccessories('price_origin,asc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': accessorySort === 'price_origin,asc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-arrow-up-wide-short SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80s21.5 48 48 48h480c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H176c-26.5 0-48 21.5-48 48s21.5 48 48 48h352c26.5 0 48-21.5 48-48zm0 176c0-26.5-21.5-48-48-48H304c-26.5 0-48 21.5-48 48s21.5 48 48 48h224c26.5 0 48-21.5 48-48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
                    {{ $t("home.priceLowToHigh") }}
                  </span>
                  <svg v-if="accessorySort === 'price_origin,asc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortAccessories('price_origin,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': accessorySort === 'price_origin,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-arrow-down-wide-short SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M576 432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48s21.5-48 48-48h480c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48s21.5-48 48-48h224c26.5 0 48 21.5 48 48zm0-176c0 26.5-21.5 48-48 48H176c-26.5 0-48-21.5-48-48s21.5-48 48-48h352c26.5 0 48 21.5 48 48z" /></svg>
                    {{ $t("home.priceHighToLow") }}
                  </span>
                  <svg v-if="accessorySort === 'price_origin,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortAccessories('sold,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': accessorySort === 'sold,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-chart-line SVG (thay thế cho fa-fire) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors">
                      <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 301.3l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L320 301.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l112-112z" />
                    </svg>
                    {{ $t("home.bestSelling") }}
                  </span>
                  <svg v-if="accessorySort === 'sold,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
                <button @click="handleSortAccessories('rating,desc')" class="flex items-center justify-between w-full hover:bg-gray-100 px-2 sm:px-3 py-2 rounded transition group/item" :class="{ 'text-sky-600 font-bold': accessorySort === 'rating,desc' }">
                  <span class="flex items-center gap-1 sm:gap-2 group-hover/item:text-sky-600 transition-colors">
                    <!-- fa-star SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 text-xs md:text-sm text-gray-500 group-hover/item:text-sky-600 transition-colors"><path d="M287.9 17.8L354 150.2l146.1 21.3c26.2 3.8 36.7 36 17.7 54.6l-105.7 103 25 145.5c4.5 26.2-23 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.5-46.4-33.7l25-145.5L30.2 226.1c-19-18.6-8.5-50.8 17.7-54.6L194 150.2 260.1 17.8c11.7-23.6 45.6-23.9 57.8 0z" /></svg>
                    {{ $t("home.highRated") }}
                  </span>
                  <svg v-if="accessorySort === 'rating,desc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" class="w-3 h-3 md:w-4 md:h-4 text-sky-500"><path d="M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L160 284.1l119-119c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-136 136c-9.4 9.4-24.6 9.4-33.9 0z" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.popularLightingDesc") }}</p>
      </div>
      <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
        <div class="w-full lg:w-1/5 h-[150px] lg:h-[858px] md:-mx-3 lg:mx-0">
          <div class="relative h-full w-full overflow-hidden shadow-md">
            <NuxtImg :src="introBanner" loading="lazy" :alt="$t('home.introBanner')" class="object-cover w-full h-full" />
            <div class="absolute w-full max-w-[300px] inset-0 bg-black/20 flex items-end justify-center py-4">
              <div class="mx-auto mt-6 hidden lg:block">
                <NuxtLink to="/shop" class="relative flex items-center justify-center px-6 py-2 overflow-hidden font-medium text-md transition-all bg-gradient-to-r from-sky-500 to-cyan-400 rounded-md shadow-lg group hover:scale-105 hover:from-sky-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-sky-400">
                  <span class="absolute top-0 right-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-sky-700 rounded group-hover:-mr-4 group-hover:-mt-4">
                    <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white"></span>
                  </span>
                  <span class="absolute bottom-0 rotate-180 left-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-sky-700 rounded group-hover:-ml-4 group-hover:-mb-4">
                    <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white"></span>
                  </span>
                  <span class="absolute bottom-0 left-0 w-full h-full transition-all duration-500 ease-in-out delay-200 -translate-x-full bg-sky-600 rounded-md group-hover:translate-x-0"></span>
                  <span class="relative text-center text-white transition-colors duration-200 ease-in-out group-hover:text-white drop-shadow">{{ $t("home.explore") }}</span>
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
        <!-- slide sản phẩm -->
        <div class="w-full lg:w-4/5 min-w-0">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 grid-rows-2 gap-4">
            <ProductCard v-for="product in highConfigAccessoryProducts" :key="product.id" :product="product" />
          </div>
        </div>
      </div>
    </section>
    <section class="bg-gradient-to-b from-white to-sky-50 relative rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 items-center gap-6 md:gap-12">
        <div class="space-y-3 md:space-y-6">
          <span class="inline-block bg-sky-100 text-sky-600 font-semibold text-xs md:px-3 px-3 py-1 rounded-full uppercase tracking-wide">{{ $t("home.newExperience") }}</span>
          <h2 class="text-xl md:text-3xl font-extrabold text-gray-800 leading-tight">
            {{ $t("home.smartShopping") }}
            <br class="" />
            <span class="text-sky-600">{{ $t("home.smartConvenient") }}</span>
            💡
          </h2>
          <p class="text-gray-600 text-sm md:text-base leading-relaxed max-w-md">{{ $t("home.discoverThousands") }}</p>
          <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700 mt-2 md:mt-4">
            <li class="flex items-center gap-2">
              <i class="fas fa-shipping-fast text-sky-500"></i>
              {{ $t("home.fast2h") }}
            </li>
            <li class="flex items-center gap-2">
              <i class="fas fa-shield-alt text-emerald-500"></i>
              {{ $t("home.nationwideWarranty") }}
            </li>
            <li class="flex items-center gap-2">
              <i class="fas fa-credit-card text-purple-500"></i>
              {{ $t("home.installment0") }}
            </li>
            <li class="flex items-center gap-2">
              <i class="fas fa-headset text-pink-500"></i>
              {{ $t("home.support247") }}
            </li>
          </ul>
          <div class="max-w-[120px] md:max-w-[180px]">
            <NuxtLink to="/shop" class="relative flex items-center justify-center px-6 py-2 overflow-hidden font-medium text-md transition-all bg-gradient-to-r from-sky-500 to-cyan-400 rounded-md shadow-lg group hover:scale-105 hover:from-sky-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-sky-400">
              <span class="absolute top-0 right-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-sky-700 rounded group-hover:-mr-4 group-hover:-mt-4">
                <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white"></span>
              </span>
              <span class="absolute bottom-0 rotate-180 left-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-sky-700 rounded group-hover:-ml-4 group-hover:-mb-4">
                <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white"></span>
              </span>
              <span class="absolute bottom-0 left-0 w-full h-full transition-all duration-500 ease-in-out delay-200 -translate-x-full bg-sky-600 rounded-md group-hover:translate-x-0"></span>
              <span class="relative text-center text-white transition-colors duration-200 ease-in-out group-hover:text-white drop-shadow text-sm md:text-base">{{ $t("home.explore") }}</span>
            </NuxtLink>
          </div>
        </div>
        <div class="relative">
          <div class="relative z-10">
            <img src="/images/slide/buy_smart_2.jpg" loading="lazy" alt="Mua sắm công nghệ" class="w-full max-w-lg mx-auto rounded-3xl shadow-xl" />
          </div>
          <div class="absolute top-0 z-20 left-0 -translate-x-4 -translate-y-4 bg-white shadow px-4 py-2 rounded-full text-sm font-medium text-sky-600 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-400"></i>
            {{ $t("home.flashSaleToday") }}
          </div>
          <div class="absolute -inset-2 bg-sky-400/20 blur-2xl rounded-3xl z-0"></div>
        </div>
      </div>
    </section>
    <CollectionProduct />
    <section class="relative bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="text-center md:mb-8 mb-4 relative">
        <div class="inline-block md:mb-4 mb-2">
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#8dc63f] via-[#0097cc] to-[rgba(43,57,144,0.91)] text-sm font-semibold tracking-wider uppercase">{{ $t("home.competitiveAdvantage") }}</span>
        </div>
        <h2 class="text-2xl md:text-4xl font-black text-gray-700 mb-2 leading-tight">
          {{ $t("home.whyChooseUs") }}
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] animate-glow">{{ $t("home.us") }}</span>
          <span class="text-2xl md:text-4xl">?</span>
        </h2>
        <p class="text-sm text-gray-600 max-w-3xl mx-auto leading-relaxed">{{ $t("home.experienceDifference") }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 md:gap-8">
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-3xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-white/50">
            <div class="flex flex-col items-center text-center">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-500 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500 shadow-lg">
                  <i class="fas fa-medal text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-pulse"></div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors duration-300">{{ $t("home.qualityGuaranteed") }}</h3>
              <p class="text-gray-600 leading-relaxed text-sm">{{ $t("home.qualityDesc") }}</p>
            </div>
          </div>
        </div>
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-white/50">
            <div class="flex flex-col items-center text-center">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 via-green-600 to-teal-500 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500 shadow-lg">
                  <i class="fas fa-shipping-fast text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="absolute -top-1 -right-1 flex">
                  <div class="w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                  <div class="w-3 h-3 bg-green-400 rounded-full absolute animate-pulse"></div>
                </div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-emerald-600 transition-colors duration-300">{{ $t("home.fastDelivery") }}</h3>
              <p class="text-gray-600 leading-relaxed text-sm">{{ $t("home.deliveryDesc") }}</p>
            </div>
          </div>
        </div>
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-white/50">
            <div class="flex flex-col items-center text-center">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 via-violet-600 to-pink-500 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500 shadow-lg">
                  <i class="fas fa-undo-alt text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="absolute -bottom-2 -left-2 w-4 h-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-bounce"></div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-purple-600 transition-colors duration-300">{{ $t("home.easyExchange") }}</h3>
              <p class="text-gray-600 leading-relaxed text-sm">{{ $t("home.exchangeDesc") }}</p>
            </div>
          </div>
        </div>
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-3xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-white/50">
            <div class="flex flex-col items-center text-center">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500 shadow-lg">
                  <i class="fas fa-heart text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="absolute -top-1 -left-1 w-5 h-5 border-2 border-yellow-400 rounded-full animate-spin"></div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-orange-600 transition-colors duration-300">{{ $t("home.topTrust") }}</h3>
              <p class="text-gray-600 leading-relaxed text-sm">{{ $t("home.trustDesc") }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center md:mt-0 mt-1">
        <div class="relative inline-block">
          <div class="absolute inset-0 bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] rounded-full animate-pulse"></div>
          <NuxtLink to="/shop" class="relative inline-flex items-center md:px-6 md:py-2 px-3 py-1 text-white font-bold text-sm md:text-base rounded-full hover:shadow-xl transition-all duration-500 transform hover:scale-110 group overflow-hidden">
            <span class="absolute inset-0 bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
            <span class="relative flex items-center">
              {{ $t("home.shopNow") }}
              <i class="fas fa-arrow-right ml-3 group-hover:translate-x-2 transition-transform duration-300"></i>
            </span>
          </NuxtLink>
        </div>
      </div>
    </section>
    <!-- partners -->
    <Partner />
    <!-- posts -->
    <section class="bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex justify-between items-center space-x-2">
          <div class="head-section-product">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.featuredEvents") }}</h2>
            <div class="h-1 max-w-48 my-1 md:my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="group flex items-center space-x-2">
            <NuxtLink to="/post" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium text-sm md:text-base after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 group-hover:after:w-full">{{ $t("home.viewAll") }}</NuxtLink>
          </div>
        </div>
        <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.featuredEventsDesc") }}</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 md:gap-6 gap-4">
        <Splide :options="postFeaturedOptions" id="featured-posts">
          <SplideSlide v-for="(item, index) in (homePosts.length ? homePosts.slice(0, 1) : postFeaturedItems)" :key="`main-${index}`" class="bg-gray-100 rounded-xl overflow-hidden shadow-md">
            <NuxtLink :to="item.slug ? `/post/${item.slug}` : (item.link || '/post')" class="block">
              <img :src="item.image?.url || item.image?.path || item.image || IMAGEDEFAULT" loading="lazy" :alt="item.name || item.title" class="w-full object-cover max-h-[300px]" />
              <div class="relative p-4">
                <span class="absolute top-0 left-0 -translate-y-10 translate-x-4 inline-block bg-sky-100/80 text-sky-600 font-semibold text-xs px-4 py-1 rounded-full uppercase shadow-lg tracking-wide">{{ (item.postCatalogues && item.postCatalogues[0]?.name) || item.category || $t("home.technology") }}</span>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 hover:text-cyan-600 transition-colors md:mb-2 mb-1 line-clamp-1">{{ item.name || item.title }}</h3>
                <p class="text-gray-600 hover:text-gray-500 transition-colors text-sm md:mb-4 mb-2 line-clamp-2">{{ item.description }}</p>
                <div class="text-right hover:scale-105 transition-transform duration-300">
                  <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium text-sm md:text-base">{{ $t("home.readMore") }}</span>
                </div>
              </div>
            </NuxtLink>
          </SplideSlide>
        </Splide>

        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-4">
          <div v-for="post in (homePosts.length > 1 ? homePosts.slice(1, 5) : postFeaturedItems.slice(0, 4))" :key="post.id || post.title" class="bg-gray-100 rounded-lg shadow-lg overflow-hidden flex flex-col justify-between">
            <NuxtLink :to="post.slug ? `/post/${post.slug}` : (post.link || '/post')" class="block cursor-pointer h-full">
              <img :src="post.image?.url || post.image?.path || post.image || IMAGEDEFAULT" loading="lazy" :alt="post.name || post.title" class="w-full h-36 object-cover" />
              <div class="relative p-3">
                <span class="absolute top-0 left-0 -translate-y-8 translate-x-4 inline-block bg-sky-100/80 text-sky-600 font-semibold text-[8px] md:text-[10px] px-4 py-1 rounded-full uppercase shadow-lg tracking-wide">{{ (post.postCatalogues && post.postCatalogues[0]?.name) || post.category || $t("home.technology") }}</span>
                <h3 class="text-sm md:text-base font-semibold text-gray-800 hover:text-cyan-600 transition-colors line-clamp-1 mt-1">{{ post.name || post.title }}</h3>
                <p class="text-gray-600 hover:text-gray-500 transition-colors text-xs line-clamp-2 mt-1">{{ post.description }}</p>
              </div>
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import CommitMent from "~/components/ui/CommitMent.vue";
import ProductCard from "~/components/features/ProductCard.vue";
import CollectionProduct from "~/components/features/Collection.vue";
import ButtonViewMore from "~/components/ui/Button.vue";
import DataState from "~/components/ui/DataState.vue";
import { useHomeSlider } from "~/composables/useHomeSlider";
import { IMAGEDEFAULT } from "~/utils/constants";

// Import auth composable
import { useAuth } from "~/composables/auth/useAuth";
import Partner from "~/components/ui/Partner.vue";

// Import product and post composable / service
import { useProduct } from "~/composables/product/useProduct";
import { productService } from "~/services/product/product.service";
import { postService } from "~/services/post/post.service";

const {
  mainOptions,
  thumbOptions,
  rightVirticalOptions,
  postFeaturedOptions,
  partnerOptions,
  mainItems,
  thumbnailItems,
  rightVirticalItems,
  introBanner,
  postFeaturedItems,
  partnerItems,
  mainSlider,
  thumbSlider,
  partnerSlider,
} = useHomeSlider();

const { isAuthenticated, user } = useAuth();

// Product data for home sections
const { list: featuredProducts, isLoading: featuredProductsLoading, fetchList } = useProduct();

const superSaleProducts = ref([]);
const bestSellerProducts = ref([]);
const highConfigProducts = ref([]);
const highConfigAccessoryProducts = ref([]);
const homePosts = ref([]);

const highConfigSort = ref("price_origin,desc");
const accessorySort = ref("id,desc");

const handleSortHighConfig = async (sortValue) => {
  highConfigSort.value = sortValue;
  try {
    const res = await productService.pagination({ perpage: 10, sort: sortValue });
    highConfigProducts.value = res?.data?.data || [];
  } catch (error) {
    console.error("Error sorting high config products:", error);
  }
};

const handleSortAccessories = async (sortValue) => {
  accessorySort.value = sortValue;
  try {
    const res = await productService.pagination({ perpage: 8, product_catalogue_id: 6, sort: sortValue });
    highConfigAccessoryProducts.value = (res?.data?.data || []).slice(0, 8);
  } catch (error) {
    console.error("Error sorting accessory products:", error);
  }
};

// Fetch featured and section products on page load
onMounted(async () => {
  try {
    const results = await Promise.allSettled([
      fetchList("featuredLimit"),
      productService.pagination({ perpage: 10, is_hot: 1, sort: "id,asc" }),
      productService.pagination({ perpage: 10, sort: "sold,desc" }),
      productService.pagination({ perpage: 10, sort: highConfigSort.value }),
      productService.pagination({ perpage: 8, product_catalogue_id: 6, sort: accessorySort.value }),
      postService.pagination({ publish: 2, perpage: 5, sort: "id,desc" }),
    ]);

    const superSaleRes = results[1].status === "fulfilled" ? results[1].value : null;
    const bestSellerRes = results[2].status === "fulfilled" ? results[2].value : null;
    const highConfigRes = results[3].status === "fulfilled" ? results[3].value : null;
    const accessoryRes = results[4].status === "fulfilled" ? results[4].value : null;
    const postRes = results[5].status === "fulfilled" ? results[5].value : null;

    superSaleProducts.value = superSaleRes?.data?.data || [];
    bestSellerProducts.value = bestSellerRes?.data?.data || [];
    highConfigProducts.value = highConfigRes?.data?.data || [];
    highConfigAccessoryProducts.value = (accessoryRes?.data?.data || []).slice(0, 8);
    homePosts.value = postRes?.data?.data || [];
  } catch (error) {
    console.error("Error loading home page data:", error);
  }
});

definePageMeta({
  layout: "client",
});
</script>

<style scoped></style>
