<template>
  <div class="main-content max-w-7xl mx-auto px-2 sm:px-4">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section class="">
      <div class="max-w-7xl mx-auto mb-5">
        <div class="bg-white/10 backdrop-blur-lg rounded-md p-6 md:p-8 border border-white/20 shadow-xl">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center px-2 sm:px-4 lg:px-6">
            <div class="lg:col-span-9">
              <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                <span class="block bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">{{ categoryDetail?.name || $t("client.common.categories") }}</span>
              </h2>
              <p v-if="categoryDetail?.description" class="text-sm text-gray-600 mb-8 leading-relaxed line-clamp-3">{{ categoryDetail.description ?? "" }}</p>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
                <div>
                  <div class="text-3xl font-bold text-cyan-400 mb-2">{{ (categoryDetail as any)?.products?.length || 0 }}</div>
                  <div class="text-sm text-gray-400">sản phẩm</div>
                </div>
              </div>
            </div>
            <div class="lg:col-span-3 flex justify-center">
              <div class="relative overflow-hidden">
                <NuxtImg :src="categoryDetail?.image?.path ? categoryDetail?.image?.url ?? '' : IMAGEDEFAULT" :alt="categoryDetail?.name || 'category'" class="w-[120px] h-[120px] md:w-[160px] md:h-[160px] object-contain" sizes="(max-width: 640px) 120px, (max-width: 1024px) 160px, 160px" loading="lazy" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <Category :title="$t('client.common.categories')" description="Khám phá các danh mục công nghệ đa dạng tại Tech Mart Store – từ thiết bị điện tử, gia dụng thông minh đến phụ kiện hiện đại, đáp ứng nhu cầu học tập, làm việc và giải trí của bạn." :items="(productCatalogueList as any)" :basePath="PRODUCTCATEGORY" :viewAllLink="TOSHOP" :viewAllText="$t('client.common.viewAll')" />

    <section class="mx-auto bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
      <div>
        <div v-if="!categoryDetail" class="text-sm text-gray-500 p-2">{{ $t("client.common.loading") }}</div>
        <div v-else-if="!(categoryDetail as any).products || (categoryDetail as any).products.length === 0" class="text-center text-gray-500 py-4">{{ $t("client.common.noData") }}</div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <div v-for="product in (categoryDetail as any).products" :key="product.id" class="flex justify-center">
            <ProductCard :product="product" />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import Category from "~/components/client/ui/Category.vue";
import ProductCard from "~/components/client/features/ProductCard.vue";
import { IMAGEDEFAULT, TOSHOP, PRODUCTCATEGORY } from "~/utils/constants";
import { useProductCatalogue } from "~/composables/client/product/useProductCatalogue";
import { useProduct } from "~/composables/client/product/useProduct";
import { useDebounceFn } from "@vueuse/core";

const route = useRoute();
const slug = computed(() => route.params.slug as string);

const { categoryDetail, fetchBySlug } = useProductCatalogue() as any;
const { productCatalogueList, fetchProductCatalogues } = useProduct();

const breadcrumbs = computed(() => [
  { label: $t("client.page.shop"), href: TOSHOP },
  { label: categoryDetail.value?.name ?? slug.value, href: `${PRODUCTCATEGORY}/${categoryDetail.value?.slug ?? slug.value}` },
]);

async function loadCategoryData(slug: string) {
  await fetchBySlug(slug);
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
  }, 300)
);

definePageMeta({
  layout: "client",
});
</script>
