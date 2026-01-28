<template>
  <div>
    <div class="product-item relative w-full h-[420px] bg-white shadow-md rounded-lg overflow-hidden group">
      <div class="absolute top-1 -left-8 w-24 text-center text-white text-[10px] py-0.5 font-medium shadow-md z-20 transform -rotate-45 bg-gradient-to-r from-red-400 via-red-500 to-green-300 rounded-md opacity-90">- 40%</div>
      <div class="absolute top-0 right-0 h-full w-10 flex flex-col justify-start items-center gap-1 z-30 translate-x-full group-hover:translate-x-0 transition-all duration-300 ease-in-out bg-gradient-to-l from-white via-white/50 to-transparent rounded-l-md backdrop-blur-sm">
        <NuxtLink to="" @click.prevent="handleToggleWishlist" class="text-gray-400 hover:text-red-500 p-2 transition duration-200">
          <i :class="isInWishlist ? 'fas fa-heart text-red-500' : 'fa-regular fa-heart'"></i>
        </NuxtLink>
        <NuxtLink to="#" @click.prevent="handleAddToCart" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200" :title="$t('cart.addToCart')">
          <i class="fas fa-cart-plus"></i>
        </NuxtLink>
        <NuxtLink to="" @click.prevent="handleAddToCompare" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-random"></i>
        </NuxtLink>
        <NuxtLink :to="`/product/${product?.slug}`" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-eye"></i>
        </NuxtLink>
        <NuxtLink to="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-share-alt"></i>
        </NuxtLink>
      </div>

      <div class="h-full flex flex-col relative overflow-hidden z-10">
        <div class="absolute z-10 group-hover:backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300 pointer-events-none rounded-lg"></div>
        <div class="relative h-[260px] overflow-hidden z-0">
          <NuxtImg :src="mainImage" loading="lazy" :alt="product?.name || $t('productCard.mainImage')" class="w-full h-full object-contain transition-opacity duration-300 group-hover:opacity-0" />
          <NuxtImg v-if="subImage" :src="subImage" loading="lazy" :alt="product?.name || $t('productCard.subImage')" class="w-full h-full object-contain absolute top-0 left-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        </div>
        <div class="px-1 flex-grow flex flex-col justify-between z-0">
          <h3 class="text-sm font-medium text-gray-500 h-10 overflow-hidden leading-5 line-clamp-2">
            {{ product?.name || $t("productCard.sampleProductName") }}
          </h3>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-red-600 font-semibold">{{ formatPrice(currentPrice) }}</span>
            <span v-if="originalPrice > currentPrice" class="text-gray-400 line-through text-xs">
              {{ formatPrice(originalPrice) }}
            </span>
          </div>
          <div class="flex flex-wrap justify-between items-center gap-2 mt-1 text-[12px] sm:text-[10px] text-gray-500 min-h-[28px]">
            <div class="flex items-center gap-1 text-emerald-400">
              <i class="fas fa-award"></i>
              <span class="hidden sm:inline">{{ $t("productCard.bestSeller") }}</span>
            </div>
            <div class="flex items-center gap-1 text-sky-400">
              <i class="fas fa-wallet"></i>
              <span class="hidden sm:inline">{{ $t("productCard.goodPrice") }}</span>
            </div>
            <div class="flex items-center gap-1 text-yellow-400">
              <i class="fas fa-fire"></i>
              <span class="hidden sm:inline">{{ $t("productCard.hotSale") }}</span>
            </div>
          </div>
          <div class="py-2 text-center h-[42px]">
            <template v-if="product?.warranty">
              <span class="inline-flex items-center bg-sky-50 w-full rounded-sm px-2 py-1 text-[10px] text-sky-700">
                {{ $t("productCard.warrantyText", { months: product.warranty }) }}
              </span>
            </template>
          </div>
          <div class="w-full max-w-8xl mx-auto">
            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
          </div>
          <div class="flex justify-between items-center text-xs text-yellow-500 py-2 gap-1 min-h-[36px]">
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

<script setup lang="ts">
import { computed } from "vue";
import type { IProduct } from "~/interfaces/product/product.interface";
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { useCompare } from "~/composables/product/useCompare";
import { useWishlist } from "~/composables/product/useWishlist";
import { useCart } from "~/composables/cart/useCart";

const props = defineProps<{ product: IProduct }>();

const { handleAddToCompareFromProduct } = useCompare();
const { handleToggleWishlistFromProduct, isProductVariantInWishlist } = useWishlist();
const { addItem } = useCart();

const mainImage = computed(() => {
  return resolveAlbum(props.product?.album)?.[0] || "";
});

const subImage = computed(() => {
  return resolveAlbum(props.product?.album)?.[1] || "";
});

const originalPrice = computed(() => Number(props.product?.priceOrigin || 0));

const currentPrice = computed(() => {
  return originalPrice.value;
});

const isInWishlist = computed(() => {
  return isProductVariantInWishlist(props.product);
});

const handleAddToCompare = async () => {
  await handleAddToCompareFromProduct(props.product);
};

const handleToggleWishlist = async () => {
  await handleToggleWishlistFromProduct(props.product);
};

const handleAddToCart = async () => {
  let variant = null;
  if (props.product?.variant?.id) {
    variant = props.product.variant;
  } else if (props.product?.productVariants && props.product.productVariants.length > 0) {
    variant = props.product.productVariants[0];
  }
  await addItem(props.product, variant || null, 1, false);
};
</script>
