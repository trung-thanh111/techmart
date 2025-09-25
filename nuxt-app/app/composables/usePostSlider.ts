export function usePostSlider() {
  const postSlideHotOptions: any = {
    type: "loop",
    perPage: 1,
    perMove: 1,
    gap: "1rem",
    autoplay: true,
    interval: 3000,
    pauseOnHover: true,
    pauseOnFocus: true,
    arrows: true,
    pagination: true,
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

  const postHotOptions: any = {
    type: "loop",
    perPage: 1,
    perMove: 1,
    arrows: false,
    pagination: true,
    pauseOnHover: true,
    breakpoints: {
      1024: { destroy: true },
    },
  };

  const postProductOptions: any = {
    type: "loop",
    perPage: 2,
    perMove: 2,
    gap: "1rem",
    autoplay: true,
    interval: 3000,
    arrows: true,
    pagination: true,
    breakpoints: {
      640: { perPage: 1 },
    },
  };

  const postRelatedOptions = (total: number) => {
    const isLoop = total > 4
    return {
      type: isLoop ? "loop" : "slide",
      perPage: 4,
      perMove: 1,
      trimSpace: false,
      autoplay: isLoop,
      interval: 3000,
      arrows: isLoop,
      pagination: isLoop,
      breakpoints: {
        1024: { perPage: 3 },
        768:  { perPage: 2 },
        640:  { perPage: 1 },
      },
    }
  }

  return {
    postSlideHotOptions,
    miniCategoryOptions,
    postHotOptions,
    postProductOptions,
    postRelatedOptions,
  };
}
