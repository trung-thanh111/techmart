<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="my-2">
      <div v-if="isLoading" class="text-gray-500 text-sm text-center">Đang tải...</div>
      <div v-else>
        <div v-for="(banner, index) in shopBanners" :key="banner.id" class="overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div v-for="(image, i) in banner.album" :key="i" class="mb-2 last:mb-0">
            <NuxtImg :src="getBannerImageUrl(image.path)" :alt="`${banner.name || 'Banner'} ${index + 1}-${i + 1}`" class="w-full h-full object-contain" />
          </div>
        </div>
      </div>
    </section>

    <Partner />
    <!-- Divider -->
    <div class="w-full max-w-7xl mx-auto py-2 mb-5">
      <div class="h-px w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
    </div>
  
    <Category description="Khám phá các danh mục sản phẩm đa dạng tại Tech Mart Store – từ thiết bị điện tử, gia dụng thông minh đến phụ kiện hiện đại, đáp ứng nhu cầu học tập, làm việc và giải trí của bạn." :items="(productCatalogueList)" basePath="/product/category" />

    <!-- Before shop product -->
    <section class="product-accsessory bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4">
      <div class="mb-4 flex flex-wrap justify-between items-center gap-2">
        <div>
          <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg md:text-2xl font-bold">Khởi động mùa hè cùng</h2>
          <div class="h-1 max-w-72 my-2 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
          </div>
        </div>
        <a href="#" class="text-sm md:text-base relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 hover:after:w-full">Xem tất cả</a>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
          <div class="relative h-full w-full overflow-hidden rounded-lg">
            <NuxtImg src="/assets/images/slide/Event_ITC Asia 2025.webp" loading="lazy" alt="Banner giới thiệu" class="object-cover w-full h-full rounded-lg" />
            <div class="absolute w-full inset-0 bg-black/20 flex items-end justify-center py-4">
              <a href="#" class="relative flex items-center justify-center md:px-6 md:py-2 py-1 px-3 font-medium text-sm md:text-base transition-all bg-gradient-to-r from-sky-500 to-cyan-400 rounded-md shadow-lg hover:scale-105 hover:from-sky-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-sky-400 text-white">Khám phá</a>
            </div>
          </div>
        </div>
        <div class="md:col-span-3">
          <Splide :options="beforeShopProductOptions" id="before-shop-product-slider">
            <SplideSlide>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- <ProductCard v-for="product in 3" :key="product.id || product" :product="product" /> -->
                <div v-for="product in 3" :key="product.id || product" class="bg-gray-100 h-32 rounded-md flex items-center justify-center">Product Card</div>
              </div>
            </SplideSlide>
          </Splide>
        </div>
      </div>
    </section>
    <!-- Shop product -->
    <section class="product-filter bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 md:mb-8 mb-4">
      <div class="flex flex-wrap md:flex-nowrap gap-3 md:gap-4">
        <FilterBar/>
        <div class="flex-1 w-full">
          <div class="flex justify-between items-center mb-3 md:mb-4">
            <span class="text-xs md:text-sm">
              Hiển thị
              <span class="text-sky-400 underline">300 kết quả</span>
            </span>
            <Filter />
          </div>
          <Splide :options="productSliderOptions" id="product-slider" class="overflow-y-auto max-h-[90vh] lg:overflow-visible lg:max-h-none">
            <SplideSlide v-for="slide in 2" :key="slide">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                <!-- <ProductCardSizeMall v-for="product in 8" :key="product.id" :product="product" /> -->
                <div v-for="product in 8" :key="product.id" class="bg-gray-100 h-40 rounded-md flex items-center justify-center">Product Card {{ product }}</div>
              </div>
            </SplideSlide>
          </Splide>
        </div>
      </div>
    </section>
    <CommitMent />
    <!-- About & Posts -->
    <section class="product-accsessory">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4">
          <div class="flex justify-between items-center mb-2">
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg font-bold">Giới thiệu sản phẩm</h2>
            <div class="h-1 max-w-64 my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <div class="content-about-product max-h-[800px] overflow-hidden">
            <!-- ... giữ nguyên nội dung giới thiệu ... -->
            <ViewMore />
          </div>
        </div>
        <div class="lg:col-span-4 min-w-0 bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4">
          <div class="posts-shop">
            <div class="flex justify-between items-center mb-2">
              <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg font-bold">Sự kiện sản phẩm</h2>
              <a href="#" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-sm font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 hover:after:w-full">Xem tất cả</a>
            </div>
            <div class="space-y-4 mt-8 relative border-l border-dashed border-sky-500 pl-4">
              <a v-for="post in posts" :key="post.img" href="#">
                <div class="flex items-start mb-3 gap-2 hover:bg-gray-100/80 transition cursor-pointer group">
                  <NuxtImg :src="post.img" :alt="post.alt" class="w-32 h-20 object-cover rounded-md flex-shrink-0" loading="lazy" />
                  <div class="flex-1">
                    <p class="text-sm font-medium hover:text-sky-400 transition line-clamp-4">{{ post.title }}</p>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup>
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import CommitMent from "~/components/client/ui/CommitMent.vue";
import Partner from "~/components/client/ui/Partner.vue";
import { useShop } from "~/composables/client/shop/useShop";
import Category from "~/components/client/ui/Category.vue";
import FilterBar from "~/components/client/ui/FilterBar.vue";

const { miniCategoryOptions, beforeShopProductOptions, productSliderOptions } = useShopSlider();
const { shopBanners, getBannerImageUrl, productCatalogueList } = useShop();

const posts = [
  { img: "/assets/images/posts/post-home-1.jpg", alt: "post1", title: "Trải nghiệm nhanh Apple CarPlay phiên bản mới trên iOS 26" },
  { img: "/assets/images/posts/post-home-2.jpg", alt: "post2", title: "Anh em không thích hiệu ứng trong suốt trên iOS 26? Đây là cách tắt nó đi" },
];

const breadcrumbs = [{ label: "shop", href: "/shop" }];

definePageMeta({ layout: "client" });
</script>
