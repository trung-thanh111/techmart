import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";

export function useShopSlider() {
  const partnerOptions: any = {
    type: "loop",
    arrows: false,
    pagination: false,
    perPage: 6,
    perMove: 1,
    autoScroll: {
      speed: 1,
      pauseOnHover: false,
      pauseOnFocus: false,
    },
    breakpoints: {
      1024: { perPage: 5 },
      768: { perPage: 4 },
      640: { perPage: 3 },
      480: { perPage: 2 },
    },
  };

  const miniCategoryOptions: any = {
    type: "loop",
    perPage: 9,
    perMove: 1,
    gap: "1rem",
    autoplay: true,
    interval: 3000,
    pauseOnHover: true,
    pauseOnFocus: true,
    breakpoints: {
      1024: { perPage: 7 },
      768: { perPage: 6 },
      640: { perPage: 3 },
    },
    arrows: false,
    pagination: true,
  };

  const beforeShopProductOptions: any = {
    type: "loop",
    autoplay: true,
    interval: 5000,
    arrows: true,
    pagination: true,
    perPage: 1,
    perMove: 1,
  };

  const productSliderOptions: any = {
    type: "loop",
    autoplay: false,
    arrows: true,
    pagination: true,
    perPage: 1,
    perMove: 1,
  };

  return {
    partnerOptions,
    miniCategoryOptions,
    beforeShopProductOptions,
    productSliderOptions,
    AutoScroll,
  };
}
