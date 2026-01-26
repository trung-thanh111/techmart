<template>
    <div class="layout-wrapper">
        <Head />
        <Header />
        <Sidebar />
        <main>
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <slot />
                    </div>
                </div>
            </div>
        </main>
        <Footer />
    </div>
</template>

<script setup lang="ts">
import Head from "~/components/layout/Head.vue";
import Header from "~/components/layout/Header.vue";
import Footer from "~/components/layout/Footer.vue";
import Sidebar from "~/components/layout/Sidebar.vue";
// Admin assets
useHead({
    title: "Techmart quản trị",
    link: [
        { rel: "icon", type: "image/x-icon", href: "/logo-favicon.png" },
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        { rel: "preconnect", href: "https://fonts.gstatic.com", crossorigin: "" },
        { rel: "stylesheet", href: "https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" },
        { rel: "stylesheet", href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css", media: "print", onload: "this.media='all'" },
        { rel: "stylesheet", href: "/css/bootstrap.min.css" },
        { rel: "stylesheet", href: "/css/icons.min.css" },
        { rel: "stylesheet", href: "/css/app.min.css" },
        { rel: "stylesheet", href: "/css/custom.min.css" },
    ],
    script: [
        { src: "/libs/bootstrap/js/bootstrap.bundle.min.js", defer: false },
        { src: "/libs/simplebar/simplebar.min.js", defer: false },
        { src: "/libs/node-waves/waves.min.js", defer: false },
        { src: "/libs/feather-icons/feather.min.js", defer: false },
        { src: "/js/pages/plugins/lord-icon-2.1.0.js", defer: false },
        { src: "/libs/apexcharts/apexcharts.min.js", defer: false },
        { src: "/libs/jsvectormap/js/jsvectormap.min.js", defer: false },
        { src: "/libs/jsvectormap/maps/world-merc.js", defer: false },
        { src: "/libs/swiper/swiper-bundle.min.js", defer: false },
        { src: "/js/app.js", defer: false },
        { src: "/js/pages/dashboard-ecommerce.init.js", defer: false },
    ],
});
</script>
<style>
.jvm-tooltip {
    display: none !important;
}
</style>
