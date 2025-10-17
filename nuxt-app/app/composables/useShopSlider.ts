export function useShopSlider() {
  const partnerOptions: any = {
    type: "loop",
    autoplay: true,
    interval: 2000,
    arrows: false,
    pagination: false,
    perPage: 6,
    perMove: 1,
    gap: "1rem",
    breakpoints: {
      768: {
        perPage: 4,
      },
      640: {
        perPage: 3,
      },
      480: {
        perPage: 2,
      }
    }
  };

  const miniCategoryOptions: any = {
    type: "loop",
    autoplay: true,
    interval: 3500,
    arrows: true,
    pagination: false,
    perPage: 6,
    perMove: 1,
    gap: "1rem",
    breakpoints: {
      768: {
        perPage: 4,
      },
      640: {
        perPage: 3,
      },
      480: {
        perPage: 2,
      }
    }
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
  };
}
