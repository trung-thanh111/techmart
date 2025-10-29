export function useProductSlider() {
  /**
   * Options cho slider ảnh chính sản phẩm
   */
  const productImageMainOptions: any = {
    type: "loop",
    autoplay: true,
    interval: 5000,
    rewind: true,
    pagination: false,
    arrows: false,
    cover: true,
    heightRatio: 0.8,
    height: "100%",
    breakpoints: {
      640: {
        heightRatio: 1,
      },
    },
  };

  /**
   * Options cho slider thumbnail sản phẩm
   */
  const productImageThumbnailOptions: any = {
    type: "slide",
    rewind: true,
    gap: 8,
    pagination: false,
    fixedWidth: 68,
    fixedHeight: 68,
    cover: true,
    focus: "center",
    isNavigation: true,
    arrows: false,
    breakpoints: {
      640: {
        fixedWidth: 60,
        fixedHeight: 60,
      },
    },
  };

  const productRelatedOptions = (total: number) => {
    const isLoop = total > 5;
    return {
      type: isLoop ? "loop" : "slide",
      perPage: 5,
      perMove: 1,
      gap: "1rem",
      trimSpace: false,
      autoplay: isLoop,
      interval: 3000,
      arrows: isLoop,
      pagination: isLoop,
      pauseOnHover: true,
      breakpoints: {
        1024: { perPage: 4 },
        768: { perPage: 3 },
        640: { perPage: 2 },
        480: { perPage: 1 },
      },
    };
  };

  return {
    productImageMainOptions,
    productImageThumbnailOptions,
    productRelatedOptions,
  };
}
