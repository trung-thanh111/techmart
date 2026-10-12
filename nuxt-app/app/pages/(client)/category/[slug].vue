<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4 py-2 sm:py-4">
    <Breadcrumb :crumbs="breadcrumbs" />

    <!-- Banner Header Danh mục -->
    <section class="mb-5">
      <div class="bg-gradient-to-r from-[#00b88c]/10 via-[#007c8b]/10 to-sky-50 border border-emerald-100 rounded-xl p-4 sm:p-6 md:p-8 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
          <div class="lg:col-span-9">
            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-[#00b88c] to-[#007c8b] bg-clip-text text-transparent mb-2">
              {{ categoryDetail?.name || $t("common.categories") }}
            </h1>
            <p v-if="categoryDetail?.description" class="text-xs sm:text-sm text-gray-600 mb-4 leading-relaxed line-clamp-3">
              {{ categoryDetail.description }}
            </p>
            <div class="flex flex-wrap items-center gap-3">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>{{ displayedProducts.length }} {{ $t("common.products") || "sản phẩm" }}</span>
              </span>
              <span v-if="selectedBrand" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                <span>Thương hiệu: <strong class="capitalize">{{ selectedBrand }}</strong></span>
                <button type="button" @click="selectBrand('')" class="hover:text-red-600 ml-1">✕</button>
              </span>
            </div>
          </div>
          <div class="lg:col-span-3 flex justify-center">
            <div class="relative overflow-hidden p-2 bg-white/60 backdrop-blur rounded-2xl shadow-sm border border-emerald-50">
              <NuxtImg
                :src="categoryDetail?.image?.path ? (categoryDetail?.image?.url ?? '') : IMAGEDEFAULT"
                :alt="categoryDetail?.name || $t('field.category')"
                class="w-24 h-24 sm:w-28 sm:h-28 md:w-36 md:h-36 object-contain"
                sizes="(max-width: 640px) 96px, (max-width: 1024px) 112px, 144px"
                loading="lazy"
              />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bộ lọc thương hiệu nhanh (Brand Filter Chips) -->
    <section v-if="availableBrands.length > 0" class="mb-5 bg-white border border-gray-100 rounded-xl p-3 sm:p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">
        <svg class="w-3.5 h-3.5 text-[#00b88c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <span>Thương hiệu</span>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          @click="selectBrand('')"
          :class="[
            !selectedBrand
              ? 'bg-gradient-to-r from-[#00b88c] to-[#007c8b] text-white shadow-sm font-semibold'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
          ]"
          class="px-3 py-1.5 rounded-lg text-xs transition-colors"
        >
          Tất cả ({{ (categoryDetail as any)?.products?.length || 0 }})
        </button>
        <button
          v-for="brand in availableBrands"
          :key="brand.slug"
          type="button"
          @click="selectBrand(brand.slug)"
          :class="[
            selectedBrand === brand.slug
              ? 'bg-gradient-to-r from-[#00b88c] to-[#007c8b] text-white shadow-sm font-semibold'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
          ]"
          class="px-3 py-1.5 rounded-lg text-xs transition-colors"
        >
          {{ brand.name }} ({{ brand.count }})
        </button>
      </div>
    </section>

    <!-- Khám phá danh mục khác -->
    <Category
      :title="$t('common.categories')"
      :description="$t('header.exploreCategories')"
      :items="productCatalogueList as any"
      :basePath="PRODUCTCATEGORY"
      :viewAllLink="TOSHOP"
      :viewAllText="$t('common.viewAll')"
    />

    <!-- Danh sách sản phẩm -->
    <section class="mx-auto bg-white shadow-sm border border-gray-100 rounded-xl py-4 sm:py-6 px-3 sm:px-6 mb-6">
      <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
        <span class="text-xs sm:text-sm text-gray-600">
          {{ $t("common.display") || "Hiển thị" }}:
          <strong class="text-sky-600 font-semibold">{{ displayedProducts.length }}</strong>
          {{ $t("common.products") || "sản phẩm" }}
        </span>
        <div v-if="selectedBrand" class="text-xs">
          <button type="button" @click="selectBrand('')" class="text-sky-600 hover:underline">
            Xoá bộ lọc thương hiệu
          </button>
        </div>
      </div>

      <DataState :loading="isLoading" :error="false" :data="displayedProducts" :show-empty-image="true">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4">
          <div v-for="product in displayedProducts" :key="product.id" class="flex justify-center">
            <ProductCard :product="product" />
          </div>
        </div>
      </DataState>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { definePageMeta, useSeoMeta } from "#imports";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Category from "~/components/ui/Category.vue";
import ProductCard from "~/components/features/ProductCard.vue";
import DataState from "~/components/ui/DataState.vue";
import { IMAGEDEFAULT, TOSHOP, PRODUCTCATEGORY } from "~/utils/constants";
import { useProductCatalogue } from "~/composables/product/useProductCatalogue";
import { useProduct } from "~/composables/product/useProduct";

const { t } = useI18n();
const route = useRoute();
const router = useRouter();

const slug = computed(() => (route.params.slug as string) || "");
const selectedBrand = computed(() => ((route.query.brand as string) || "").toLowerCase());

const { categoryDetail, fetchById, isLoading } = useProductCatalogue() as any;
const { productCatalogueList, fetchProductCatalogues } = useProduct();

// Danh sách thương hiệu khả dụng từ các sản phẩm thuộc danh mục
const availableBrands = computed(() => {
  const products = (categoryDetail.value as any)?.products || [];
  const map = new Map<string, { name: string; slug: string; count: number }>();

  for (const p of products) {
    const brandSlug = p.brand?.slug?.toLowerCase();
    const brandName = p.brand?.name || p.brand?.slug;
    if (brandSlug) {
      if (!map.has(brandSlug)) {
        map.set(brandSlug, { name: brandName, slug: brandSlug, count: 0 });
      }
      map.get(brandSlug)!.count++;
    }
  }

  return Array.from(map.values());
});

// Danh sách sản phẩm đã lọc theo thương hiệu
const displayedProducts = computed(() => {
  const products = (categoryDetail.value as any)?.products || [];
  if (!selectedBrand.value) return products;

  return products.filter((p: any) => {
    const bSlug = p.brand?.slug?.toLowerCase();
    const bName = p.brand?.name?.toLowerCase();
    return bSlug === selectedBrand.value || bName === selectedBrand.value;
  });
});

const breadcrumbs = computed(() => {
  const crumbs = [
    { label: t("page.shop") || "Cửa hàng", href: TOSHOP },
    {
      label: categoryDetail.value?.name ?? slug.value,
      href: `${PRODUCTCATEGORY}/${categoryDetail.value?.slug ?? slug.value}`,
    },
  ];

  if (selectedBrand.value) {
    const foundBrand = availableBrands.value.find((b) => b.slug === selectedBrand.value);
    crumbs.push({
      label: foundBrand?.name || selectedBrand.value.toUpperCase(),
      href: `${PRODUCTCATEGORY}/${slug.value}?brand=${selectedBrand.value}`,
    });
  }

  return crumbs;
});

const selectBrand = (brandSlug: string) => {
  const query = { ...route.query };
  if (brandSlug) {
    query.brand = brandSlug;
  } else {
    delete query.brand;
  }
  router.push({ query });
};

async function loadCategoryData(targetSlug: string) {
  if (!targetSlug) return;
  await fetchById(targetSlug);
}

onMounted(async () => {
  const jobs: Promise<any>[] = [loadCategoryData(slug.value)];
  if (!productCatalogueList.value || productCatalogueList.value.length === 0) {
    jobs.push(fetchProductCatalogues());
  }
  await Promise.all(jobs);
});

watch(
  () => route.params.slug,
  async (newVal) => {
    if (newVal) {
      await loadCategoryData(newVal as string);
    }
  },
);

useSeoMeta({
  title: () => categoryDetail.value?.name ? `${categoryDetail.value.name} - Techmart` : "Danh mục sản phẩm - Techmart",
  description: () => categoryDetail.value?.description || "Khám phá các sản phẩm công nghệ chính hãng tại Techmart",
});

definePageMeta({
  layout: "client",
});
</script>
