<template></template>

<script setup>
// Client-specific meta tags and resources
useHead({
  title: "TechMart",
  meta: [
    {
      name: "description",
      content: "Techmart - Công nghệ tiên tiến, giá cả hợp lý",
    },
    { property: "og:title", content: "TechMart" },
    {
      property: "og:description",
      content: "Khám phá sản phẩm công nghệ mới nhất",
    },
    { name: "viewport", content: "width=device-width, initial-scale=1" },
  ],
  link: [{ rel: "stylesheet", href: "/client/css/customize.css" }],
  script: [{ src: "https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js" }, { src: "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" }, { src: "/client/js/interface.js" }],
});

// SEO meta
useSeoMeta({
  title: "TechMart",
  description: "Techmart - sản phẩm công nghệ hiện đại mới mẻ, dùng AI tích hợp vào sản phẩm",
  ogTitle: "TechMart",
  ogDescription: "Khám phá sản phẩm công nghệ mới nhất",
  ogType: "website",
});
</script>
