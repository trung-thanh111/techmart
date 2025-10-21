<template>
  <div>
    <div class="product-item relative w-full h-[420px] bg-white shadow-md rounded-lg overflow-hidden group">
      <div class="absolute top-1 -left-8 w-24 text-center text-white text-xs font-medium py-1 shadow-md z-20 transform -rotate-45 bg-gradient-to-r from-red-400 via-red-500 to-green-300 rounded-md opacity-90">- 40%</div>
      <div class="absolute top-0 right-0 h-full w-10 flex flex-col justify-start items-center gap-1 z-30 translate-x-full group-hover:translate-x-0 transition-all duration-300 ease-in-out bg-gradient-to-l from-white via-white/50 to-transparent rounded-l-md backdrop-blur-sm">
        <a href="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fa-regular fa-heart"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-cart-plus"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-random"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-eye"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-share-alt"></i>
        </a>
      </div>

      <div class="h-full flex flex-col relative overflow-hidden z-10">
        <div class="absolute inset-0 z-10 group-hover:backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300 pointer-events-none rounded-lg"></div>
        <div class="relative h-[260px] overflow-hidden z-0">
          <img :src="product_main" loading="lazy" :alt="$t('client.productCard.mainImage')" class="w-full h-full object-contain transition-opacity duration-300 group-hover:opacity-0" />
          <img :src="product_sub" loading="lazy" :alt="$t('client.productCard.subImage')" class="w-full h-full object-contain absolute top-0 left-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        </div>
        <div class="px-2 flex-grow flex flex-col justify-between z-0">
          <h3 class="text-base font-semibold text-gray-500 h-10 overflow-hidden leading-5 line-clamp-2">{{ $t("client.productCard.sampleProductName") }}</h3>
          <div class="flex items-center gap-2 mt-2">
            <span class="text-red-600 font-bold text-md">250.800 ₫</span>
            <span class="text-gray-400 line-through text-xs">418.000 ₫</span>
          </div>
          <div class="flex items-center justify-between space-x-4 text-[12px] py-2 text-gray-200">
            <div class="flex items-center space-x-1 text-emerald-400">
              <i class="fas fa-award"></i>
              <span class="hidden md:inline lg:inline text-xs">{{ $t("client.productCard.bestSeller") }}</span>
            </div>
            <div class="flex items-center space-x-1 text-sky-400">
              <i class="fas fa-wallet"></i>
              <span class="hidden md:inline lg:inline text-xs">{{ $t("client.productCard.goodPrice") }}</span>
            </div>
            <div class="flex items-center space-x-1 text-yellow-400">
              <i class="fas fa-fire"></i>
              <span class="hidden md:inline lg:inline text-xs">{{ $t("client.productCard.hotSale") }}</span>
            </div>
          </div>
          <div class="py-2">
            <span class="inline-flex items-center bg-sky-50 rounded-sm px-2 py-1 text-xs text-sky-700">{{ $t("client.productCard.warranty12Months") }}</span>
          </div>
          <div class="w-full max-w-8xl mx-auto">
            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
          </div>
          <div class="flex justify-between items-center text-xs text-yellow-500 py-2 gap-1">
            <div class="flex">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star-half-alt"></i>
              <i class="far fa-star"></i>
            </div>
            <span class="text-gray-400 ml-1">(124)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import product_main from "~/assets/client/images/products/iphone-16-pro-max.webp";
import product_sub from "~/assets/client/images/products/iphone-16-pro-max-3.webp";
</script>
