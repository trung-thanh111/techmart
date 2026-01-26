import { resolve } from "path";
import { defineNuxtConfig } from "nuxt/config";
import Aura from "@primevue/themes/aura";

export default defineNuxtConfig({
    srcDir: "app/",

    devServer: {
        port: 3030,
    },

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

    devtools: {
        enabled: false,
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
        defaultLocale: process.env.APP_LOCALE || "vi",
        langDir: "../app/locales/",
        strategy: "no_prefix",
        detectBrowserLanguage: {
            useCookie: true,
            cookieKey: "i18n_redirected",
            redirectOn: "root",
        },
    },

    nitro: {
        compressPublicAssets: true,
        storage: {
            redis: {
                driver: "memory",
            },
        },
    },

    experimental: {
        payloadExtraction: false,
        viewTransition: false,
        asyncContext: true,
    },

    routeRules: {
        "/**": { ssr: false },
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
