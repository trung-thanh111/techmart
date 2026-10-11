import { resolve } from "path";
import { defineNuxtConfig } from "nuxt/config";
import Aura from "@primevue/themes/aura";

export default defineNuxtConfig({
  future: {
    compatibilityVersion: 4,
  },
  compatibilityDate: "2025-08-23",
  srcDir: "app/",

  modules: ["@nuxtjs/tailwindcss", "@primevue/nuxt-module", "@nuxt/image", "@pinia/nuxt", "pinia-plugin-persistedstate/nuxt", "@nuxtjs/i18n", "nuxt-easy-lightbox"],

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

  devtools: {
    enabled: false,
  },

  image: {
    domains: ["cdn2.cellphones.com.vn", "cellphones.com.vn", "ui-avatars.com"],
  },

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
      { code: "vi", name: "Tiếng Việt", file: "vi.json" },
      { code: "en", name: "English", file: "en.json" },
    ],
    defaultLocale: "vi",
    fallbackLocale: "vi",
    langDir: "../app/locales/",
    strategy: "no_prefix",
    detectBrowserLanguage: false,
  },

  app: {
    head: {
      title: "TechMart",
      meta: [
        { name: "description", content: "Techmart - Công nghệ tiên tiến, giá cả hợp lý" },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
      ],
      link: [
        { rel: "icon", type: "image/x-icon", href: "/logo-favicon.png" },
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        { rel: "preconnect", href: "https://fonts.gstatic.com", crossorigin: "" },
        { rel: "stylesheet", href: "https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" },
        { rel: "stylesheet", href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css", media: "print", onload: "this.media='all'" },
      ],
    },
  },

  nitro: {
    compressPublicAssets: true,
    storage: {
      redis: {
        driver: "memory",
      },
    },
    hooks: {
      compiled: async (nitro) => {
        const fs = await import("node:fs");
        const path = await import("node:path");
        const src = path.resolve("node_modules/@img");
        const dest = path.join(nitro.options.output.serverDir, "node_modules/@img");
        if (fs.existsSync(src)) {
          fs.mkdirSync(path.dirname(dest), { recursive: true });
          fs.cpSync(src, dest, { recursive: true });
        }
      },
    },
  },

  experimental: {
    payloadExtraction: false,
    viewTransition: false,
    asyncContext: true,
  },

  routeRules: {
    "/**": { ssr: true },
  },

  vite: {
    server: {
      hmr: {
        overlay: false,
        port: 24678,
      },
      watch: {
        usePolling: false,
        ignored: ["**/node_modules/**", "**/.git/**", "**/dist/**", "**/.nuxt/**", "**/public/**"],
      },
    },
    warmup: {
      clientFiles: ["./app/components/**/*.vue", "./app/layouts/**/*.vue", "./app/pages/(client)/**/*.vue"],
    },
    optimizeDeps: {
      include: ["vue", "vue-router", "@vueuse/core", "pinia", "primevue/button", "primevue/datatable", "primevue/inputtext", "primevue/dialog", "primevue/dropdown", "primevue/calendar", "primevue/fileupload", "primevue/paginator", "primevue/toast", "@splidejs/vue-splide", "zod", "vee-validate", "fuzzysort", "extend"],
      exclude: ["ckeditor5", "@ckeditor/ckeditor5-vue", "@nuxt/devtools-kit"],
    },
    esbuild: {
      target: "esnext",
      treeShaking: true,
    },
    css: {
      devSourcemap: false,
    },
    build: {
      target: "esnext",
      chunkSizeWarningLimit: 1000,
    },
  },
  // debug: true,

  // === BUILD OPTIMIZATION ===
  build: {
    transpile: ["primevue"],
  },

  // === TYPESCRIPT - Tắt checking trong dev ===
  typescript: {
    typeCheck: false,
  },


  // === TELEMETRY - Tắt ===
  telemetry: false,
});
