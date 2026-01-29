<template>
  <section v-if="resolvedLogos.length > 0" :class="['bg-white shadow-md rounded-md py-2 px-2 md:px-6 mb-3 md:mb-6', rootClass]">
    <Splide :options="partnerOptions" :extensions="{ AutoScroll }" id="partner-shop-slider">
      <SplideSlide v-for="logo in resolvedLogos" :key="logo.alt || logo.src">
        <div class="block rounded-md shadow">
          <div class="logo-slide h-24 w-full flex items-center justify-center">
            <NuxtImg :src="logo.src" :alt="logo.alt || 'Brand'" class="h-12 w-auto object-contain" loading="lazy" />
          </div>
        </div>
      </SplideSlide>
    </Splide>
    <div class="w-full max-w-7xl mx-auto py-2 mb-3">
      <div class="h-px w-full bg-gradient-to-r from-transparent via-[rgba(32,141,128,0.3)] to-transparent"></div>
    </div>
  </section>
</template>
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import type { IBrand } from "~/interfaces/brand/brand.interface";
import { brandService } from "~/services/brand/brand.service";
import { useShopSlider } from "~/composables/useShopSlider";

const { partnerOptions, AutoScroll } = useShopSlider();

const { t } = useI18n();

const props = defineProps<{
  logos?: { src: string; alt?: string }[];
  title?: string;
  rootClass?: string;
}>();

const resolvedLogos = ref<{ src: string; alt?: string }[]>(props.logos || []);

onMounted(async () => {
  if (resolvedLogos.value.length === 0) {
    try {
      const res = await brandService.all();
      const data: any = (res as any).data;
      const brands: IBrand[] = Array.isArray(data) ? data : data?.data || [];
      resolvedLogos.value = (brands || [])
        .map((b) => ({
          src: b.logo?.path ? (b.logo?.url ?? "") : "",
          alt: b.name || "Brand",
        }))
        .filter((item) => !!item.src);
    } catch (e) {
      console.error(t("message.errorLoading"), e);
    }
  }
});
</script>
