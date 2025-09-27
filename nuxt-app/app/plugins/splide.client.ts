import { defineNuxtPlugin } from "#app";

export default defineNuxtPlugin(async (nuxtApp) => {
  // Lazy load Splide để không block UI
  const { Splide, SplideSlide } = await import("@splidejs/vue-splide");
  
  // Import CSS async
  if (process.client) {
    await import("@splidejs/splide/css");
  }
  
  nuxtApp.vueApp.component("Splide", Splide);
  nuxtApp.vueApp.component("SplideSlide", SplideSlide);
});
