document.addEventListener("DOMContentLoaded", function () {
  // Hero slide with thumbs
  const heroMain = document.querySelector("#hero-slide-left-main");
  const heroThumbs = document.querySelector("#hero-slide-content-thumbs");

  if (heroMain && heroThumbs) {
    var thumbs = new Splide("#hero-slide-content-thumbs", {
      fixedWidth: 250,
      gap: 12,
      perPage: 4,
      isNavigation: true,
      pagination: false,
      arrows: false,
      focus: "center",
      type: "loop",
      pauseOnFocus: true,
      dragMinThreshold: { mouse: 4, touch: 10 },
      breakpoints: {
        768: { fixedWidth: 200 },
        480: { fixedWidth: 160 },
      },
    });

    var main = new Splide("#hero-slide-left-main", {
      type: "loop",
      speed: 800,
      autoplay: true,
      interval: 3000,
      arrows: true,
      pagination: true,
      pauseOnFocus: true,
      easing: "cubic-bezier(0.25, 1, 0.5, 1)",
      height: "400px",
      breakpoints: {
        1024: { height: "300px" },
        768: { height: "180px" },
        480: { height: "140px" },
      },
    });

    thumbs.mount();
    main.sync(thumbs).mount();
  }

  const productImageMain = document.querySelector("#product-image-main-slider");
  const productImageThumbs = document.querySelector("#product-image-thumbnail-slider");

  if (productImageMain && productImageThumbs) {
    var thumbs = new Splide("#product-image-thumbnail-slider", {
      fixedWidth: 68,
      fixedHeight: 68,
      isNavigation: true,
      gap: 8,
      focus: "center",
      pagination: false,
      cover: true,
      arrows: false,
      breakpoints: {
        640: {
          fixedWidth: 56,
          fixedHeight: 56,
        },
      },
    });

    var main = new Splide("#product-image-main-slider", {
      type: "fade",
      heightRatio: 1,
      pagination: false,
      arrows: false,
      cover: true,
    });

    thumbs.mount();
    main.sync(thumbs).mount();
  }

  // Vertical slider
  const verticalSlider = document.querySelector("#right-vertical-slider");
  if (verticalSlider) {
    new Splide("#right-vertical-slider", {
      type: "loop",
      perMove: 1,
      perPage: 3,
      direction: "ttb",
      height: 500,
      gap: 12,
      arrows: false,
      pagination: false,
      autoplay: true,
      interval: 3500,
      pauseOnFocus: true,
      easing: "cubic-bezier(0.25, 1, 0.5, 1)",
      breakpoints: {
        768: { height: 250, perPage: 2 },
        480: { height: 130, perPage: 1 },
      },
    }).mount();
  }

  // Product slider
  const productSlider = document.querySelector("#product-slider");
  if (productSlider) {
    new Splide("#product-slider", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      arrows: true,
      pagination: false,
      pauseOnHover: true,
      breakpoints: {
        1024: {
          destroy: true,
        },
      },
      height: "900px",
    }).mount();
  }

  const postHotSlider = document.querySelector("#post-hot-slider");
  if (postHotSlider) {
    new Splide("#post-hot-slider", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      arrows: false,
      pagination: true,
      pauseOnHover: true,
      breakpoints: {
        1024: {
          destroy: true,
        },
      },
    }).mount();
  }

  const beforeShopProductSlider = document.querySelector("#before-shop-product-slider");
  if (beforeShopProductSlider) {
    new Splide("#before-shop-product-slider", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      arrows: true,
      pagination: false,
      pauseOnFocus: true,
      breakpoints: {
        1024: {
          destroy: true,
        },
      },
    }).mount();
  }

  // Partner slider
  const partnerSlider = document.querySelector("#partner-slider");
  if (partnerSlider) {
    new Splide("#partner-slider", {
      type: "loop",
      drag: false,
      autoScroll: {
        speed: 1.5,
        pauseOnHover: false,
        pauseOnFocus: true,
      },
      gap: 0,
      arrows: false,
      pagination: false,
      perPage: 6,
      perMove: 1,
      breakpoints: {
        1024: { perPage: 4 },
        768: { perPage: 3 },
        640: { perPage: 2 },
      },
    }).mount({ AutoScroll });
  }

  const partnerShopSlider = document.querySelector("#partner-shop-slider");
  if (partnerShopSlider) {
    new Splide("#partner-shop-slider", {
      type: "loop",
      drag: false,
      autoScroll: {
        speed: 1.5,
        pauseOnHover: true,
        pauseOnFocus: true,
      },
      gap: 0,
      arrows: false,
      pagination: false,
      perPage: 6,
      perMove: 1,
      breakpoints: {
        1024: { perPage: 4 },
        768: { perPage: 3 },
        640: { perPage: 2 },
      },
    }).mount({ AutoScroll });
  }

  // Featured posts slider
  const featuredPosts = document.querySelector("#featured-posts");
  if (featuredPosts) {
    new Splide("#featured-posts", {
      type: "loop",
      perPage: 1,
      gap: 12,
      arrows: false,
      pagination: true,
      autoplay: true,
      interval: 3500,
      breakpoints: {
        1024: { perPage: 1 },
        640: { perPage: 1 },
      },
      easing: "cubic-bezier(0.25, 1, 0.5, 1)",
    }).mount();
  }
  const bannerShopTopLeft = document.querySelector("#banner-shop-top-left");
  const bannerShopTopRight = document.querySelector("#banner-shop-top-right");
  if (bannerShopTopLeft && bannerShopTopRight) {
    new Splide("#banner-shop-top-left", {
      type: "loop",
      // autoplay: true,
      // interval: 3000,
      arrows: true,
      pagination: true,
      easing: "cubic-bezier(0.25, 1, 0.5, 1)",
    }).mount();

    new Splide("#banner-shop-top-right", {
      type: "loop",
      autoplay: true,
      interval: 4000,
      arrows: true,
      pagination: true,
      easing: "cubic-bezier(0.25, 1, 0.5, 1)",
    }).mount();
  }

  const SlideCategories = document.querySelector("#mini-category-slider");
  if (SlideCategories) {
    new Splide("#mini-category-slider", {
      type: "loop",
      perPage: 9,
      perMove: 1,
      gap: "1rem",
      autoplay: true,
      interval: 3000,
      pauseOnHover: true,
      pauseOnFocus: true,
      breakpoints: {
        1024: {
          perPage: 7,
        },
        768: {
          perPage: 6,
        },
        640: {
          perPage: 3,
        },
      },
      arrows: false,
      pagination: true,
    }).mount();
  }

  const SlidePostHot = document.querySelector("#post-slide-hot");
  if (SlidePostHot) {
    new Splide("#post-slide-hot", {
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
    }).mount();
  }

  const slideProduct = document.querySelector("#post-product-slider");
  if (slideProduct) {
    new Splide("#post-product-slider", {
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
    }).mount();
  }
  const postRelatedSlide = document.querySelector("#post-related-slide");
  if (postRelatedSlide) {
    new Splide("#post-related-slide", {
      type: "loop",
      perPage: 4,
      perMove: 1,
      autoplay: true,
      interval: 3000,
      arrows: true,
      pagination: true,
      breakpoints: {
        1024: {
          perPage: 3,
        },
        768: {
          perPage: 2,
        },
        640: {
          perPage: 1,
        },
      },
    }).mount();
  }
  const postCategorySlider = document.querySelector("#post-category-slide");
  if (postCategorySlider) {
    new Splide("#post-category-slide", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      autoplay: true,
      interval: 3000,
      arrows: true,
      pagination: false,
    }).mount();
  }

  const productCategorySlider = document.querySelector("#product-category-slide");
  if (productCategorySlider) {
    new Splide("#product-category-slide", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      autoplay: true,
      interval: 3000,
      arrows: true,
      pagination: true,
    }).mount();
  }

  const promotionSlider = document.querySelector("#promotion-slider");
  if (promotionSlider) {
    new Splide("#promotion-slider", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      autoplay: true,
      interval: 3500,
      arrows: true,
      pagination: true,
    }).mount();
  }
  document.addEventListener("DOMContentLoaded", function () {
    new Splide("#splide", {
      type: "loop",
      autoplay: true,
      interval: 5000,
      arrows: false,
      pagination: true,
    }).mount();
  });

  const contentFormUserSlider = document.querySelector("#content-form-user-slider");
  if (contentFormUserSlider) {
    new Splide("#content-form-user-slider", {
      type: "loop",
      perPage: 1,
      perMove: 1,
      autoplay: true,
      interval: 2500,
      arrows: false,
      pagination: false,
    }).mount();
  }
});
