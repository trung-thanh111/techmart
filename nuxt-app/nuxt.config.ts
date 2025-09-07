// nuxt.config.ts
import { resolve } from "path";
import { defineNuxtConfig } from 'nuxt/config';
import Aura from "@primevue/themes/aura";

export default defineNuxtConfig({
  future: {
    compatibilityVersion: 4,
  },
  compatibilityDate: "2025-08-23",
  srcDir: "app/",
  modules: ["@nuxtjs/tailwindcss", "@primevue/nuxt-module", "@nuxt/image", "@pinia/nuxt", "pinia-plugin-persistedstate/nuxt", "@nuxtjs/i18n"],
  primevue: {
    autoImport: true,
    options: {
      ripple: true,
      theme: {
        preset: Aura,
        options: {
          darkModeSelector: false,
        },
      },
    },
  },
  devtools: { enabled: true },
  alias: {
    "@": resolve(__dirname, "./app"),
    "~": resolve(__dirname, "./app"),
  },
  runtimeConfig: {
    public: {
      baseUrl: process.env.BASE_URL,
      apyHubApiKey: process.env.APYHUB_API_KEY,
    },
  },
  i18n: {
    locales: [
      { code: 'vi', name: 'Tiếng Việt', file: 'vi.json' },
      { code: 'en', name: 'English', file: 'en.json' }
    ],
    defaultLocale: process.env.APP_LOCALE || 'vi',
    langDir: '../app/locales/',
    strategy: 'no_prefix',
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'i18n_redirected',
      redirectOn: 'root'
    }
  },
  app: {
    head: {
      title: "TechMart",
      meta: [{ name: "description", content: "Techmart - Công nghệ tiên tiến, giá cả hợp lý" }],
      link: [
        { rel: "icon", type: "image/x-icon", href: "/logo-favicon.png" },
        { rel: "stylesheet", href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" },
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        { rel: "preconnect", href: "https://fonts.gstatic.com" },
        { rel: "stylesheet", href: "https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" },
      ],
    },
  },
  nitro: {
    compressPublicAssets: true,
  },
  experimental: {
    payloadExtraction: false,
  },
  routeRules: {
    "/admin/**": { ssr: false },
    "/**": { ssr: true },
  },
  debug: true,
});
