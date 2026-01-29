<template>
  <section v-if="items.length > 0" :class="['bg-white shadow-md rounded-md py-3 md:py-6 px-2 md:px-6 mb-4 md:mb-6', rootClass]">
    <div class="mb-3 md:mb-5">
      <div class="flex flex-wrap justify-between items-center gap-2">
        <div>
          <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-lg md:text-xl font-bold">
            {{ title || $t("category.title") }}
          </h2>
          <div class="h-1 max-w-64 my-2 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
          </div>
        </div>
        <div class="group flex items-center gap-2">
          <NuxtLink v-if="viewAllLink" :to="viewAllLink" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 group-hover:after:w-full">
            {{ viewAllText ?? "" }}
          </NuxtLink>
        </div>
      </div>
      <p v-if="description" class="text-gray-600 mt-1 md:mt-2 max-w-2xl text-xs md:text-sm">
        {{ description || $t("category.description") }}
      </p>
    </div>
    <Splide :options="options" id="mini-category-slider">
      <SplideSlide v-for="item in items" :key="item.id" class="shadow-lg">
        <NuxtLink v-if="item.slug" :to="`${basePath}/${item.slug}`" class="block">
          <div class="mini-category-item" style="height: 180px; width: 120px">
            <NuxtImg :src="item.image?.path ? (item.image?.url ?? '') : IMAGEDEFAULT" :alt="item.name || 'Item'" class="rounded-md" loading="lazy" />
            <div class="mini-category-text">
              <span class="line-clamp-2">{{ item.name }}</span>
            </div>
          </div>
        </NuxtLink>
      </SplideSlide>
    </Splide>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useShopSlider } from "~/composables/useShopSlider";
import { IMAGEDEFAULT } from "~/utils/constants";

const { miniCategoryOptions: defaultOptions } = useShopSlider();

const props = defineProps<{
  title?: string;
  description?: string;
  items: {
    id: number | string;
    name?: string;
    slug?: string;
    image?: {
      path?: string;
      url?: string;
    };
  }[];
  basePath?: string;
  viewAllLink?: string;
  viewAllText?: string;
  id?: string;
  options?: any;
  rootClass?: string;
}>();

const options = computed(() => props.options || defaultOptions);
const id = computed(() => props.id || "category-slider");
</script>
