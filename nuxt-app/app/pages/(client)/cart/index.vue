<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- Breadcrumb  -->
    <Breadcrumb />
    <!-- bnner slide  -->
    <section>
      <article>
        <div class="container mx-auto">
          <div class="grid grid-cols-12 gap-6 mb-5">
            <div class="col-span-12 lg:col-span-8">
              <div class="w-full overflow-x-auto scrollbar-hide">
                <table class="min-w-[700px] w-full text-gray-700 table-fixed bg-white rounded shadow-sm">
                  <thead>
                    <tr class="border-b border-gray-200 md:text-base text-sm text-left">
                      <th class="p-2 md:p-4 w-13 text-center">
                        <label class="relative flex items-center justify-center cursor-pointer">
                          <input type="checkbox" checked class="peer h-4 w-4 md:h-5 md:w-5 appearance-none rounded border border-slate-300 checked:bg-teal-500 checked:border-teal-500" />
                          <span class="absolute inset-0 flex items-center justify-center pointer-events-none text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </label>
                      </th>
                      <th class="w-[58%] px-1 md:px-2">Sản phẩm</th>
                      <th class="w-[14%] px-1 md:px-2 text-center">Đơn giá</th>
                      <th class="w-[12%] px-1 md:px-2 text-center">Số lượng</th>
                      <th class="w-[14%] px-1 md:px-2 text-center">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Duyệt sản phẩm -->
                    <tr v-for="(cart, i) in cartList" :key="i" class="hover:bg-[#f1f1f1]">
                      <td class="p-2 md:p-4 text-center">
                        <label class="relative flex items-center justify-center cursor-pointer">
                          <input type="checkbox" checked class="peer h-4 w-4 md:h-5 md:w-5 appearance-none rounded border border-slate-300 checked:bg-teal-500 checked:border-teal-500" />
                          <span class="absolute inset-0 flex items-center justify-center pointer-events-none text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </label>
                      </td>
                      <td class="py-2 md:py-4">
                        <div class="flex gap-2 md:gap-4 min-w-0">
                          <img src="/public/images/products/iphone-16-pro-max.webp" alt="áo khoác" class="w-14 h-14 md:w-20 md:h-20 object-cover rounded max-w-full" />
                          <div class="min-w-0">
                            <p class="font-semibold leading-snug mb-1 line-clamp-2 truncate text-xs md:text-base">Áo khoác bomber unisex phong cách stkhoác bomber unisex phong cách stkhoác bomber unisex phong cách streetwear - Màu đen, M</p>
                            <p class="text-xs text-gray-500 truncate">M, &nbsp; Màu đen</p>
                            <a href="#" class="text-red-500 text-xs inline-flex items-center px-1 py-0.5 gap-1 rounded-md hover:bg-red-100">
                              <i class="fa-solid fa-trash text-xs md:text-base"></i>
                              xóa
                            </a>
                          </div>
                        </div>
                      </td>
                      <td class="text-center align-middle whitespace-nowrap text-xs md:text-base">23.500.000đ</td>
                      <td class="text-center align-middle">
                        <div class="flex items-center justify-between rounded overflow-hidden border border-gray-200 p-1 shadow-sm min-w-0 gap-1">
                          <button type="button" title="quantity-decrease" @click="decreaseQty(i)" class="bg-teal-500 text-white w-8 h-6 md:w-8 md:h-7 flex items-center justify-center hover:bg-teal-600">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                              <path d="M3.75 9H14.25"></path>
                            </svg>
                          </button>
                          <div class="w-7 h-6 md:w-8 md:h-7 flex items-center justify-center bg-white">
                            <input type="text" :value="cart.qty" readonly class="text-center font-medium text-gray-800 text-xs md:text-base focus:outline-none max-w-full bg-white" inputmode="numeric" />
                          </div>
                          <button type="button" title="quantity-increase" @click="increaseQty(i)" class="bg-teal-500 text-white w-8 h-6 md:w-8 md:h-7 flex items-center justify-center hover:bg-teal-600">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                              <path d="M9 3.75V14.25"></path>
                              <path d="M3.75 9H14.25"></path>
                            </svg>
                          </button>
                        </div>
                      </td>
                      <td class="text-center align-middle font-medium whitespace-nowrap text-xs md:text-base">112.500.000đ</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Cart Actions -->
              <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4">
                  <div class="flex justify-between items-center gap-2 md:gap-4">
                    <a href="/shop" class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-1 md:py-2 text-xs md:text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-sm transition-colors duration-200 group">
                      <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                      </svg>
                      <span>Mua sắm</span>
                    </a>
                    <button type="button" @click="clearCart" class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-1 md:py-2 text-xs md:text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-sm transition-all duration-200 transform hover:scale-105 shadow-sm">
                      <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a2 2 0 012 2v0H7v0a2 2 0 012-2z" />
                      </svg>
                      <span>Xóa giỏ hàng</span>
                    </button>
                  </div>
                  <div class="mt-2 md:mt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-1 md:gap-2 text-xs text-gray-500">
                      <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Giỏ hàng sẽ được lưu trong 30 ngày</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-span-12 lg:col-span-4">
              <div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-3">
                  <div class="flex items-center justify-between">
                    <h2 class="text-white text-base md:text-lg font-bold">Đơn hàng</h2>
                    <div class="flex items-center gap-2">
                      <svg class="md:w-5 md:h-5 w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                      </svg>
                      <span class="text-white/80 md:text-sm text-xs">1 sản phẩm</span>
                    </div>
                  </div>
                </div>
                <div class="p-4">
                  <div class="space-y-3">
                    <div v-for="(product_orders, id) in 2" :key="id" class="flex items-start gap-4 bg-gray-50 border border-gray-100 hover:border-sky-500 rounded-sm">
                      <div class="relative">
                        <img src="/public/images/products/iphone-16-pro-max-3.webp" alt="Sản phẩm" class="w-16 h-16 object-cover rounded-lg" />
                        <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">1</div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="md:text-sm text-xs font-semibold text-gray-900 line-clamp-2 mb-1">Áo khoác bomber unisex phong cổ suông mát mẻ</h3>
                        <p class="text-xs text-gray-500 mb-1">M, Màu đen</p>
                        <p class="md:text-sm text-xs font-medium text-gray-900">120.000.000đ</p>
                      </div>
                      <button type="button" class="text-gray-400 hover:text-red-500 transition-colors md:p-1 p-0.5">
                        <svg class="md:w-4 md:h-4 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a2 2 0 012 2v0H7v0a2 2 0 012-2z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="mt-5 p-2 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-sm">
                    <div class="flex items-center gap-2 mb-3">
                      <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                      </svg>
                      <h3 class="text-sm font-semibold text-amber-800">Mã giảm giá</h3>
                    </div>
                    <div class="flex gap-2">
                      <input type="text" placeholder="Nhập mã voucher..." class="flex-1 text-sm md:px-3 px-2 md:py-2 py-1.5 border border-amber-300 rounded-xs focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-transparent" />
                      <button class="md:px-4 px-3 md:py-2 py-1.5 bg-amber-600 hover:bg-amber-700 text-white md:text-sm text-xs font-medium rounded-xs transition-colors">Áp dụng</button>
                    </div>
                  </div>
                  <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">Thành tiền:</span>
                      <span class="font-medium">500.000đ</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">Giảm giá:</span>
                      <span class="text-green-600 font-medium">− 0đ</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">Phí vận chuyển:</span>
                      <span class="font-medium">25.000đ</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">Thuế (5%):</span>
                      <span class="font-medium">10.000đ</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 mt-4">
                      <div class="flex items-center justify-between">
                        <span class="md:text-lg text-base font-bold text-gray-900">Tổng tiền:</span>
                        <span class="md:text-xl text-lg font-bold text-red-600">525.000đ</span>
                      </div>
                      <p class="text-xs text-gray-500 mt-1">Đã bao gồm thuế VAT</p>
                    </div>
                  </div>
                  <div class="mt-6">
                    <button class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white py-2 px-4 rounded-sm font-semibold text-base shadow">
                      <div class="flex items-center justify-center gap-2 uppercase md:text-base text-sm">TIẾN HÀNH THANH TOÁN</div>
                    </button>
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                      Thanh toán an toàn với SSL
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";

// Giả lập 3 sản hẩm, mỗi sản phẩm có số lượng riêng
const cartList = ref([{ qty: 1 }, { qty: 1 }, { qty: 1 }]);

function increaseQty(idx) {
  cartList.value[idx].qty++;
}
function decreaseQty(idx) {
  if (cartList.value[idx].qty > 1) cartList.value[idx].qty--;
}
</script>
