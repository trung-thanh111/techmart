<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header />
    <PopupManager />
    <main>
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Header from "~/components/layout/Header.vue";
import Footer from "~/components/layout/Footer.vue";
import PopupManager from "~/components/ui/PopupManager.vue";
import { useSeo } from "~/composables/useSeo";

const { setClientSeo } = useSeo();
setClientSeo();
</script>
