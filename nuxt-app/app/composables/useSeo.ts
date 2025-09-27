import type { SeoMeta } from "~/interfaces/seo/seo.interface";

export const useSeo = () => {
  const defaultSeo = {
    title: "TechMart",
    description: "Techmart - Công nghệ tiên tiến, giá cả hợp lý",
    ogTitle: "TechMart",
    ogDescription: "Khám phá sản phẩm công nghệ mới nhất",
    ogType: "website" as const,
    ogImage: "/logo-favicon.png",
    robots: "index, follow",
  };

  // SEO (chỉ cho trang chủ và client pages)
  const setClientSeo = (customSeo?: Partial<SeoMeta>) => {
    const seo = { ...defaultSeo, ...customSeo };

    useHead({
      title: seo.title,
      meta: [
        { name: "description", content: seo.description },
        { property: "og:title", content: seo.ogTitle },
        { property: "og:description", content: seo.ogDescription },
        { property: "og:type", content: seo.ogType },
        { property: "og:image", content: seo.ogImage },
        { name: "robots", content: seo.robots },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
      ],
      link: [{ rel: "stylesheet", href: "/client/css/customize.css" }],
      script: [{ src: "https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js" }, { src: "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" }, { src: "/client/js/interface.js" }],
    });

    useSeoMeta({
      title: seo.title,
      description: seo.description,
      ogTitle: seo.ogTitle,
      ogDescription: seo.ogDescription,
      ogType: seo.ogType as "website" | "article",
      ogImage: seo.ogImage,
      robots: seo.robots,
    });
  };

  const setPageSeo = (pageSeo: SeoMeta) => {
    useHead({
      title: pageSeo.title,
      meta: [
        { name: "description", content: pageSeo.description },
        { name: "keywords", content: pageSeo.keywords || "" },
        { property: "og:title", content: pageSeo.ogTitle || pageSeo.title },
        { property: "og:description", content: pageSeo.ogDescription || pageSeo.description },
        { property: "og:image", content: pageSeo.ogImage || defaultSeo.ogImage },
        { property: "og:type", content: (pageSeo.ogType || "article") as "website" | "article" },
        { name: "robots", content: pageSeo.robots || "index, follow" },
      ],
    });

    useSeoMeta({
      title: pageSeo.title,
      description: pageSeo.description,
      ogTitle: pageSeo.ogTitle || pageSeo.title,
      ogDescription: pageSeo.ogDescription || pageSeo.description,
      ogType: (pageSeo.ogType || "article") as "website" | "article",
      ogImage: pageSeo.ogImage || defaultSeo.ogImage,
      robots: pageSeo.robots || "index, follow",
    });
  };

  return {
    setClientSeo,
    setPageSeo,
  };
};
