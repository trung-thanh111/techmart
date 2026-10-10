import type { SeoMeta } from "~/interfaces/seo/seo.interface";
import { useSystemConfig } from "~/composables/system/useSystemConfig";

export const useSeo = () => {
  const { seo, companyName, favicon, headScript } = useSystemConfig();

  const getDefaultSeo = () => ({
    title: seo.value.meta_title || companyName.value || "TechMart",
    description: seo.value.meta_description || "Techmart - Công nghệ tiên tiến, giá cả hợp lý",
    keywords: seo.value.meta_keywords || "",
    ogTitle: seo.value.meta_title || companyName.value || "TechMart",
    ogDescription: seo.value.meta_description || "Khám phá sản phẩm công nghệ mới nhất",
    ogType: "website" as const,
    ogImage: seo.value.og_image || "/logo-favicon.png",
    robots: seo.value.robots || "index, follow",
  });

  // SEO (chỉ cho trang chủ và client pages)
  const setClientSeo = (customSeo?: Partial<SeoMeta>) => {
    const defaultSeo = getDefaultSeo();
    const resolved = { ...defaultSeo, ...customSeo };

    const links: Array<Record<string, any>> = [{ rel: "stylesheet", href: "/css/customize.css" }];
    if (favicon.value) {
      links.push({ rel: "icon", href: favicon.value });
    }

    const scripts: Array<Record<string, any>> = [
      { src: "https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js" },
      { src: "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" },
      { src: "//js/interface.js" },
    ];
    if (headScript.value) {
      scripts.push({ innerHTML: headScript.value });
    }

    useHead({
      title: resolved.title,
      meta: [
        { name: "description", content: resolved.description },
        { name: "keywords", content: resolved.keywords || "" },
        { property: "og:title", content: resolved.ogTitle },
        { property: "og:description", content: resolved.ogDescription },
        { property: "og:type", content: resolved.ogType },
        { property: "og:image", content: resolved.ogImage },
        { name: "robots", content: resolved.robots },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
      ],
      link: links,
      script: scripts,
    });

    useSeoMeta({
      title: resolved.title,
      description: resolved.description,
      ogTitle: resolved.ogTitle,
      ogDescription: resolved.ogDescription,
      ogType: resolved.ogType as "website" | "article",
      ogImage: resolved.ogImage,
      robots: resolved.robots,
    });
  };

  const setPageSeo = (pageSeo: SeoMeta) => {
    const defaultSeo = getDefaultSeo();
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
