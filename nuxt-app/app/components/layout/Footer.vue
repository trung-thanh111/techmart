<template>
  <div>
    <footer class="bg-gradient-to-r from-[#00b88c] to-[#007c8b] px-2 sm:px-4 py-4 text-white">
      <div class="max-w-7xl mx-auto pb-4">
        <div class="py-2 md:py-4 flex flex-col items-start">
          <nuxtImg src="/images/logo/logo-website.png" alt="logo footer" class="h-10 md:h-12 mb-2" format="webp" sizes="120px md:150px" loading="lazy" />
        </div>
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-4 py-5">
          <div class="space-y-2 text-sm">
            <h3 class="font-bold uppercase mb-2 text-base">{{ $t("footer.supportCustomer") }}</h3>
            <p>
              {{ $t("footer.hotline") }}:
              <a :href="`tel:${$t('footer.hotlineNumber')}`" class="!text-red-500 font-semibold hover:text-gray-300 underline underline-offset-2">{{ $t("footer.hotlineNumber") }}</a>
            </p>
            <p>
              {{ $t("footer.complaints") }}:
              <a :href="`tel:${$t('footer.complaintsNumber')}`" class="hover:text-gray-300 underline underline-offset-2">{{ $t("footer.complaintsNumber") }}</a>
            </p>
            <p>
              {{ $t("footer.email") }}:
              <a :href="`mailto:${$t('footer.emailAddress')}`" class="hover:text-gray-300 underline underline-offset-2">{{ $t("footer.emailAddress") }}</a>
            </p>
            <p>
              {{ $t("footer.serviceTime") }}:
              <span class="font-bold">{{ $t("footer.always") }}</span>
            </p>
          </div>

          <!-- About -->
          <div>
            <h3 class="font-bold uppercase mb-2">{{ $t("footer.aboutUs") }}</h3>
            <ul class="space-y-2">
              <li v-for="item in aboutLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm">
                  {{ $t(item.text) }}
                </a>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold uppercase mb-2">{{ $t("footer.customerCare") }}</h3>
            <ul class="space-y-2">
              <li v-for="item in supportLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm">
                  {{ $t(item.text) }}
                </a>
              </li>
            </ul>
          </div>
          <div class="space-y-2">
            <div class="mb-4">
              <h3 class="font-bold uppercase mb-2">{{ $t("footer.paymentMethods") }}</h3>
              <div class="flex flex-wrap gap-2">
                <nuxtImg v-for="(method, index) in paymentMethods" :key="index" :src="method.src" :alt="method.alt" class="h-12 w-12 bg-white rounded-md object-contain p-1 hover:bg-gray-300 transition hover:scale-95" format="webp" sizes="48px" loading="lazy" />
              </div>
            </div>

            <div>
              <h3 class="font-bold uppercase mb-2">{{ $t("footer.downloadNow") }}</h3>
              <div class="flex gap-2 items-center">
                <a href="#">
                  <nuxtImg src="/images/logo/downloadANDROID.webp" alt="android" class="h-10 w-auto" format="webp" sizes="160px" loading="lazy" />
                </a>
                <a href="#">
                  <nuxtImg src="/images/logo/downloadIOS.webp" alt="ios" class="h-10 w-auto" format="webp" sizes="160px" loading="lazy" />
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent mb-2"></div>
        <div class="border-t border-white/30 text-xs sm:text-sm py-2 text-left">
          <p class="mb-2 font-medium">
            {{ $t("footer.copyright.owner") }}
            <br />
            {{ $t("footer.copyright.text") }}
          </p>
          <p class="mb-2">{{ $t("footer.copyright.contactMe") }}</p>
          <div class="flex justify-start items-center gap-4 mt-2">
            <a href="#" class="text-white text-xl md:text-2xl hover:text-blue-400"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white text-xl md:text-2xl hover:text-red-500"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </footer>
    <UtilityTool />
  </div>
</template>

<script setup>
import UtilityTool from "~/components/navigation/UtilityTool.vue";

const { t } = useI18n();

const aboutLinks = [
  { text: "footer.aboutLinks.about", href: "#" },
  { text: "footer.aboutLinks.sitemap", href: "#" },
  { text: "footer.aboutLinks.promotion", href: "#" },
  { text: "footer.aboutLinks.handbook", href: "#" },
  { text: "footer.aboutLinks.privacy", href: "#" },
  { text: "footer.aboutLinks.terms", href: "#" },
  { text: "footer.aboutLinks.complaintPolicy", href: "#" },
];

const supportLinks = [
  { text: "footer.supportLinks.guide", href: "#" },
  { text: "footer.supportLinks.shipping", href: "#" },
  { text: "footer.supportLinks.payment", href: "#" },
  { text: "footer.supportLinks.exchange", href: "#" },
  { text: "footer.supportLinks.warranty", href: "#" },
  { text: "footer.supportLinks.cancel", href: "#" },
  { text: "footer.supportLinks.faq", href: "#" },
];

const paymentMethods = [
  { src: "/images/logo/momo.svg", alt: "Momo", loading: "lazy" },
  { src: "/images/logo/vnpay.webp", alt: "VNPAY", loading: "lazy" },
  { src: "/images/logo/credit-card.png", alt: "Credit Card", loading: "lazy" },
  { src: "/images/logo/cod.png", alt: "COD", loading: "lazy" },
];
</script>
