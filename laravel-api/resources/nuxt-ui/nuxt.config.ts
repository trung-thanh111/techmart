import { resolve } from "path";
import { defineNuxtConfig } from "nuxt/config";
import Aura from "@primevue/themes/aura";

export default defineNuxtConfig({
    app: {
        head: {
            title: "TechMart quản trị",
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
        },
    },

    srcDir: "app/",

    devServer: {
        port: 3030,
    },

    sourcemap: {
        // Tắt hoàn toàn source maps trong production
        server: false,
        client: false,
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

    image: {
        quality: 80,
        format: ["webp", "avif", "png", "jpg"],
        screens: {
            xs: 320,
            sm: 640,
            md: 768,
            lg: 1024,
            xl: 1280,
            xxl: 1536,
        },
    },

    alias: {
        "@": resolve(__dirname, "./app"),
        "~": resolve(__dirname, "./app"),
    },

    runtimeConfig: {
        public: {
            baseUrl: process.env.BASE_URL || "http://localhost:8000",
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
        output: {
            dir: resolve(__dirname, "../../public/nuxt-ui/.output"),
            publicDir: resolve(__dirname, "../../public/nuxt-ui"),
        },
        compressPublicAssets: true,
        storage: {
            redis: {
                driver: "memory",
            },
        },
        preset: "static",
    },

    experimental: {
        payloadExtraction: false,
        viewTransition: false,
        asyncContext: true,
    },

    ssr: false,

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
            rollupOptions: {
                output: {
                    manualChunks: {
                        primevue: ["primevue"],
                        vendor: ["vue", "vue-router", "pinia"],
                    },
                },
            },
        },
        resolve: {
            alias: {
                debug: "debug/src/browser.js",
            },
        },
    },

    debug: true,

    build: {
        transpile: ["primevue"],
    },

    typescript: {
        typeCheck: false,
    },

    generate: {
        fallback: "200.html",
        routes: ["/"],
    },

    telemetry: false,
});
