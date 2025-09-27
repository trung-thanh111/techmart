<template>
  <div>
    <footer class="bg-gradient-to-r from-[#00b88c] to-[#007c8b] px-2 sm:px-4 py-4 text-white">
      <div class="max-w-7xl mx-auto pb-4">
        <div class="py-2 md:py-4 flex flex-col items-start">
          <nuxtImg src="/shared/images/logo/logo-website.png" alt="logo footer" class="h-10 md:h-12 mb-2" format="webp" sizes="120px md:150px" loading="lazy" />
        </div>
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-4 py-5">
          <div class="space-y-2">
            <h3 class="font-bold uppercase mb-2">{{ $t('client.footer.supportCustomer') }}</h3>
            <p>
              {{ $t('client.footer.hotline') }}:
              <a href="tel:0812720008" class="!text-red-500 font-semibold hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">0812720008</a>
            </p>
            <p>
              {{ $t('client.footer.complaints') }}:
              <a href="tel:0812720008" class="hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">0812720008</a>
            </p>
            <p>
              {{ $t('client.footer.email') }}:
              <a href="mailto:trungptdev2004@gmail.com" class="hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">trungptdev2004@gmail.com</a>
            </p>
            <p>
              {{ $t('client.footer.serviceTime') }}:
              <span class="font-bold">{{ $t('client.footer.always') }}</span>
            </p>
          </div>

          <!-- About -->
          <div>
            <h3 class="font-bold uppercase mb-2">{{ $t('client.footer.aboutUs') }}</h3>
            <ul class="space-y-2">
              <li v-for="item in aboutLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">
                  {{ $t(item.text) }}
                </a>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold uppercase mb-2">{{ $t('client.footer.customerCare') }}</h3>
            <ul class="space-y-2">
              <li v-for="item in supportLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">
                  {{ $t(item.text) }}
                </a>
              </li>
            </ul>
          </div>
          <div class="space-y-2">
            <div class="mb-4">
              <h3 class="font-bold uppercase mb-2">{{ $t('client.footer.paymentMethods') }}</h3>
              <div class="flex flex-wrap gap-2">
                <nuxtImg v-for="(method, index) in paymentMethods" :key="index" :src="method.src" :alt="method.alt" class="h-12 w-12 bg-white rounded-md object-contain p-1 hover:bg-gray-300 transition hover:scale-95" format="webp" sizes="48px" loading="lazy" />
              </div>
            </div>

            <div>
              <h3 class="font-bold uppercase mb-2">{{ $t('client.footer.downloadNow') }}</h3>
              <div class="flex gap-2 items-center">
                <a href="#">
                  <nuxtImg src="/shared/images/logo/downloadANDROID.webp" alt="android" class="h-10 w-auto" format="webp" sizes="160px" loading="lazy" />
                </a>
                <a href="#">
                  <nuxtImg src="/shared/images/logo/downloadIOS.webp" alt="ios" class="h-10 w-auto" format="webp" sizes="160px" loading="lazy" />
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/30 to-transparent mb-2"></div>
        <div class="border-t border-white/30 text-xs sm:text-sm py-2 text-left">
          <p class="mb-2 font-medium">
            {{ $t('client.footer.copyright.owner') }}
            <br />
            {{ $t('client.footer.copyright.text') }}
          </p>
          <p class="mb-2">{{ $t('client.footer.copyright.contactMe') }}</p>
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
import UtilityTool from "~/components/client/navigation/UtilityTool.vue";

const aboutLinks = [
  { text: "client.footer.aboutLinks.about", href: "#" },
  { text: "client.footer.aboutLinks.sitemap", href: "#" },
  { text: "client.footer.aboutLinks.promotion", href: "#" },
  { text: "client.footer.aboutLinks.handbook", href: "#" },
  { text: "client.footer.aboutLinks.privacy", href: "#" },
  { text: "client.footer.aboutLinks.terms", href: "#" },
  { text: "client.footer.aboutLinks.complaintPolicy", href: "#" },
];

const supportLinks = [
  { text: "client.footer.supportLinks.guide", href: "#" },
  { text: "client.footer.supportLinks.shipping", href: "#" },
  { text: "client.footer.supportLinks.payment", href: "#" },
  { text: "client.footer.supportLinks.exchange", href: "#" },
  { text: "client.footer.supportLinks.warranty", href: "#" },
  { text: "client.footer.supportLinks.cancel", href: "#" },
  { text: "client.footer.supportLinks.faq", href: "#" },
];

const paymentMethods = [
  { src: "/shared/images/logo/momo.svg", alt: "Momo" },
  { src: "/shared/images/logo/vnpay.webp", alt: "VNPAY" },
  { src: "/shared/images/logo/credit-card.png", alt: "Credit Card" },
  { src: "/shared/images/logo/cod.png", alt: "COD" },
];
</script>
