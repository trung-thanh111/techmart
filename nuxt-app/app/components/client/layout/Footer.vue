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
            <h3 class="font-bold uppercase mb-2">Hỗ trợ khách hàng</h3>
            <p>
              Hotline:
              <a href="tel:0812720008" class="!text-red-500 font-semibold hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">0812720008</a>
            </p>
            <p>
              Kiếu nại, góp ý:
              <a href="tel:0812720008" class="hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">0812720008</a>
            </p>
            <p>
              Email:
              <a href="mailto:trungptdev2004@gmail.com" class="hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">trungptdev2004@gmail.com</a>
            </p>
            <p>
              Thời gian phục vụ:
              <span class="font-bold">Mọi lúc</span>
            </p>
          </div>

          <!-- About -->
          <div>
            <h3 class="font-bold uppercase mb-2">Về Chúng tôi</h3>
            <ul class="space-y-2">
              <li v-for="item in aboutLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">
                  {{ item.text }}
                </a>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold uppercase mb-2">Chăm sóc khách hàng</h3>
            <ul class="space-y-2">
              <li v-for="item in supportLinks" :key="item.text">
                <a :href="item.href" class="font-normal hover:text-gray-300 underline underline-offset-2 text-sm md:text-base">
                  {{ item.text }}
                </a>
              </li>
            </ul>
          </div>
          <div class="space-y-2">
            <div class="mb-4">
              <h3 class="font-bold uppercase mb-2">Phương thức thanh toán</h3>
              <div class="flex flex-wrap gap-2">
                <nuxtImg v-for="(method, index) in paymentMethods" :key="index" :src="method.src" :alt="method.alt" class="h-12 w-12 bg-white rounded-md object-contain p-1 hover:bg-gray-300 transition hover:scale-95" format="webp" sizes="48px" loading="lazy" />
              </div>
            </div>

            <div>
              <h3 class="font-bold uppercase mb-2">Tải ngay</h3>
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
            Pham Thanh Trung
            <br />
            ©2025 - Bản quyền thanhtrung
          </p>
          <p class="mb-2">Lãy liên hệ với tôi thông qua nền tảng bên dưới</p>
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
  { text: "Giới thiệu", href: "#" },
  { text: "Site map", href: "#" },
  { text: "Khuyến mãi", href: "#" },
  { text: "Cẩm nang", href: "#" },
  { text: "Chính sách bảo mật", href: "#" },
  { text: "Điều khoản sử dụng", href: "#" },
  { text: "Chính sách giải quyết khiếu nại", href: "#" },
];

const supportLinks = [
  { text: "Hướng dẫn đặt hàng", href: "#" },
  { text: "Chính sách vận chuyển", href: "#" },
  { text: "Chính sách thanh toán", href: "#" },
  { text: "Chính sách đổi hàng", href: "#" },
  { text: "Chính sách bảo hành", href: "#" },
  { text: "Chính sách huỷ đơn", href: "#" },
  { text: "Những câu hỏi thường gặp", href: "#" },
];

const paymentMethods = [
  { src: "/shared/images/logo/momo.svg", alt: "Momo" },
  { src: "/shared/images/logo/vnpay.webp", alt: "VNPAY" },
  { src: "/shared/images/logo/credit-card.png", alt: "Credit Card" },
  { src: "/shared/images/logo/cod.png", alt: "COD" },
];
</script>
