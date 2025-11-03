<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="my-2">
      <div v-if="isLoading" class="text-gray-500 text-sm text-center">Đang tải...</div>
      <div v-else>
        <div v-for="(bannerTop, index) in bannerShopTops" :key="bannerTop.id" class="overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div v-for="(imageUrl, i) in resolveAlbum(bannerTop.album || [])" :key="i" class="mb-2 last:mb-0">
            <NuxtImg :src="imageUrl" :alt="`${bannerTop.name || 'Banner'} ${index + 1}-${i + 1}`" class="w-full h-full object-contain" />
          </div>
        </div>
      </div>
    </section>
    <Partner />
    <div class="w-full max-w-7xl mx-auto py-2 mb-3">
      <div class="h-px w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
    </div>
    <Category description="Khám phá các danh mục sản phẩm đa dạng tại Tech Mart Store – từ thiết bị điện tử, gia dụng thông minh đến phụ kiện hiện đại, đáp ứng nhu cầu học tập, làm việc và giải trí của bạn." :items="(productCatalogueList as any)" basePath="/product/category" />
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
          <div v-for="bannerSeason in bannerProductSeason" :key="bannerSeason.id" class="relative h-full w-full overflow-hidden rounded-lg">
            <div v-for="(imageUrl, i) in resolveAlbum(bannerSeason.album || [])" :key="i" class="relative h-full w-full overflow-hidden rounded-lg">
              <NuxtImg :src="imageUrl" :alt="`${bannerSeason.name || 'Banner Season'} ${i + 1}`" loading="lazy" class="object-cover w-full h-full rounded-lg" />
            </div>
            <div class="absolute w-full inset-0 bg-black/20 flex items-end justify-center py-4">
              <a href="#" class="relative flex items-center justify-center md:px-6 md:py-2 py-1 px-3 font-medium text-sm md:text-base transition-all bg-gradient-to-r from-sky-500 to-cyan-400 rounded-md shadow-lg hover:scale-105 hover:from-sky-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-sky-400 text-white">Khám phá</a>
            </div>
          </div>
        </div>
        <div class="md:col-span-3">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <ProductCard v-for="product in summerProducts" :key="product.id" :product="product" />
          </div>
        </div>
      </div>
    </section>

    <section class="product-filter bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 md:mb-8 mb-4">
      <div class="flex flex-wrap md:flex-nowrap gap-3 md:gap-4">
        <FilterBar @update:attributes="handleAttributesChange" @update:price="handlePriceChange" @update:brand="handleBrandChange" />
        <div class="flex-1 w-full">
          <div class="flex justify-between items-center mb-3 md:mb-4">
            <span class="text-xs md:text-sm">
              Hiển thị
              <span class="text-sky-400 underline">{{ totalProducts }} kết quả</span>
            </span>
            <FilterSort :sort="filters.sort" :setSort="setSort" />
          </div>
          <div class="relative" id="product-shop-slide">
            <template v-if="totalProducts > 0 && chunks.length">
              <Splide :options="sliderOptions" ref="splideRef">
                <SplideSlide v-for="(chunk, idx) in chunks" :key="idx">
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                    <ProductCard v-for="product in chunk" :key="product.id" :product="product" />
                  </div>
                </SplideSlide>
              </Splide>
              <button v-if="totalProducts > 8" @click="prev" :disabled="isLoading" class="absolute left-0 top-1/2 -translate-y-1/2 flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 shadow hover:bg-gray-200 disabled:opacity-50 transition-all">
                <i class="fa-solid fa-chevron-left"></i>
              </button>
              <button v-if="totalProducts > 8" @click="next" :disabled="isLoading" class="absolute right-0 top-1/2 -translate-y-1/2 flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-sky-500 to-cyan-400 text-white shadow hover:from-sky-600 hover:to-cyan-500 disabled:opacity-50 transition-all">
                <i v-if="isLoading" class="fa-solid fa-circle-notch fa-spin"></i>
                <i v-else class="fa-solid fa-chevron-right"></i>
              </button>
            </template>
            <template v-else>
              <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500">
                <NuxtImg :src="EMPTYBOX" alt="" class="object-contain" />
                <p class="text-sm text-center">Không có sản phẩm phù hợp với bộ lọc bạn chọn.</p>
              </div>
            </template>
          </div>
        </div>
      </div>
    </section>

    <CommitMent />

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
              <NuxtLink :to="TOPOST" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-sm font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 hover:after:w-full">Xem tất cả</NuxtLink>
            </div>
            <div class="space-y-4 mt-8 relative border-l border-dashed border-sky-500 pl-4">
              <NuxtLink v-for="post in posts" :key="post.id" :to="`${TOPOST}/${post.slug}`">
                <div class="flex items-start mb-3 gap-2 hover:bg-gray-100/80 transition cursor-pointer group">
                  <NuxtImg :src="post.image?.url || IMAGEDEFAULT" :alt="post.name" class="w-32 h-20 object-cover rounded-md flex-shrink-0" loading="lazy" />
                  <div class="flex-1">
                    <p class="text-sm font-medium hover:text-sky-400 transition line-clamp-4">{{ post.name }}</p>
                  </div>
                </div>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import CommitMent from "~/components/client/ui/CommitMent.vue";
import Partner from "~/components/client/ui/Partner.vue";
import { useShop } from "~/composables/client/shop/useShop";
import Category from "~/components/client/ui/Category.vue";
import FilterBar from "~/components/client/ui/FilterBar.vue";
import ProductCard from "~/components/client/features/ProductCard.vue";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import FilterSort from "~/components/client/features/FilterSort.vue";
import { EMPTYBOX, IMAGEDEFAULT, TOPOST } from "#imports";
import { resolveAlbum } from "~/utils/helpers";

const { banners, catalogues, products, summerProducts, posts, bannerSeason, totalProducts, hasMore, isLoading, initializeData, loadMore, filters, setSort, setFilter } = useShop();

initializeData();

const resetSlide = () => nextTick(() => splideRef.value?.splide?.go(0));

const handleAttributesChange = async (newAttributes: Record<string, number[]>) => {
  await setFilter("attributes", newAttributes);
  resetSlide();
};

const handlePriceChange = async (newRange: number[]) => {
  await setFilter("price", newRange);
  resetSlide();
};

const handleBrandChange = async (brandId: number | null) => {
  await setFilter("brand", brandId);
  resetSlide();
};

const bannerShopTops = banners;
const productCatalogueList = catalogues;
const bannerProductSeason = bannerSeason;
const hasMoreProducts = hasMore;
const loadMoreProducts = loadMore;
const isInitialized = computed(() => !!banners.value.length && !!products.value.length);

const sliderOptions = {
  type: "slide",
  perPage: 1,
  arrows: false,
  pagination: false,
  drag: true,
  speed: 600,
  easing: "cubic-bezier(0.25, 1, 0.5, 1)",
};

const splideRef = ref<any>(null);

const chunks = computed(() => {
  const result = [];
  for (let i = 0; i < products.value.length; i += 8) {
    result.push(products.value.slice(i, i + 8));
  }
  return result;
});

const isLastSlide = computed(() => {
  const splide = splideRef.value?.splide;
  return splide?.index === chunks.value.length - 1;
});

const prev = () => splideRef.value?.splide?.go("-");
const next = async () => {
  if (isLastSlide.value && hasMoreProducts.value) {
    await loadMoreProducts();
    await nextTick();
  }
  splideRef.value?.splide?.go("+");
};

const breadcrumbs = [{ label: "shop", href: "/shop" }];

definePageMeta({ layout: "client" });
</script>
<style>
#product-shop-slide button {
  position: absolute;
  top: 50%;
  transform: translateY(-57%);
  height: 3.5em;
  width: 2em;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  outline: none;
  color: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
}

#product-shop-slide button:first-of-type {
  left: 0;
  background: linear-gradient(to right, #00a3b8, #0097cc);
  border-radius: 0;
  border-top-right-radius: 14px;
  border-bottom-right-radius: 14px;
}

#product-shop-slide button:last-of-type {
  right: 0;
  background: linear-gradient(to right, #0097cc, #00c3b5);
  border-radius: 0;
  border-top-left-radius: 14px;
  border-bottom-left-radius: 14px;
}

#product-shop-slide button:hover {
  transform: translateY(-50%) 0;
  filter: brightness(1.1);
}

#product-shop-slide button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: translateY(-50%) 0;
}

#product-shop-slide button i {
  font-size: 1.2em;
  color: #fff;
}
</style>
