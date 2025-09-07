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
</script>
