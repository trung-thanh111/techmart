import { ref, onMounted } from "vue";
import type { Ref } from "vue";
import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";
import { Splide as SplideJS } from "@splidejs/splide";
import { bannerService } from "~/services/banner/banner.service";
import { resolveAlbum } from "~/utils/helpers";
import type { IBanner } from "~/interfaces/banner/banner.interface";

type SplideOptions = any;

interface SlideItem {
  image: string;
  alt?: string;
  link?: string;
}

interface ThumbnailItem {
  icon: string;
  title: string;
  description: string;
}

interface PostItem {
  image: string;
  category: string;
  title: string;
  description: string;
}

interface PartnerItem {
  logo: string;
  alt: string;
  name: string;
}

export function useHomeSlider() {
  // cấu hình main slider
  const mainOptions: SplideOptions = {
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
  };

  // cấu hình thumb slider
  const thumbOptions: SplideOptions = {
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
  };

  // cấu hình right banner virtical
  const rightVirticalOptions: SplideOptions = {
    type: "loop",
    perMove: 1,
    perPage: 3.5,
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
      1024: { height: 260, perPage: 1 },
      768: { perPage: 1 },
      480: { height: 200, perPage: 1 },
    },
  };

  // cấu hình post futured
  const postFeaturedOptions: SplideOptions = {
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
  };

  // cấu hình partner slider với auto scroll
  const partnerOptions: SplideOptions = {
    type: "loop",
    drag: false, // tắt drag để auto scroll mượt hơn
    autoScroll: {
      speed: 2,
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
  };

  // Slides data
  const defaultMainItems: SlideItem[] = [
    { image: "/images/slide/hero-slide-left-1.webp" },
    { image: "/images/slide/hero-slide-left-2.webp" },
    { image: "/images/slide/hero-slide-left-3.webp" },
  ];

  const defaultRightVirticalItems: SlideItem[] = [
    { image: "/images/slide/hero-slide-right-1.webp", alt: "Banner Right 1" },
    { image: "/images/slide/hero-slide-right-2.webp", alt: "Banner Right 2" },
    { image: "/images/slide/slide_hero_r_3.png", alt: "Banner Right 3" },
    { image: "/images/slide/slide_hero_r_3.png", alt: "Banner Right 4" },
    { image: "/images/slide/slide_hero_r_3.png", alt: "Banner Right 5" },
  ];

  const mainItems = useState<SlideItem[]>("home-main-slider-items", () => defaultMainItems);
  const rightVirticalItems = useState<SlideItem[]>("home-right-slider-items", () => defaultRightVirticalItems);
  const introBanner = useState<string>("home-intro-banner-1", () => "/images/slide/test11.jpg");
  const introAccessoryBanner = useState<string>("home-intro-banner-2", () => "/images/slide/image_about_accessory_1.jpg");

  const fetchHomeBanners = async () => {
    try {
      const res = await bannerService.all({
        page_id: 1,
        publish: 2,
      });

      const list: IBanner[] = Array.isArray(res?.data)
        ? res.data
        : (res?.data as any)?.data || [];

      if (!list.length) return;

      // 1. Slider chính (position_id = 1)
      const mainBanner = list.find((b: any) => b.position_id === 1 || b.positionId === 1);
      if (mainBanner && mainBanner.album) {
        const resolved = resolveAlbum(mainBanner.album);
        if (resolved.length > 0) {
          mainItems.value = resolved.map((img, idx) => ({
            image: img,
            alt: mainBanner.name ? `${mainBanner.name} ${idx + 1}` : `Slide ${idx + 1}`,
            link: mainBanner.link_url || "/shop",
          }));
        }
      }

      // 2. Banner dọc phải (position_id = 2)
      const rightBanner = list.find((b: any) => b.position_id === 2 || b.positionId === 2);
      if (rightBanner && rightBanner.album) {
        const resolved = resolveAlbum(rightBanner.album);
        if (resolved.length > 0) {
          rightVirticalItems.value = resolved.map((img, idx) => ({
            image: img,
            alt: rightBanner.name ? `${rightBanner.name} ${idx + 1}` : `Banner Right ${idx + 1}`,
            link: rightBanner.link_url || "/shop",
          }));
        }
      }

      // 3. Banner giới thiệu (position_id = 3)
      const introBanners = list.filter((b: any) => b.position_id === 3 || b.positionId === 3);
      if (introBanners.length > 0) {
        const first = introBanners[0];
        const resolvedFirst = resolveAlbum(first.album || []);
        if (resolvedFirst[0]) {
          introBanner.value = resolvedFirst[0];
        }

        if (introBanners.length > 1) {
          const second = introBanners[1];
          const resolvedSecond = resolveAlbum(second.album || []);
          if (resolvedSecond[0]) {
            introAccessoryBanner.value = resolvedSecond[0];
          }
        }
      }
    } catch (err) {
      console.error("[useHomeSlider] fetchHomeBanners error:", err);
    }
  };

  const thumbnailItems: ThumbnailItem[] = [
    {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" class="md:w-5 md:h-5 w-4 h-4"><path d="M49.7 32l90.8 0L114.9 160l-94 0C9.3 160 0 150.7 0 139.1c0-2.1 .3-4.1 .9-6.1L26.8 48.9C29.9 38.9 39.2 32 49.7 32zM272 160l-124.5 0L173.1 32 272 32l0 128zm32 0l0-128 98.9 0 14.4 72.1c-1.7 1.1-3.3 2.4-4.8 3.8c-18.4 16.4-35.4 34-50.5 52.1l-58 0zm209.9-23.7c-1.7 1.6-3.4 3.2-5 4.8C498 129.6 486.7 118.6 475 108c-7.6-6.9-17-10.8-26.6-11.8L435.5 32l90.8 0c10.5 0 19.8 6.9 22.9 16.9L575.1 133c.2 .7 .4 1.4 .5 2.1c-17.8-15-44.3-14.6-61.7 1.2zM325.2 210.7C304.3 244.5 288 282.9 288 318.1c0 49.3 18.6 95.2 49.6 129.9L128 448c0 17.7-14.3 32-32 32s-32-14.3-32-32L44 288l-12 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l4 0-4-32 305.5 0c-4.4 6.2-8.5 12.5-12.3 18.7zm180.6-34.5L518 162.5c5.4-6.1 13.3-8.8 20.9-8.9c7.2 0 14.3 2.6 19.9 7.8c19.7 18.3 39.8 43.2 55 70.6C629 259.2 640 290.2 640 320.2C640 408.8 568.7 480 480 480c-89.6 0-160-71.3-160-159.8c0-37.3 16-73.4 36.8-104.5c20.9-31.3 47.5-59 70.9-80.2c5.7-5.2 13.1-7.7 20.3-7.5c14.1 .3 23.8 11.4 32.7 21.6c0 0 0 0 0 0c2 2.3 4 4.6 6 6.7l19 19.9zM544 368.2c0-36.5-37-73-54.8-88.4c-5.4-4.7-13.1-4.7-18.5 0C453 295.1 416 331.6 416 368.2c0 35.3 28.7 64 64 64s64-28.7 64-64z"/></svg>',
      title: "Sản phẩm mới",
      description: "Dòng sản phẩm hữu cơ chất lượng cao.",
    },
    {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor" class="md:w-5 md:h-5 w-4 h-4"><path d="M374.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-320 320c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l320-320zM128 128A64 64 0 1 0 0 128a64 64 0 1 0 128 0zM384 384a64 64 0 1 0 -128 0 64 64 0 1 0 128 0z"/></svg>',
      title: "Ưu đãi hôm nay",
      description: "Mua 1 sẽ được tặng thêm 1 đến hết tháng 6.",
    },
    {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor" class="md:w-5 md:h-5 w-4 h-4"><path d="M153.6 29.9l16-21.3C173.6 3.2 180 0 186.7 0C198.4 0 208 9.6 208 21.3V43.5c0 13.1 5.4 25.7 14.9 34.7L307.6 159C356.4 205.6 384 270.2 384 337.7C384 434 306 512 209.7 512H192C86 512 0 426 0 320v-3.8c0-48.8 19.4-95.6 539-130.1l3.5-3.5c4.2-4.2 10-6.6 16-6.6C85.9 176 96 186.1 96 198.6V288c0 35.3 28.7 64 64 64s64-28.7 64-64v-3.9c0-18-7.2-35.3-19.9-48l-38.6-38.6c-24-24-37.5-56.7-37.5-90.7c0-27.7 9-54.8 25.6-76.9z"/></svg>',
      title: "Sản phẩm bán chạy",
      description: "Top 10 sản phẩm được yêu thích nhất.",
    },
    {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor" class="md:w-5 md:h-5 w-4 h-4"><path d="M272 384c9.6-31.9 29.5-59.1 49.2-86.2c0 0 0 0 0 0c5.2-7.1 10.4-14.2 15.4-21.4c19.8-28.5 31.4-63 31.4-100.3C368 78.8 289.2 0 192 0S16 78.8 16 176c0 37.3 11.6 71.9 31.4 100.3c5 7.2 10.2 14.3 15.4 21.4c0 0 0 0 0 0c19.8 27.1 39.7 54.4 49.2 86.2l160 0zM192 512c44.2 0 80-35.8 80-80l0-16-160 0 0 16c0 44.2 35.8 80 80 80zM112 176c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-61.9 50.1-112 112-112c8.8 0 16 7.2 16 16s-7.2 16-16 16c-44.2 0-80 35.8-80 80z"/></svg>',
      title: "Mẹo vặt",
      description: "Cách bảo quản sản phẩm nông nghiệp hiệu quả.",
    },
  ];


  const postFeaturedItems: PostItem[] = [
    {
      image: "/images/posts/post-home-1.jpg",
      category: "Công nghệ",
      title: "Bài viết nổi bật: Công nghệ AI thay đổi cuộc sống thế nào1",
      description: "Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này. Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này.",
    },
    {
      image: "/images/posts/post-home-2.jpg",
      category: "Công nghệ2",
      title: "Bài viết nổi bật: Công nghệ AI thay đổi cuộc sống thế nào2",
      description: "Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này. Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này.",
    },
    {
      image: "/images/posts/post-home-1.jpg",
      category: "Công nghệ3",
      title: "Bài viết nổi bật: Công nghệ AI thay đổi cuộc sống thế nào3",
      description: "Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này. Trí tuệ nhân tạo đang làm thay đổi cách chúng ta sống, làm việc và giao tiếp. Cùng tìm hiểu những đột phá mới nhất trong lĩnh vực này.",
    },
  ];

  const partnerItems: PartnerItem[] = [
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg",
      alt: "Apple Logo",
      name: "Apple",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/9/96/Microsoft_logo_%282012%29.svg",
      alt: "Microsoft Logo",
      name: "Microsoft",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg",
      alt: "Google Logo",
      name: "Google",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg",
      alt: "Amazon Logo",
      name: "Amazon",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/7/7b/Meta_Platforms_Inc._logo.svg",
      alt: "Meta Logo",
      name: "Meta",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/b/bb/Tesla_T_symbol.svg",
      alt: "Tesla Logo",
      name: "Tesla",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg",
      alt: "Netflix Logo",
      name: "Netflix",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/sco/2/21/Nvidia_logo.svg",
      alt: "NVIDIA Logo",
      name: "NVIDIA",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg",
      alt: "Samsung Logo",
      name: "Samsung",
    },
    {
      logo: "https://upload.wikimedia.org/wikipedia/commons/7/7b/Adobe_Systems_logo_and_wordmark.svg",
      alt: "Adobe Logo",
      name: "Adobe",
    },
  ];

  // Refs cho sync
  const mainSlider: Ref<any> = ref();
  const thumbSlider: Ref<any> = ref();
  const partnerSlider: Ref<any> = ref();

  // Đồng bộ main vs thumb slide & nạp banners
  onMounted(async () => {
    fetchHomeBanners();

    if (mainSlider.value?.splide && thumbSlider.value?.splide) {
      mainSlider.value.splide.sync(thumbSlider.value.splide);
    }
    const partnerElement = partnerSlider.value;
    if (partnerElement) {
      const splide = new SplideJS(partnerElement, partnerOptions);
      splide.mount({ AutoScroll });
    }
  });

  return {
    // cấu hình slider
    mainOptions,
    thumbOptions,
    rightVirticalOptions,
    postFeaturedOptions,
    partnerOptions,

    // dữ liệu banners & slides
    mainItems,
    thumbnailItems,
    rightVirticalItems,
    introBanner,
    introAccessoryBanner,
    postFeaturedItems,
    partnerItems,

    // actions
    fetchHomeBanners,

    // refs cho Splide
    mainSlider,
    thumbSlider,
    partnerSlider,
  };
}
