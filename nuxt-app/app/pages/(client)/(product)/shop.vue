<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4 md:mb-8 mb-4">
      <div>
        <Splide :options="bannerShopTopLeftOptions" id="banner-shop-top-left" aria-label="Banner Left Slider">
          <SplideSlide>
            <NuxtImg src="/assets/images/slide/banner-slide-shop-top_5.webp" alt="Banner 1A" class="w-full h-48 object-cover rounded-md shadow" loading="lazy" />
          </SplideSlide>
          <SplideSlide>
            <NuxtImg src="/assets/images/slide/banner-slide-shop-top_6.webp" alt="Banner 1B" class="w-full h-48 object-cover rounded-md shadow" loading="lazy" />
          </SplideSlide>
        </Splide>
      </div>
      <div>
        <Splide :options="bannerShopTopRightOptions" id="banner-shop-top-right" aria-label="Banner Right Slider">
          <SplideSlide>
            <NuxtImg src="/assets/images/slide/banner-slide-shop-top_7.webp" alt="Banner 2A" class="w-full h-48 object-cover rounded-md shadow" loading="lazy" />
          </SplideSlide>
          <SplideSlide>
            <NuxtImg src="/assets/images/slide/banner-slide-shop-top_8.webp" alt="Banner 2B" class="w-full h-48 object-cover rounded-md shadow" loading="lazy" />
          </SplideSlide>
        </Splide>
      </div>
    </section>
    <Partner />
    <!-- Divider -->
    <div class="w-full max-w-7xl mx-auto py-2 mb-5">
      <div class="h-px w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
    </div>
    <!-- Category -->
    <section class="shop-category bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4">
      <div class="mb-4 md:mb-8">
        <div class="flex flex-wrap justify-between items-center gap-2">
          <div>
            <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg md:text-2xl font-bold">Danh mục</h2>
            <div class="h-1 max-w-64 my-2 relative">
              <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
            </div>
          </div>
          <a href="#" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 hover:after:w-full">Xem tất cả</a>
        </div>
        <p class="text-gray-600 mt-1 max-w-xl text-xs md:text-sm">Các thiết bị chiếu sáng được ưa chuộng nhất tại Tech mart Store, nâng cao trải nghiệm sống của hàng ngàn khách hàng.</p>
      </div>
      <Splide :options="miniCategoryOptions" id="mini-category-slider">
        <SplideSlide v-for="cat in 10" :key="cat" class="shadow-lg">
          <a href="#" class="block">
            <div class="mini-category-item" style="height: 180px; width: 120px">
              <NuxtImg :src="`/assets/images/slide/shop-category-${cat}.webp`" loading="lazy" class="rounded-lg w-full h-[120px] object-contain" :alt="`Category ${cat}`" />
              <div class="mini-category-text"><span class="line-clamp-2">Điện thoại thông minh</span></div>
            </div>
          </a>
        </SplideSlide>
      </Splide>
    </section>
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
        <aside class="product-filter flex-shrink-0 w-full md:w-[280px] mb-3 md:mb-0 sticky top-4">
          <div class="bg-white rounded-md shadow p-3 md:p-4 overflow-y-auto max-h-[126vh] custom-scrollbar">
            <h2 class="text-transparent text-start bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg sm:text-md font-bold pb-2">Bộ lọc sản phẩm</h2>
            <div class="filter-scroll"></div>
            <div id="accordion-flush" data-accordion="open" data-active-classes="bg-white text-gray-900" data-inactive-classes="text-gray-500">
              <!-- Thương hiệu -->
              <div class="border-b border-gray-200 mr-2">
                <h2 id="accordion-flush-heading-1">
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-1">
                    <span>Thương hiệu</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-1" class="block" aria-labelledby="accordion-flush-heading-1">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="brand" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Apple</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="brand" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Samsung</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="brand" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">ASUS</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="border-b border-gray-200 mr-2">
                <h2>
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-category">
                    <span>Danh mục</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-category" class="block">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="category" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Laptop</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="category" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Điện thoại</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="category" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Tablet</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Ổ cứng -->
              <div class="border-b border-gray-200 mr-2">
                <h2>
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-storage">
                    <span>Ổ cứng</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-storage" class="block">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="storage" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">256GB SSD</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="storage" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">512GB SSD</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="storage" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">1TB HDD</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- RAM -->
              <div class="border-b border-gray-200 mr-2">
                <h2>
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-ram">
                    <span>RAM</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-ram" class="block">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="ram" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">8GB</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="ram" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">16GB</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="ram" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">32GB</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Màn hình -->
              <div class="border-b border-gray-200 mr-2">
                <h2>
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-screen">
                    <span>Màn hình</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-screen" class="block">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="screen" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">13 inch</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="screen" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">15.6 inch</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="screen" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">4K UHD</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Tính năng đặc biệt -->
              <div class="border-b border-gray-200 mr-2">
                <h2>
                  <button type="button" class="flex flex-warp items-center justify-between w-full py-2 font-medium text-gray-500" data-accordion-target="#accordion-flush-body-features">
                    <span>Tính năng đặc biệt</span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5 5 1 1 5" />
                    </svg>
                  </button>
                </h2>
                <div id="accordion-flush-body-features" class="block">
                  <div class="py-2 space-y-2">
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="feature" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Tích hợp AI</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="feature" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Chống nước IP68</span>
                    </label>
                    <label class="checkbox-container">
                      <input class="custom-checkbox" name="feature" type="checkbox" />
                      <span class="checkmark"></span>
                      <span class="text-gray-300">Bảo mật vân tay</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </aside>
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
// import ViewMore from "../components/ui/button/ViewMore.vue";
// import Filter from "../components/ui/product/FilterBgWhite.vue";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import CommitMent from "~/components/client/ui/CommitMent.vue";
import Partner from "~/components/client/ui/Partner.vue";
// import ProductCard from "../components/ui/product/ProductCard.vue";
// import ProductCardSizeMall from "../components/ui/product/ProductCardSizeMall.vue";

const { bannerShopTopLeftOptions, bannerShopTopRightOptions, partnerShopOptions, miniCategoryOptions, beforeShopProductOptions, productSliderOptions } = useShopSlider();

// Partner component sẽ tự fetch brand khi không truyền logos

const posts = [
  { img: "/assets/images/posts/post-home-1.jpg", alt: "post1", title: "Trải nghiệm nhanh Apple CarPlay phiên bản mới trên iOS 26" },
  { img: "/assets/images/posts/post-home-2.jpg", alt: "post2", title: "Anh em không thích hiệu ứng trong suốt trên iOS 26? Đây là cách tắt nó đi" },
];

const breadcrumbs = [{ label: "shop", href: "/shop" }];

definePageMeta({ layout: "client" });
</script>
