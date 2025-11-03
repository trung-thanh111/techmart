<template>
  <div>
    <div class="product-item relative w-full h-[420px] bg-white shadow-md rounded-lg overflow-hidden group">
      <div class="absolute top-0 right-0 h-full w-10 flex flex-col justify-start items-center gap-1 z-30 translate-x-full group-hover:translate-x-0 transition-all duration-300 ease-in-out bg-gradient-to-l from-white via-white/50 to-transparent rounded-l-md backdrop-blur-sm">
        <!-- Heart Icon -->
        <div class="relative group/heart">
          <button @click.prevent="handleHeartClick" class="text-red-500 p-1.5 transition duration-200 relative">
            <i v-if="variantsCount > 1" class="fa-solid fa-heart"></i>
            <i v-else class="fa-solid fa-heart"></i>

            <span v-if="variantsCount > 1" class="absolute top-0 right-1 text-red-500 text-[10px]">
              {{ variantsCount > 5 ? "5+" : variantsCount }}
            </span>
          </button>

          <div v-if="variantsCount > 1" class="absolute top-full right-0 w-42 sm:w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50 opacity-0 pointer-events-none group-hover/heart:opacity-100 group-hover/heart:pointer-events-auto transition-all duration-200">
            <div class="p-1 max-h-64 overflow-y-auto">
              <div v-for="variant in variants" :key="variant.id" class="flex items-center gap-0.5 rounded hover:bg-gray-50 transition last:mb-0">
                <div class="flex-1 min-w-0 text-end">
                  <span class="inline-block text-end bg-sky-50 text-sky-700 text-[10px] px-2 py-0.5 rounded max-w-full break-words line-clamp-2" :title="variant.name">
                    {{ variant.name }}
                  </span>
                </div>
                <button @click.prevent.stop="handleRemoveWishlist(variant.id, variant.wishlistId)" class="text-gray-400 hover:text-red-500 p-1 transition flex-shrink-0">
                  <i class="fa-solid fa-heart-crack"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <NuxtLink to="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-cart-plus"></i>
        </NuxtLink>
        <NuxtLink to="" @click.prevent="handleAddToCompare" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-random"></i>
        </NuxtLink>
        <NuxtLink :to="`/product/${productData?.slug || ''}`" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-eye"></i>
        </NuxtLink>
        <NuxtLink to="#" class="text-gray-400 hover:text-sky-500 p-2 transition duration-200">
          <i class="fas fa-share-alt"></i>
        </NuxtLink>
      </div>

      <div class="h-full flex flex-col relative overflow-hidden z-10">
        <div class="relative h-[260px] overflow-hidden z-0">
          <img v-if="mainImage" :src="mainImage" loading="lazy" :alt="productData?.name || ''" class="w-full h-full object-contain transition-opacity duration-300 group-hover:opacity-0" />
          <img v-if="subImage" :src="subImage" loading="lazy" :alt="productData?.name || ''" class="w-full h-full object-contain absolute top-0 left-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        </div>
        <div class="px-1 flex-grow flex flex-col justify-between z-0">
          <h3 class="text-sm font-medium text-gray-500 h-10 overflow-hidden leading-5 line-clamp-2">
            {{ productData?.name || "" }}
          </h3>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-red-600 font-semibold">{{ formatPrice(priceOrigin) }}</span>
          </div>
          <div class="flex flex-wrap justify-between items-center gap-2 mt-1 text-[12px] sm:text-[10px] text-gray-500 min-h-[28px]">
            <div class="flex items-center gap-1 text-emerald-400">
              <i class="fas fa-award"></i>
              <span class="hidden sm:inline">{{ $t("client.productCard.bestSeller") }}</span>
            </div>
            <div class="flex items-center gap-1 text-sky-400">
              <i class="fas fa-wallet"></i>
              <span class="hidden sm:inline">{{ $t("client.productCard.goodPrice") }}</span>
            </div>
            <div class="flex items-center gap-1 text-yellow-400">
              <i class="fas fa-fire"></i>
              <span class="hidden sm:inline">{{ $t("client.productCard.hotSale") }}</span>
            </div>
          </div>
          <div class="flex justify-start gap-1 py-2 max-w-full overflow-hidden">
            <span v-for="(variant, index) in displayedVariants" :key="variant.id" class="inline-flex items-center bg-sky-50 text-sky-700 text-xs px-2 py-1 rounded whitespace-nowrap relative">
              {{ variant.name }}
            </span>
            <span v-if="variantsCount > 2" class="inline-flex items-center bg-sky-50 text-gray-700 text-xs px-2 py-1 absolute right-0">+{{ variantsCount - 2 }}</span>
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
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { useWishlist } from "~/composables/client/product/useWishlist";
import type { IWishlistGrouped } from "~/interfaces/product/wishlist.interface";
import { useCompare } from "~/composables/client/product/useCompare";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

const { handleAddToCompareFromProduct } = useCompare();

interface WishlistVariant {
  id: number;
  name: string;
  wishlistId?: number;
}

const props = defineProps<{
  product: IWishlistGrouped;
}>();

const { removeWishlist, wishlistGrouped } = useWishlist();

const emit = defineEmits<{
  removed: [variantId: number];
}>();

// Map product data từ IWishlistGrouped structure
const productData = computed(() => props.product?.product || null);
const albumData = computed(() => {
  if (!productData.value?.album) return [];
  try {
    return typeof productData.value.album === "string" ? JSON.parse(productData.value.album) : productData.value.album;
  } catch {
    return [];
  }
});

const albumUrls = computed(() => resolveAlbum(albumData.value));
const mainImage = computed(() => albumUrls.value[0] || "");
const subImage = computed(() => albumUrls.value[1] || "");

const priceOrigin = computed(() => Number(productData.value?.priceOrigin || 0));

// Watch wishlistGrouped để update variants khi có thay đổi (real-time)
const currentProduct = computed(() => {
  return wishlistGrouped.value.find((p) => p.product?.id === props.product?.product?.id);
});

const variant = computed(() => {
  return currentProduct.value?.product?.variant || productData.value?.variant || null;
});

const variants = computed(() => {
  return variant.value ? [variant.value] : [];
});

const variantsCount = computed(() => variants.value.length);
const displayedVariants = computed(() => variants.value.slice(0, 2));

const handleHeartClick = async () => {
  if (variant.value) {
    await handleRemoveWishlist(variant.value.id, variant.value.wishlistId);
  }
};

const handleRemoveWishlist = async (variantId: number, wishlistId?: number) => {
  const success = await removeWishlist(variantId, wishlistId);
  if (success) {
    emit("removed", variantId);
  }
};

const handleAddToCompare = async () => {
  const productData = props.product?.product;
  if (!productData?.id) {
    return;
  }

  const variantData = productData.variant;
  const productVariant: IProductVariant = variantData
    ? {
        id: variantData.id,
        name: variantData.name,
        price: variantData.price,
        attributeData: variantData.attributeData,
      }
    : ({} as IProductVariant);

  const productForCompare: IProduct = {
    id: productData.id,
    name: productData.name,
    slug: productData.slug,
    sku: productData.sku,
    priceOrigin: productData.priceOrigin,
    album: productData.album ? (typeof productData.album === "string" ? JSON.parse(productData.album) : productData.album) : undefined,
    season: productData.season ? (productData.season as "spring" | "summer" | "autumn" | "winter" | "none") : undefined,
    warranty: productData.warranty ? (typeof productData.warranty === "string" ? Number(productData.warranty) : productData.warranty) : undefined,
    productCatalogueId: productData.productCatalogueId,
    brandId: productData.brandId,
    variant: productVariant,
  };

  await handleAddToCompareFromProduct(productForCompare);
};
</script>
