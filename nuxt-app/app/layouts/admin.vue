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
import Head from "~/components/admin/layout/Head.vue";
import Header from "~/components/admin/layout/Header.vue";
import Footer from "~/components/admin/layout/Footer.vue";
import Sidebar from "~/components/admin/layout/Sidebar.vue";
useHead({
  link: [
    { rel: "stylesheet", href: "/admin/css/bootstrap.min.css" },
    { rel: "stylesheet", href: "/admin/css/icons.min.css" },
    { rel: "stylesheet", href: "/admin/css/app.min.css" },
    { rel: "stylesheet", href: "/admin/css/custom.min.css" },
  ],
  script: [
    { src: "/admin/libs/bootstrap/js/bootstrap.bundle.min.js", defer: false },
    { src: "/admin/libs/simplebar/simplebar.min.js", defer: false },
    { src: "/admin/libs/node-waves/waves.min.js", defer: false },
    { src: "/admin/libs/feather-icons/feather.min.js", defer: false },
    { src: "/admin/js/pages/plugins/lord-icon-2.1.0.js", defer: false },
    { src: "/admin/libs/apexcharts/apexcharts.min.js", defer: false },
    { src: "/admin/libs/jsvectormap/js/jsvectormap.min.js", defer: false },
    { src: "/admin/libs/jsvectormap/maps/world-merc.js", defer: false },
    { src: "/admin/libs/swiper/swiper-bundle.min.js", defer: false },
    { src: "/admin/js/app.js", defer: false },
    { src: "/admin/js/pages/dashboard-ecommerce.init.js", defer: false }
  ]
})
</script>
