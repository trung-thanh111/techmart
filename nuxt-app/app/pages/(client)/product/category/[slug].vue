<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="">
      <div class="max-w-7xl mx-auto mb-5">
        <div class="bg-white/10 backdrop-blur-lg rounded-md p-6 md:p-8 border border-white/20 shadow-xl">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center px-2 sm:px-4 lg:px-6">
            <div class="lg:col-span-9">
              <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                <span class="block bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">{{ categoryDetail?.name || $t("common.categories") }}</span>
              </h2>
              <p v-if="categoryDetail?.description" class="text-sm text-gray-600 mb-8 leading-relaxed line-clamp-3">{{ categoryDetail.description ?? "" }}</p>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
                <div>
                  <div class="text-3xl font-bold text-cyan-400 mb-2">{{ (categoryDetail as any)?.products?.length || 0 }}</div>
                  <div class="text-sm text-gray-400">{{ $t("common.productCount") }}</div>
                </div>
              </div>
            </div>
            <div class="lg:col-span-3 flex justify-center">
              <div class="relative overflow-hidden">
                <NuxtImg :src="categoryDetail?.image?.path ? (categoryDetail?.image?.url ?? '') : IMAGEDEFAULT" :alt="categoryDetail?.name || $t('field.category')" class="w-[120px] h-[120px] md:w-[160px] md:h-[160px] object-contain" sizes="(max-width: 640px) 120px, (max-width: 1024px) 160px, 160px" loading="lazy" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <Category :title="$t('common.categories')" :description="$t('header.exploreCategories')" :items="productCatalogueList as any" :basePath="PRODUCTCATEGORY" :viewAllLink="TOSHOP" :viewAllText="$t('common.viewAll')" />

    <section class="mx-auto bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <DataState :loading="!categoryDetail" :error="false" :data="(categoryDetail as any)?.products" :show-empty-image="true">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4">
          <div v-for="product in (categoryDetail as any).products" :key="product.id" class="flex justify-center">
            <ProductCard :product="product" />
          </div>
        </div>
      </DataState>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { onMounted } from "vue";
import { definePageMeta } from "#imports";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Category from "~/components/ui/Category.vue";
import ProductCard from "~/components/features/ProductCard.vue";
import DataState from "~/components/ui/DataState.vue";
import { IMAGEDEFAULT, TOSHOP, PRODUCTCATEGORY } from "~/utils/constants";
import { useProductCatalogue } from "~/composables/product/useProductCatalogue";
import { useProduct } from "~/composables/product/useProduct";
import { useDebounceFn } from "@vueuse/core";

const { t } = useI18n();
const route = useRoute();
const slug = computed(() => route.params.slug as string);

const { categoryDetail, fetchById } = useProductCatalogue() as any;
const { productCatalogueList, fetchProductCatalogues } = useProduct();

const breadcrumbs = computed(() => [
  { label: t("page.shop"), href: TOSHOP },
  { label: categoryDetail.value?.name ?? slug.value, href: `${PRODUCTCATEGORY}/${categoryDetail.value?.slug ?? slug.value}` },
]);

async function loadCategoryData(slug: string) {
  await fetchById(slug);
}

onMounted(async () => {
  const jobs: Promise<any>[] = [loadCategoryData(slug.value)];
  if (!productCatalogueList.value || productCatalogueList.value.length === 0) {
    jobs.push(fetchProductCatalogues());
  }
  await Promise.all(jobs);
});

watch(
  () => slug.value,
  useDebounceFn(async (val?: string) => {
    if (!val) return;
    await loadCategoryData(val);
  }, 300),
);

definePageMeta({
  layout: "client",
});
</script>
