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
