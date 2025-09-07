<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head />
    <Header />
    <main>
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Head from "~/components/client/layout/Head.vue";
import Header from "~/components/client/layout/Header.vue";
import Footer from "~/components/client/layout/Footer.vue";
</script>
